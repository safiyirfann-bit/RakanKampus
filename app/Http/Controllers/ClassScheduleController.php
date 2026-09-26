<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = $request->user()->classSchedules()
            ->get()
            ->sortBy(fn (ClassSchedule $s) => array_search($s->day_of_week, ClassSchedule::DAYS) * 1440 + $this->toMinutes($s->start_time))
            ->values()
            ->map(fn (ClassSchedule $s) => $this->toRaw($s));

        return view('timetable', [
            'user' => $request->user(),
            'schedules' => $schedules,
            'days' => ClassSchedule::DAYS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateSchedule($request);

        $schedule = $request->user()->classSchedules()->create($data);

        return response()->json(['success' => true, 'schedule' => $this->toRaw($schedule)]);
    }

    public function update(Request $request, ClassSchedule $schedule)
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);

        $data = $this->validateSchedule($request);

        $schedule->update($data);

        return response()->json(['success' => true, 'schedule' => $this->toRaw($schedule)]);
    }

    public function destroy(Request $request, ClassSchedule $schedule)
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);

        $schedule->delete();

        return response()->json(['success' => true]);
    }

    public function destroyAll(Request $request)
    {
        $request->user()->classSchedules()->delete();

        return response()->json(['success' => true]);
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $request->user()->classSchedules()
            ->whereIn('id', $data['ids'])
            ->delete();

        return response()->json(['success' => true]);
    }

    public function aiCapture(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:6144', // 6MB
        ]);

        $file = $request->file('photo');
        $dataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

        // Malaysian polytechnic weekly timetables are Isnin-Jumaat only in
        // practice (this is also true of the real timetable this was tested
        // against) — leaving Saturday/Sunday out of what's asked for keeps the
        // response smaller. This only affects AI capture; both days are still
        // selectable when adding a class by hand, in case that's ever wrong
        // for someone.
        $days = array_values(array_diff(ClassSchedule::DAYS, ['Saturday', 'Sunday']));

        // ONE Groq call for the whole week, sending the photo only once — not one
        // call per day. Real production logs from this account's actual Groq plan
        // showed why that didn't work: the plan caps this vision model at only
        // 1000 OUTPUT tokens/minute AND only 7000 INPUT tokens/minute, and every
        // per-day call re-sends the full image, which alone costs roughly
        // 2500-2900 input tokens. Even switching from concurrent to one-at-a-time
        // calls (a prior attempt at fixing this) still burned through the INPUT
        // budget after just two or three calls, on top of the output-budget
        // problem concurrency hit first — there is no way to fit 5+ image-bearing
        // calls into one minute on this plan. So this goes back to a single call,
        // and leans on the model self-reporting which row it actually read for
        // each day (see the schema in visionPayload()) so a day whose content
        // came from the wrong row can still be caught and discarded below rather
        // than trusted, instead of relying on splitting the days apart to
        // prevent that in the first place.
        $response = Http::withToken(config('services.groq.key'))
            ->timeout(110)
            ->post('https://api.groq.com/openai/v1/chat/completions', $this->visionPayload($dataUri, $days));

        if (! $response || $response->failed()) {
            Log::error('Groq vision API error (timetable)', [
                'status' => $response?->status(),
                'body' => $response?->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'AI tak dapat proses gambar tu sekarang. Cuba lagi sekejap.',
            ], 422);
        }

        $rawContent = $response->json('choices.0.message.content');
        $finishReason = $response->json('choices.0.finish_reason');

        // Temporary: log the raw model reply (truncated) so we can see exactly what
        // Groq returned if the extraction still looks wrong for a tricky timetable photo.
        // Safe to remove once the vision extraction is confirmed reliable in production.
        Log::info('Groq vision raw response (timetable)', [
            'finish_reason' => $finishReason,
            'raw' => Str::limit((string) $rawContent, 4000),
        ]);

        if ($finishReason === 'length') {
            // The model ran out of its token budget mid-answer — what we have is
            // likely truncated JSON that will fail to parse below. Flagged loudly
            // here so this is diagnosable from the logs instead of only from a
            // user screenshot next time.
            Log::warning('Groq vision response for timetable was cut off by max_completion_tokens');
        }

        $parsed = $this->extractJson($rawContent);
        $dayResults = is_array($parsed['days'] ?? null) ? $parsed['days'] : [];

        $classes = [];

        foreach ($days as $day) {
            $dayResult = $dayResults[$day] ?? null;
            $dayClasses = is_array($dayResult) ? ($dayResult['classes'] ?? null) : null;

            if (! is_array($dayResult) || empty($dayResult['detected']) || ! is_array($dayClasses)) {
                continue;
            }

            // Cross-check the model's own account of which row it read against the
            // day this entry is supposed to be. A dense/crowded table can make the
            // model drift onto a neighbouring row while still answering under the
            // label it was asked about — this is exactly what produced a wrong
            // day's class showing up under the wrong day in a real test. Trusting
            // content whose self-reported row doesn't match is worse than showing
            // nothing for that day, so it's discarded here rather than saved.
            $rowLabelFound = $this->normalizeDayLabel($dayResult['row_label_found'] ?? null);
            $expectedLabels = [$this->normalizeDayLabel($this->localDayLabel($day)), $this->normalizeDayLabel($day)];

            if ($rowLabelFound !== '' && ! in_array($rowLabelFound, $expectedLabels, true)) {
                Log::warning('Timetable AI capture: discarding a day whose row label didn\'t match', [
                    'day' => $day,
                    'expected' => $expectedLabels,
                    'row_label_found' => $rowLabelFound,
                ]);
                continue;
            }

            foreach ($dayClasses as $entry) {
                $entry['day_of_week'] = $day;
                $classes[] = $entry;
            }
        }

        if (count($classes) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Tak dapat kesan jadual kelas dalam gambar tu. Cuba gambar yang lebih jelas, atau isi manual.',
            ], 422);
        }

        $classes = $this->mergeAdjacentDuplicates($classes);

        // Sanity check independent of whether the model followed the prompt: a real weekly
        // timetable never has two classes overlapping in time on the same day. If the
        // extraction produced overlapping entries, that's a strong signal the model got
        // confused reading the grid (this is exactly what happened with a fabricated,
        // sliding-window result on a dense timetable) — refuse the whole batch rather than
        // saving a pile of conflicting junk the user then has to clean up by hand.
        if ($this->hasOverlaps($classes)) {
            Log::warning('Timetable AI capture rejected: overlapping classes detected', [
                'classes' => $classes,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'AI kurang yakin baca jadual ni betul-betul (jumpa kelas yang bertindih masa). Cuba gambar yang lebih jelas/dekat, atau isi manual untuk elak data salah.',
            ], 422);
        }

        $created = [];

        foreach ($classes as $entry) {
            $day = $entry['day_of_week'] ?? null;
            $start = $entry['start_time'] ?? null;
            $end = $entry['end_time'] ?? null;
            $subject = trim((string) ($entry['subject'] ?? ''));

            if (! in_array($day, ClassSchedule::DAYS, true) || ! $this->isValidTime($start) || ! $this->isValidTime($end) || $subject === '') {
                continue; // skip anything the model couldn't fill in cleanly rather than saving junk
            }

            $schedule = $request->user()->classSchedules()->create([
                'subject' => Str::limit($subject, 150, ''),
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'room' => $entry['room'] ?? null,
                'lecturer' => $entry['lecturer'] ?? null,
            ]);

            $created[] = $this->toRaw($schedule);
        }

        if (count($created) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Tak dapat kesan jadual kelas dalam gambar tu. Cuba gambar yang lebih jelas, atau isi manual.',
            ], 422);
        }

        return response()->json(['success' => true, 'schedules' => $created]);
    }

    /**
     * The Groq request payload for extracting the whole week out of the
     * timetable photo in a single call. $days is the list of English day
     * names to extract (Saturday/Sunday already excluded by the caller).
     */
    private function visionPayload(string $dataUri, array $days): array
    {
        $dayList = collect($days)
            ->map(fn (string $d) => "\"{$d}\" ({$this->localDayLabel($d)})")
            ->implode(', ');

        $exampleDay = $days[0] ?? 'Monday';

        $systemPrompt = 'You extract a weekly class timetable from a single photo for a Malaysian polytechnic student app. '
            . "The photo may be in English or Bahasa Melayu (e.g. Isnin=Monday, Selasa=Tuesday, Rabu=Wednesday, Khamis=Thursday, Jumaat=Friday). "
            . "Extract exactly these days, each as its own entry keyed by its English name: {$dayList}. "
            . 'Reply with ONLY a single JSON object, no markdown, no code fences, no explanation, in exactly this shape: '
            . '{"days": {"' . $exampleDay . '": {"row_label_found": string|null, "detected": true|false, "classes": [{"subject": string, "start_time": "HH:MM", "end_time": "HH:MM", "room": string|null, "lecturer": string|null}]}, ... one such entry per day listed above, using exactly its English name as the key}}. '
            . '"row_label_found" is the day-label text exactly as printed in the leftmost day/HARI column next to the row you read FOR THAT DAY (e.g. "ISNIN") — this is how a second automated check confirms you read the right row for each day, so it must reflect the REAL row you read, not just repeat the day key. Set it to null only if you could not locate that day\'s row at all. '
            . 'Use 24-hour HH:MM for times. Set detected=false and classes=[] for a day with no classes, or if the image is not a class timetable at all. '
            . 'The timetable may be a dense grid table where DAYS are ROWS and TIME SLOTS are COLUMNS (not the other way around). Process the days ONE AT A TIME, in order: for each day, first find its row by its day label, read only along that one row, and fully finish and double-check it before starting the next day — do not read across rows at once or let one row\'s content leak into another\'s answer. '
            . 'A single class often SPANS MULTIPLE adjacent time-slot columns/cells (a merged cell) — treat that whole span as ONE class entry with the correct combined start_time and end_time, not one duplicate entry per column it touches. Read the start_time and end_time directly off the column headers the cell spans — do not shift or guess the hour. '
            . 'A span only continues for as long as the SAME course code is printed in each column it covers — the instant the code changes to a different one, or a column is blank, that class has ENDED there. Never stretch a class\'s end_time past the last column where its own code is actually printed, and never let it swallow a different class\'s columns into the same entry just because they sit next to each other. '
            . 'There may be a separate legend/key table (e.g. course code -> full course name) elsewhere in the image — read that legend FIRST and use it to resolve abbreviated course codes into a readable subject name. '
            . 'For each occupied cell, read the exact text in it (course code, lecturer initials/name, room) before deciding the subject/time/room/lecturer values — do not skip straight to an answer. '
            . 'Read every value directly from the cell, even if it looks identical to a room/lecturer/subject you would expect to repeat elsewhere on the same row or a different day — re-read the actual cell rather than reusing a value from memory or pattern. '
            . 'Before finalizing each day, double-check: trace that day\'s row back to the leftmost column and confirm its label really matches the day you\'re filling in — if it turns out you actually read a different day\'s row, or a busy/crowded row was hard to fully segment, set detected=false and classes=[] for THAT DAY rather than submit a guess or another day\'s content under it. '
            . 'CRITICAL — do not hallucinate: every subject, room and lecturer you output MUST be text you can actually point to in that row (directly, or via the legend table). Never output a generic-sounding subject name unless it literally appears in the image or legend. If a cell/subject/time is blurry, cut off, or ambiguous, SKIP that cell entirely rather than guessing or repeating a nearby value. An empty or partially-filled day is far better than a fabricated one.';

        return [
            'model' => 'qwen/qwen3.8-27b',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => [
                    ['type' => 'text', 'text' => 'Extract every class for every day listed above from this timetable image.'],
                    ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
                ]],
            ],
            // 0.3 (the original value) let the model invent a plausible-sounding-
            // but-wrong lecturer/course name instead of the literal cell text —
            // real runs kept producing a different garbled name each time (e.g.
            // "Ainie Hayati,Afifah" coming back as "Anne Havatilafrih" on one run,
            // "Anne Nakatlafrim" on another), consistent with sampling noise.
            // Dropping all the way to 0 (fully greedy) traded that for a WORSE
            // failure: real logs showed the exact same fabricated name
            // ("Anne Havati Latifah") reused verbatim across three different
            // cells for three different real lecturers — a known failure mode of
            // greedy decoding, where it locks onto a completed pattern and
            // repeats it for structurally-similar-looking cells instead of
            // re-reading each one. 0.15 aims for the middle: still low enough to
            // mostly suppress invented answers, but enough randomness to avoid
            // that repeat-lock. Neither setting fixes the harder structural
            // mistakes (merging two different courses' cells into one span,
            // missing a day entirely) — those look more like the model not
            // having enough room to verify carefully at low reasoning effort —
            // but temperature costs nothing against the rate limit, so it's
            // worth tuning before attempting a bigger architecture change.
            'temperature' => 0.15,
            'response_format' => ['type' => 'json_object'],
            // This account's Groq plan caps this vision model at only 1000 OUTPUT
            // tokens/minute total (real server logs: "Rate limit reached... Limit
            // 1000"), and separately rejects a request outright — "Request too
            // large" — the moment Groq's OWN estimate of that single request's
            // expected output crosses 1000, even with a completely fresh budget.
            // 'low' reasoning_effort (down from 'medium') avoids that pre-flight
            // rejection at this prompt length — confirmed in production, this no
            // longer gets rejected outright.
            //
            // But at max_completion_tokens=3000 it then failed a DIFFERENT way:
            // Groq returned status 400 "json_validate_failed" with an EMPTY
            // failed_generation, twice in a row. That's not truncation (truncation
            // still returns partial content plus finish_reason=length) — an empty
            // failed_generation means nothing was left to validate at all. Hidden
            // reasoning tokens still count against max_completion_tokens even
            // though reasoning_format=hidden strips them from the response, so the
            // likely story is: reasoning alone (now working through 5 days instead
            // of 1) used up the entire 3000-token budget, leaving zero left for the
            // actual JSON answer. Raised to 5000 to give the visible answer room to
            // exist without changing reasoning_effort — isolating this from the
            // OTPM fix above, since that one is already confirmed working.
            //
            // 5000 wasn't enough either: real logs showed finish_reason=length,
            // with the (still perfectly valid) JSON cut off after finishing
            // Monday, Tuesday and just one class into Wednesday — Thursday and
            // Friday never got generated at all, simply because the budget ran
            // out partway through, not because anything was misread. Unlike the
            // json_validate_failed case, this is a clean, unambiguous "needs more
            // room" signal (valid JSON, explicit finish_reason=length), so raised
            // again to 8000. The earlier OTPM pre-flight rejection ("Request too
            // large") was measured on the LONGER 0104 prompt and hasn't actually
            // been triggered by this shorter prompt at any max_completion_tokens
            // value tried so far — so there's reasonable headroom to try this
            // before assuming it'll get rejected outright again. If it does start
            // reporting "Request too large", that means even this shorter prompt
            // is now over budget and max_completion_tokens needs to come back
            // down; if it instead still truncates at 8000, that's evidence this
            // whole-week-in-one-call approach may need to be split into two
            // smaller calls instead of just raising this number further.
            'reasoning_effort' => 'low',
            'reasoning_format' => 'hidden',
            'max_completion_tokens' => 8000,
        ];
    }

    /**
     * Normalizes a day-label string enough to compare the model's self-reported
     * "row_label_found" against the day it was actually asked for — case/whitespace
     * only, since the model may return either the Malay or English label.
     */
    private function normalizeDayLabel(?string $label): string
    {
        return mb_strtoupper(trim((string) $label));
    }

    private function localDayLabel(string $day): string
    {
        return match ($day) {
            'Monday' => 'Isnin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Khamis',
            'Friday' => 'Jumaat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Ahad',
            default => $day,
        };
    }

    private function validateSchedule(Request $request): array
    {
        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'day_of_week' => 'required|string|in:' . implode(',', ClassSchedule::DAYS),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
            'lecturer' => 'nullable|string|max:150',
        ]);

        return $data;
    }

    /**
     * @param array<int, array<string, mixed>> $classes
     */
    private function hasOverlaps(array $classes): bool
    {
        $byDay = [];

        foreach ($classes as $entry) {
            $day = $entry['day_of_week'] ?? null;
            $start = $entry['start_time'] ?? null;
            $end = $entry['end_time'] ?? null;

            if (! in_array($day, ClassSchedule::DAYS, true) || ! $this->isValidTime($start) || ! $this->isValidTime($end)) {
                continue; // invalid entries are dropped later anyway; don't let them trigger a false overlap
            }

            $byDay[$day][] = [$this->toMinutes($start), $this->toMinutes($end)];
        }

        foreach ($byDay as $ranges) {
            usort($ranges, fn ($a, $b) => $a[0] <=> $b[0]);

            for ($i = 1; $i < count($ranges); $i++) {
                if ($ranges[$i][0] < $ranges[$i - 1][1]) {
                    return true; // this range starts before the previous one ends -> overlap
                }
            }
        }

        return false;
    }

    private function toRaw(ClassSchedule $schedule): array
    {
        return [
            'id' => $schedule->id,
            'subject' => $schedule->subject,
            'day_of_week' => $schedule->day_of_week,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'room' => $schedule->room,
            'lecturer' => $schedule->lecturer,
        ];
    }

    private function toMinutes(?string $time): int
    {
        if (! $this->isValidTime($time)) {
            return 0;
        }

        [$h, $m] = explode(':', $time);

        return ((int) $h) * 60 + (int) $m;
    }

    /**
     * Safety net for a common vision-model mistake on grid timetables: instead of
     * reading a merged cell (one class spanning several hour columns) as a single
     * entry, the model sometimes emits one duplicate entry per hour column it touches
     * (same day/subject/room/lecturer, back-to-back times). Collapse those runs into
     * one entry covering the full span, regardless of whether the prompt was followed.
     *
     * @param array<int, array<string, mixed>> $classes
     * @return array<int, array<string, mixed>>
     */
    private function mergeAdjacentDuplicates(array $classes): array
    {
        $key = fn (array $e) => implode('|', [
            $e['day_of_week'] ?? '',
            trim((string) ($e['subject'] ?? '')),
            trim((string) ($e['room'] ?? '')),
            trim((string) ($e['lecturer'] ?? '')),
        ]);

        // Sort by day (in DAYS order), then start time, so back-to-back slots sit next to each other.
        usort($classes, function ($a, $b) {
            $dayA = array_search($a['day_of_week'] ?? null, ClassSchedule::DAYS, true);
            $dayB = array_search($b['day_of_week'] ?? null, ClassSchedule::DAYS, true);
            $dayA = $dayA === false ? 99 : $dayA;
            $dayB = $dayB === false ? 99 : $dayB;

            return $dayA <=> $dayB ?: $this->toMinutes($a['start_time'] ?? null) <=> $this->toMinutes($b['start_time'] ?? null);
        });

        $merged = [];

        foreach ($classes as $entry) {
            $last = end($merged);

            if (
                $last !== false
                && $key($last) === $key($entry)
                && $this->isValidTime($last['end_time'] ?? null)
                && $this->isValidTime($entry['start_time'] ?? null)
                && $last['end_time'] === $entry['start_time']
            ) {
                // Extends the previous block directly — merge instead of adding a duplicate.
                $merged[count($merged) - 1]['end_time'] = $entry['end_time'] ?? $last['end_time'];
                continue;
            }

            $merged[] = $entry;
        }

        return $merged;
    }

    private function isValidTime(?string $time): bool
    {
        return is_string($time) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) === 1;
    }

    private function extractJson(?string $raw): ?array
    {
        if (! $raw) {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
