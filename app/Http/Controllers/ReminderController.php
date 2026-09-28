<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $reminders = $user->reminders()
            ->orderBy('due_at')
            ->get()
            ->map(fn (Reminder $r) => $this->toRaw($r));

        $historyCount = $user->reminders()->onlyTrashed()->count()
            + $user->reminders()->where('due_at', '<', now())->count();

        return view('reminders', [
            'user' => $user,
            'reminders' => $reminders,
            'historyCount' => $historyCount,
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $deleted = $user->reminders()->onlyTrashed()->get()
            ->map(fn (Reminder $r) => $this->toHistoryItem($r, true));

        $completed = $user->reminders()->where('due_at', '<', now())->get()
            ->map(fn (Reminder $r) => $this->toHistoryItem($r, false));

        $items = $deleted->concat($completed)->sortByDesc('due_at')->values();

        return view('reminders-history', [
            'user' => $user,
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateReminder($request);

        $reminder = $request->user()->reminders()->create($data);

        return response()->json(['success' => true, 'reminder' => $this->toRaw($reminder)]);
    }

    /**
     * AI reminder capture — step 1 of 2: read the photo/PDF and return EVERY
     * dated item it contains (each exam paper, assignment, quiz...) as a
     * preview. Nothing is saved here; the student reviews/edits the list and
     * confirms, which calls bulkStore().
     */
    public function aiCapture(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic,pdf|max:6144', // 6MB
        ], [
            'photo.mimes' => __('The file must be an image (JPG/PNG) or a PDF.'),
            'photo.max' => __('The file is too large (maximum 6MB).'),
        ]);

        $file = $request->file('photo');
        $mime = $file->getMimeType();
        $isPdf = $mime === 'application/pdf' || strtolower($file->getClientOriginalExtension()) === 'pdf';
        $useGemini = (bool) config('services.gemini.key');

        if ($isPdf && ! $useGemini) {
            return response()->json(['success' => false, 'error' => __('Please upload a photo or screenshot instead of a PDF.')], 422);
        }

        $today = now();
        $prompt = "You extract reminders from an image or PDF (exam slip, exam timetable, assignment brief, class notice, announcement) for a Malaysian polytechnic student app. "
            . "Today's date is {$today->format('Y-m-d')} ({$today->format('l')}). "
            . 'Return EVERY distinct dated item you can see — each exam paper, test, quiz, assignment, project, presentation or deadline is its own item. Do not merge items and do not skip any. '
            . 'Copy course codes and names exactly as printed; never invent a subject. subject = a short title, e.g. "DFK50083 Python Programming - Final Exam" or "Mobile App Assignment 3". '
            . 'type must be one of Exam, Assignment, Quiz, Other. '
            . 'due_date = YYYY-MM-DD. Resolve relative dates (esok, lusa, next Monday) from today. If the year is not shown, use the next occurrence on or after today. '
            . 'due_time = HH:MM in 24-hour time, the START time if a range is shown (e.g. 8:30-10:30 -> 08:30). If no time is shown, use null — do not guess. '
            . 'Reply with ONLY this JSON, no markdown: {"items":[{"subject":string,"type":string,"due_date":string,"due_time":string|null}]}. '
            . 'If nothing dated is visible, reply {"items":[]}. '
            . 'If the image is a WEEKLY class timetable (days of the week like Isnin/Monday with time slots, but no calendar dates), reply {"items":[],"kind":"weekly_timetable"}.';

        [$raw, $error] = $useGemini
            ? $this->geminiRead($prompt, $file->getRealPath(), $isPdf ? 'application/pdf' : $mime)
            : $this->groqRead($prompt, $file->getRealPath(), $mime);

        if ($raw === null) {
            return response()->json(['success' => false, 'error' => $error], 422);
        }

        // Models don't always follow the exact shape: accept {"items":[...]}, a bare
        // [...] list, other wrapper names, or a single object.
        $parsed = $this->extractJson($raw) ?? [];
        if (array_is_list($parsed)) {
            $rows = $parsed;
        } else {
            $rows = $parsed['items'] ?? $parsed['reminders'] ?? $parsed['events'] ?? $parsed['data'] ?? null;
            if (! is_array($rows)) {
                $rows = isset($parsed['subject']) || isset($parsed['due_date']) || isset($parsed['date']) ? [$parsed] : [];
            }
        }

        Log::info('Reminder AI capture parsed', ['count' => count($rows), 'raw' => Str::limit((string) $raw, 1500)]);

        $items = [];
        $seen = [];
        foreach (array_slice((array) $rows, 0, 40) as $row) {
            if (! is_array($row)) {
                continue;
            }
            // Alternative key names the model sometimes uses.
            $row['due_date'] = $row['due_date'] ?? $row['date'] ?? $row['dueDate'] ?? null;
            $row['due_time'] = $row['due_time'] ?? $row['time'] ?? $row['dueTime'] ?? null;
            $row['subject'] = $row['subject'] ?? $row['title'] ?? $row['name'] ?? null;
            if (empty($row['due_date'])) {
                continue;
            }
            // Normalise dates like 2/12/2026 or 02-12-2026 (day first, Malaysian style) to Y-m-d.
            if (preg_match('#^(\d{1,2})[/.-](\d{1,2})[/.-](\d{4})$#', trim($row['due_date']), $dm)) {
                $row['due_date'] = sprintf('%04d-%02d-%02d', $dm[3], $dm[2], $dm[1]);
            }

            $type = in_array($row['type'] ?? null, ['Exam', 'Assignment', 'Quiz', 'Other'], true) ? $row['type'] : 'Other';
            $warnings = [];

            // Accept "8:30", "08.30", "8:30 PM", "2.00pm".
            $time = null;
            if (is_string($row['due_time'] ?? null) && preg_match('/(\d{1,2})[:.](\d{2})\s*([ap]\.?m\.?)?/i', $row['due_time'], $tm)) {
                $h = (int) $tm[1];
                $ampm = strtolower(str_replace('.', '', $tm[3] ?? ''));
                if ($ampm === 'pm' && $h < 12) $h += 12;
                if ($ampm === 'am' && $h === 12) $h = 0;
                if ($h <= 23 && (int) $tm[2] <= 59) $time = sprintf('%02d:%02d', $h, (int) $tm[2]);
            }
            if ($time === null) {
                $time = in_array($type, ['Exam', 'Quiz'], true) ? '09:00' : '23:59';
                $warnings[] = __('No time was shown — check the time.');
            }

            try {
                $dueAt = Carbon::createFromFormat('Y-m-d H:i', trim($row['due_date']) . ' ' . $time);
            } catch (\Throwable $e) {
                continue;
            }

            // A date that's already past usually means the image had no year / the AI picked the wrong one.
            $safety = 0;
            while ($dueAt->isPast() && $safety < 3) {
                $dueAt->addYear();
                $safety++;
            }
            if ($safety > 0) {
                $warnings[] = __('The year was guessed — check the date.');
            }

            $subject = trim((string) ($row['subject'] ?? ''));
            if ($subject === '') {
                $subject = __(':type Reminder', ['type' => __($type)]);
                $warnings[] = __('No title was found — please type one.');
            }

            $key = mb_strtolower($subject) . '|' . $dueAt->format('Y-m-d H:i');
            if (isset($seen[$key])) {
                continue; // the same item listed twice
            }
            $seen[$key] = true;

            $items[] = [
                'subject' => Str::limit($subject, 150, ''),
                'type' => $type,
                'due_date' => $dueAt->format('Y-m-d'),
                'due_time' => $dueAt->format('H:i'),
                'lead_hours' => match ($type) {
                    'Exam' => 3,
                    'Assignment' => 6,
                    default => 1,
                },
                'warnings' => $warnings,
            ];
        }

        if (count($items) === 0) {
            $isWeekly = is_array($parsed) && ($parsed['kind'] ?? null) === 'weekly_timetable';

            return response()->json([
                'success' => false,
                'error' => $isWeekly
                    ? __('This looks like a weekly class timetable, which has no dates. Upload it on the Timetable page instead — reminders need an exam, test or assignment with a date.')
                    : __('No dates were found in that file. Reminders need an exam slip, exam timetable or assignment brief that shows a date.'),
            ], 422);
        }

        usort($items, fn ($a, $b) => strcmp($a['due_date'] . $a['due_time'], $b['due_date'] . $b['due_time']));

        return response()->json(['success' => true, 'items' => $items]);
    }

    /**
     * AI reminder capture — step 2 of 2: save the reminders the student confirmed.
     */
    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'reminders' => 'required|array|min:1|max:40',
            'reminders.*.subject' => 'required|string|max:150',
            'reminders.*.type' => 'required|string|in:Exam,Assignment,Quiz,Other',
            'reminders.*.due_date' => 'required|date_format:Y-m-d',
            'reminders.*.due_time' => 'required|date_format:H:i',
            'reminders.*.lead_hours' => 'nullable|numeric|min:0',
        ]);

        $created = [];
        foreach ($data['reminders'] as $r) {
            $reminder = $request->user()->reminders()->create([
                'subject' => $r['subject'],
                'type' => $r['type'],
                'due_at' => Carbon::createFromFormat('Y-m-d H:i', $r['due_date'] . ' ' . $r['due_time']),
                'lead_hours' => $r['lead_hours'] ?? 1,
            ]);
            $created[] = $this->toRaw($reminder);
        }

        return response()->json(['success' => true, 'reminders' => $created]);
    }

    /**
     * Read the file with Google Gemini (GEMINI_API_KEY). Retries when Google is
     * busy (500/503, common on the free tier).
     *
     * @return array{0: ?string, 1: ?string} [raw JSON text, error message for the student]
     */
    private function geminiRead(string $prompt, string $path, string $mime): array
    {
        $model = config('services.gemini.model') ?: 'gemini-flash-latest';
        $response = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $response = Http::timeout(90)
                ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'systemInstruction' => ['parts' => [['text' => $prompt]]],
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [
                            ['inline_data' => ['mime_type' => $mime, 'data' => base64_encode(file_get_contents($path))]],
                            ['text' => 'List every reminder in this file.'],
                        ],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0,
                        'responseMimeType' => 'application/json',
                        'maxOutputTokens' => 8192,
                    ],
                ]);

            if (! in_array($response->status(), [500, 503], true)) {
                break;
            }
            sleep(2 * $attempt);
        }

        if ($response->failed()) {
            Log::error('Gemini reminder capture error', ['status' => $response->status(), 'body' => Str::limit($response->body(), 1500)]);

            return [null, in_array($response->status(), [429, 500, 503], true)
                ? __('The AI server is busy right now. Please try again in a minute.')
                : __('AI could not process that image right now. Please try again shortly.')];
        }

        $text = collect($response->json('candidates.0.content.parts') ?? [])
            ->reject(fn ($p) => ! empty($p['thought']))
            ->pluck('text')->filter()->implode('');

        Log::info('Gemini reminder capture raw', ['raw' => Str::limit($text, 3000)]);

        return $text !== '' ? [$text, null] : [null, __('Could not detect a date/subject in that image. Try a clearer picture, or fill it in manually.')];
    }

    /**
     * Fallback when no Gemini key is configured: Groq vision (images only).
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function groqRead(string $prompt, string $path, string $mime): array
    {
        $response = Http::withToken(config('services.groq.key'))
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'qwen/qwen3.8-27b',
                'messages' => [
                    ['role' => 'system', 'content' => $prompt],
                    ['role' => 'user', 'content' => [
                        ['type' => 'text', 'text' => 'List every reminder in this image.'],
                        ['type' => 'image_url', 'image_url' => ['url' => 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path))]],
                    ]],
                ],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            Log::error('Groq vision API error', ['status' => $response->status(), 'body' => $response->body()]);

            return [null, __('AI could not process that image right now. Please try again shortly.')];
        }

        return [(string) $response->json('choices.0.message.content'), null];
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

        // AI kadang bungkus JSON dalam code fence atau ada text tambahan sebelum/lepas
        if (preg_match('/\[.*\]|\{.*\}/s', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    public function update(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);

        $data = $this->validateReminder($request);

        // Due date or lead time may have changed, so allow it to notify again
        $reminder->update($data + ['notified_at' => null, 'notified_leads' => null]);

        return response()->json(['success' => true, 'reminder' => $this->toRaw($reminder)]);
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);

        $reminder->delete();

        return response()->json(['success' => true]);
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $request->user()->reminders()
            ->whereIn('id', $data['ids'])
            ->get()
            ->each(fn (Reminder $r) => $r->delete());

        return response()->json(['success' => true]);
    }

    public function historyBulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        Reminder::withTrashed()
            ->where('user_id', $request->user()->id)
            ->whereIn('id', $data['ids'])
            ->get()
            ->each(fn (Reminder $r) => $r->forceDelete());

        return response()->json(['success' => true]);
    }

    private function validateReminder(Request $request): array
    {
        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'type' => 'nullable|string|in:Exam,Assignment,Quiz,Other',
            'due_at' => 'required|date',
            'lead_hours' => 'nullable|numeric|min:0',
            'repeat_lead_hours' => 'nullable|array|max:8',
            'repeat_lead_hours.*' => 'numeric|min:0',
        ]);

        $repeatLeadHours = collect($data['repeat_lead_hours'] ?? [])
            ->map(fn ($h) => (float) $h)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return [
            'subject' => $data['subject'],
            'type' => $data['type'] ?? 'Other',
            'due_at' => $data['due_at'],
            'lead_hours' => $data['lead_hours'] ?? 1,
            'repeat_lead_hours' => empty($repeatLeadHours) ? null : $repeatLeadHours,
        ];
    }

    private function toRaw(Reminder $reminder): array
    {
        return [
            'id' => $reminder->id,
            'subject' => $reminder->subject,
            'type' => $reminder->type,
            'due_at' => $reminder->due_at->toIso8601String(),
            'lead_hours' => (float) $reminder->lead_hours,
            'repeat_lead_hours' => $reminder->repeat_lead_hours ?? [],
        ];
    }

    private function toHistoryItem(Reminder $reminder, bool $deleted): array
    {
        return [
            'id' => $reminder->id,
            'subject' => $reminder->subject,
            'type' => $reminder->type,
            'due_at' => $reminder->due_at->toIso8601String(),
            'status' => $deleted ? 'deleted' : 'completed',
        ];
    }
}
