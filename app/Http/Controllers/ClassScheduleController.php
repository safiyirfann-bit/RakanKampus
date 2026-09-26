<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Services\TimetablePdfParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClassScheduleController extends Controller
{
    /** Longest believable single class block (real timetables max out around 3h). */
    private const MAX_PLAUSIBLE_CLASS_MINUTES = 240;

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

    /**
     * AI timetable capture — step 1 of 2: read the photo and return a PREVIEW.
     * Nothing is saved here. The student reviews/edits the result in the modal
     * and then confirms, which calls bulkStore().
     *
     * Design: the model only TRANSCRIBES (one entry per occupied grid cell +
     * the KOD/NAMA KURSUS legend). Everything that needs reasoning — merging
     * adjacent cells into one class, working out start/end times, resolving a
     * course code to its name, sanity checks — is done in plain PHP below, so
     * it's deterministic instead of depending on a small vision model getting
     * it right. Earlier versions asked the model to do all of that at once and
     * it kept shifting times and inventing course names.
     */
    public function aiCapture(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic,pdf|max:6144', // 6MB
        ], [
            'photo.mimes' => 'Fail mesti gambar (JPG/PNG) atau PDF jadual.',
            'photo.max' => 'Fail terlalu besar (maksimum 6MB).',
        ]);

        $file = $request->file('photo');
        $isPdf = strtolower($file->getClientOriginalExtension()) === 'pdf' || $file->getMimeType() === 'application/pdf';

        if ($isPdf) {
            // Official PDF timetable: read its text layer directly — exact, no AI.
            if (! TimetablePdfParser::isAvailable()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Server belum boleh baca PDF (pdftotext tiada). Upload gambar/screenshot jadual buat masa ni.',
                ], 422);
            }

            try {
                $parsed = (new TimetablePdfParser())->parse($file->getRealPath());
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }
        } else {
            $dataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

            $response = Http::withToken(config('services.groq.key'))
                ->timeout(110)
                ->post('https://api.groq.com/openai/v1/chat/completions', $this->visionPayload($dataUri));

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

            Log::info('Groq vision raw response (timetable)', [
                'finish_reason' => $finishReason,
                'raw' => Str::limit((string) $rawContent, 4000),
            ]);

            if ($finishReason === 'length') {
                Log::warning('Groq vision response for timetable was cut off by max_completion_tokens');
            }

            $parsed = $this->extractJson($rawContent) ?? [];
        }

        // Legend: course code -> full course name, straight from the KOD/NAMA KURSUS table.
        $legend = [];
        foreach ((array) ($parsed['legend'] ?? []) as $k => $row) {
            // Accept whatever shape the model used: ["CODE","NAME"], {"code":..,"name":..}, or {"CODE":"NAME"}.
            if (is_string($row) && is_string($k)) {
                [$rawCode, $rawName] = [$k, $row];
            } elseif (is_array($row) && array_is_list($row)) {
                [$rawCode, $rawName] = [$row[0] ?? '', $row[1] ?? ''];
            } elseif (is_array($row)) {
                $rawCode = $row['code'] ?? $row['kod'] ?? $row['CODE'] ?? $row['KOD'] ?? '';
                $rawName = $row['name'] ?? $row['nama'] ?? $row['course'] ?? $row['NAME'] ?? $row['NAMA KURSUS'] ?? '';
            } else {
                continue;
            }
            $code = $this->normalizeCode($rawCode);
            $name = trim((string) $rawName);
            if ($code !== '' && $name !== '' && ! in_array(strtoupper($name), ['JUMLAH', 'TOTAL'], true)) {
                $legend[$code] = $name;
            }
        }

        // Cells: one per occupied grid cell -> [day, start, end, code, type, lecturer, room].
        $cells = [];
        foreach ((array) ($parsed['cells'] ?? []) as $row) {
            if (! is_array($row) || count($row) < 4) {
                continue;
            }

            $day = $this->parseDay($row[0] ?? null);
            $start = $this->parseHour($row[1] ?? null);
            $end = $this->parseHour($row[2] ?? null);
            $code = $this->normalizeCode($row[3] ?? '');
            $type = strtoupper(trim((string) ($row[4] ?? '')));
            if (($type === '' || $type === 'NULL') && preg_match('/\(([A-Z]{1,3})\)/i', (string) ($row[3] ?? ''), $tm)) {
                $type = strtoupper($tm[1]); // model left "(L)" inside the code instead
            }
            $type = $type === 'NULL' ? '' : preg_replace('/[^A-Z]/', '', $type);

            if (! $day || ! $start || ! $code) {
                continue;
            }

            if (! $end || $this->toMinutes($end) <= $this->toMinutes($start)) {
                $end = sprintf('%02d:%s', ((int) substr($start, 0, 2)) + 1, substr($start, 3, 2)); // assume a 1-hour column
            }

            $cells[] = [
                'day_of_week' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'code' => $code,
                'type' => $type,
                'lecturer' => $this->cleanText($row[5] ?? null),
                'room' => $this->cleanText($row[6] ?? null),
            ];
        }

        if (count($cells) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Tak dapat kesan jadual kelas dalam gambar tu. Cuba gambar yang lebih jelas, atau isi manual.',
            ], 422);
        }

        // The model often misreads one or two characters of a code (DFK50463 for
        // DFP50463). Snap each code to the closest legend code before merging, so
        // the course name can still be found.
        foreach ($cells as &$cell) {
            [$cell['code'], $cell['code_fixed']] = $this->matchLegendCode($cell['code'], array_keys($legend));
        }
        unset($cell);

        $classes = $this->mergeCells($cells);

        // Turn code + legend into the subject, and flag anything that looks off
        // so the preview highlights it for the student to double-check.
        foreach ($classes as &$c) {
            $name = $legend[$c['code']] ?? null;
            $typeLabel = match ($c['type']) {
                'L' => ' (Kuliah)',
                'P' => ' (Amali)',
                'TU' => ' (Tutorial)',
                default => '',
            };
            // Subject shown to the student = the course NAME, not the code.
            $c['subject'] = Str::limit(
                $name ? Str::title(mb_strtolower($name)) . $typeLabel : $c['code'] . $typeLabel,
                150,
                ''
            );

            $warnings = [];
            if (! $name) {
                $warnings[] = "Nama kursus untuk kod {$c['code']} tak jumpa — sila taip nama subjek.";
            } elseif (! empty($c['code_fixed'])) {
                $warnings[] = "AI baca kod kurang jelas, dipadankan ke {$c['code']} — pastikan subjek betul.";
            }
            $c['warnings'] = $warnings;
            unset($c['code'], $c['type'], $c['code_fixed']);
        }
        unset($c);

        // A real polytechnic class is never more than ~4 consecutive hours. A longer
        // merged block usually means the model repeated one cell's text across
        // columns that held something else (idea from commit 516750f — flagged for
        // review here instead of dropping the whole day, since the student checks
        // the preview anyway).
        foreach ($classes as &$c) {
            if ($this->toMinutes($c['end_time']) - $this->toMinutes($c['start_time']) > self::MAX_PLAUSIBLE_CLASS_MINUTES) {
                $c['warnings'][] = 'Kelas ni panjang luar biasa (lebih 4 jam) — semak masa tamat.';
            }
        }
        unset($c);

        // Overlapping classes on the same day = the model misread the grid somewhere.
        // Flag both sides instead of rejecting everything, since the student reviews anyway.
        foreach ($this->overlappingIndexes($classes) as $i) {
            $classes[$i]['warnings'][] = 'Bertindih masa dengan kelas lain — semak masa.';
        }

        return response()->json(['success' => true, 'classes' => array_values($classes)]);
    }

    /**
     * AI timetable capture — step 2 of 2: save the classes the student confirmed.
     */
    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'classes' => 'required|array|min:1|max:60',
            'classes.*.subject' => 'required|string|max:150',
            'classes.*.day_of_week' => 'required|string|in:' . implode(',', ClassSchedule::DAYS),
            'classes.*.start_time' => 'required|date_format:H:i',
            'classes.*.end_time' => 'required|date_format:H:i',
            'classes.*.room' => 'nullable|string|max:100',
            'classes.*.lecturer' => 'nullable|string|max:150',
        ]);

        $created = [];

        foreach ($data['classes'] as $c) {
            $schedule = $request->user()->classSchedules()->create([
                'subject' => $c['subject'],
                'day_of_week' => $c['day_of_week'],
                'start_time' => $c['start_time'],
                'end_time' => $c['end_time'],
                'room' => $c['room'] ?? null,
                'lecturer' => $c['lecturer'] ?? null,
            ]);

            $created[] = $this->toRaw($schedule);
        }

        return response()->json(['success' => true, 'schedules' => $created]);
    }

    /**
     * The Groq request payload. The model is asked to transcribe only — no
     * merging, no time maths, no guessing course names.
     */
    private function visionPayload(string $dataUri): array
    {
        $systemPrompt = 'You transcribe a Malaysian polytechnic class timetable image into JSON. You ONLY copy text; you never merge, calculate or guess. '
            . 'The main table is a grid: ROWS are days (ISNIN=MON, SELASA=TUE, RABU=WED, KHAMIS=THU, JUMAAT=FRI, SABTU=SAT, AHAD=SUN), COLUMNS are one-hour time slots whose header shows a start and end time (e.g. "8:00 ... 9:00", "2.00 ... 3:00"). '
            . 'For EVERY non-empty cell, output ONE entry, even if the cell next to it has the same text (the app merges them itself). Do not merge cells. Do not skip a cell because it repeats. '
            . 'Each cell usually has 3 lines: course code with type in brackets (e.g. "DFK50083(L)"), lecturer(s), room. '
            . 'Copy each value character-by-character exactly as printed. Codes look like 3 letters + 5 digits (e.g. DFP50463, MPU22071) or short codes like "PA". Room codes look like APDV1, DK-JTMK, M204-JTMK, CNW3. '
            . 'Take start/end straight from THAT cell\'s column header, as printed. '
            . 'Also transcribe the separate legend table (KOD, NAMA KURSUS) exactly. '
            . 'Reply with ONLY this JSON, no markdown: '
            . '{"legend":[["CODE","COURSE NAME"],...],"cells":[["DAY","START","END","CODE","TYPE","LECTURER","ROOM"],...]} '
            . 'Example cell: ["FRI","8:00","9:00","DFK50083","L","AINIE HAYATI,AFIFAH","APDV1"]. '
            . 'Use null for a value that is unreadable. If the image is not a timetable, return {"legend":[],"cells":[]}.';

        return [
            'model' => 'qwen/qwen3.8-27b',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => [
                    ['type' => 'text', 'text' => 'Transcribe every occupied cell and the legend from this timetable.'],
                    ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
                ]],
            ],
            // See git history for why these values: low temperature without being
            // fully greedy (which repeat-locked on one name), low reasoning effort to
            // stay under this Groq plan's 1000 output tokens/minute pre-flight check,
            // and enough completion budget that hidden reasoning doesn't eat the answer.
            // The compact array-per-cell format keeps the visible answer small.
            'temperature' => 0.15,
            'response_format' => ['type' => 'json_object'],
            'reasoning_effort' => 'low',
            'reasoning_format' => 'hidden',
            'max_completion_tokens' => 8000,
        ];
    }

    /**
     * Merge back-to-back cells of the same class (same day, code, type, room,
     * lecturer, and the next cell starting exactly where the previous ended)
     * into one class spanning the full time range.
     *
     * @param array<int, array<string, mixed>> $cells
     * @return array<int, array<string, mixed>>
     */
    private function mergeCells(array $cells): array
    {
        usort($cells, function ($a, $b) {
            return array_search($a['day_of_week'], ClassSchedule::DAYS, true) <=> array_search($b['day_of_week'], ClassSchedule::DAYS, true)
                ?: $this->toMinutes($a['start_time']) <=> $this->toMinutes($b['start_time']);
        });

        // Lecturer names are compared order-insensitively ("PUTERI,JAMALIAH" == "JAMALIAH,PUTERI").
        $lect = function (?string $l) {
            $names = array_map('trim', explode(',', strtoupper((string) $l)));
            sort($names);

            return implode(',', $names);
        };
        $key = fn (array $c) => implode('|', [$c['day_of_week'], $c['code'], $c['type'], $c['room'], $lect($c['lecturer'])]);
        // code_fixed must survive a merge if any merged cell was corrected.
        $merged = [];

        foreach ($cells as $cell) {
            $lastIndex = count($merged) - 1;
            $last = $merged[$lastIndex] ?? null;

            if ($last && $key($last) === $key($cell)) {
                if ($last['end_time'] === $cell['start_time']) {
                    $merged[$lastIndex]['end_time'] = $cell['end_time'];
                    $merged[$lastIndex]['code_fixed'] = ! empty($last['code_fixed']) || ! empty($cell['code_fixed']);
                    continue;
                }
                if ($last['start_time'] === $cell['start_time']) {
                    continue; // exact duplicate cell from the model
                }
            }

            $merged[] = $cell;
        }

        return $merged;
    }

    /**
     * @param array<int, array<string, mixed>> $classes
     * @return array<int, int>
     */
    private function overlappingIndexes(array $classes): array
    {
        $bad = [];
        $n = count($classes);

        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                if ($classes[$i]['day_of_week'] !== $classes[$j]['day_of_week']) {
                    continue;
                }
                if ($this->toMinutes($classes[$i]['start_time']) < $this->toMinutes($classes[$j]['end_time'])
                    && $this->toMinutes($classes[$j]['start_time']) < $this->toMinutes($classes[$i]['end_time'])) {
                    $bad[$i] = $i;
                    $bad[$j] = $j;
                }
            }
        }

        return array_values($bad);
    }

    /**
     * Snap a possibly-misread code to the nearest legend code (edit distance <= 2,
     * and a unique best match). Returns [code, wasCorrected].
     *
     * @param array<int, string> $legendCodes
     * @return array{0: string, 1: bool}
     */
    private function matchLegendCode(string $code, array $legendCodes): array
    {
        if ($code === '' || in_array($code, $legendCodes, true) || count($legendCodes) === 0) {
            return [$code, false];
        }

        $best = null;
        $bestDist = PHP_INT_MAX;
        $tie = false;

        foreach ($legendCodes as $candidate) {
            $d = levenshtein($code, $candidate);
            if ($d < $bestDist) {
                [$best, $bestDist, $tie] = [$candidate, $d, false];
            } elseif ($d === $bestDist) {
                $tie = true;
            }
        }

        return ($best !== null && $bestDist <= 2 && ! $tie) ? [$best, true] : [$code, false];
    }

    /** "JUMAAT" / "Fri" / "friday" -> "Friday" */
    private function parseDay(mixed $label): ?string
    {
        $l = mb_strtoupper(trim((string) $label));

        return match (true) {
            in_array($l, ['MON', 'MONDAY', 'ISNIN'], true) => 'Monday',
            in_array($l, ['TUE', 'TUESDAY', 'SELASA'], true) => 'Tuesday',
            in_array($l, ['WED', 'WEDNESDAY', 'RABU'], true) => 'Wednesday',
            in_array($l, ['THU', 'THURSDAY', 'KHAMIS'], true) => 'Thursday',
            in_array($l, ['FRI', 'FRIDAY', 'JUMAAT'], true) => 'Friday',
            in_array($l, ['SAT', 'SATURDAY', 'SABTU'], true) => 'Saturday',
            in_array($l, ['SUN', 'SUNDAY', 'AHAD'], true) => 'Sunday',
            default => null,
        };
    }

    /**
     * Column-header time as printed ("8:00", "2.00", "12:00", "3 PM") -> "HH:MM" 24h.
     * Timetable headers are 12-hour without AM/PM, and classes run ~8am-6pm,
     * so 1-7 are treated as afternoon.
     */
    private function parseHour(mixed $raw): ?string
    {
        if (! preg_match('/(\d{1,2})(?:[:.](\d{2}))?\s*(am|pm)?/i', trim((string) $raw), $m)) {
            return null;
        }

        $h = (int) $m[1];
        $min = isset($m[2]) && $m[2] !== '' ? (int) $m[2] : 0;
        $ampm = strtolower($m[3] ?? '');

        if ($ampm === 'pm' && $h < 12) {
            $h += 12;
        } elseif ($ampm === '' && $h >= 1 && $h <= 7) {
            $h += 12;
        }

        if ($h > 23 || $min > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $h, $min);
    }

    /** "dfk 50083(L)" -> "DFK50083" (type in brackets is read separately). */
    private function normalizeCode(mixed $raw): string
    {
        $code = strtoupper((string) $raw);
        $code = preg_replace('/\(.*$/', '', $code);

        return preg_replace('/[^A-Z0-9]/', '', $code);
    }

    private function cleanText(mixed $raw): ?string
    {
        $t = trim(preg_replace('/\s+/', ' ', (string) $raw));

        return ($t === '' || strtolower($t) === 'null') ? null : Str::limit($t, 100, '');
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
