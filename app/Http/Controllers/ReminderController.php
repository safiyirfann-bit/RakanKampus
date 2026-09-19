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

    public function aiCapture(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:6144', // 6MB
        ]);

        $file = $request->file('photo');
        $dataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

        $today = now();

        $systemPrompt = "You extract reminder details from a photo of an exam slip, class timetable, or assignment brief for a Malaysian polytechnic student app. "
            . "Today's date is {$today->format('Y-m-d')} ({$today->format('l')}). Use this to resolve relative dates (e.g. 'esok', 'next Monday') and to infer the year when the image only shows day/month. "
            . "If the image is a full exam timetable listing many different subjects/papers (e.g. an official exam board schedule for a whole cohort), extract ONLY the single earliest (soonest date/time) entry — don't try to merge or list them all. "
            . 'Reply with ONLY a single JSON object, no markdown, no code fences, no explanation, in exactly this shape: '
            . '{"detected": true|false, "subject": string|null, "type": "Exam"|"Assignment"|"Quiz"|"Other"|null, "due_date": "YYYY-MM-DD"|null, "due_time": "HH:MM"|null}. '
            . 'Set detected=false ONLY if the image clearly has no identifiable subject/title AND no identifiable due date (e.g. a random unrelated photo). '
            . "If a time isn't shown in the image, set due_time to null (don't guess a time). "
            . "subject should be a short descriptive title (e.g. 'Database Systems Final Exam', 'Mobile App Assignment 3'), not the whole raw text of the image.";

        $response = Http::withToken(config('services.groq.key'))
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'qwen/qwen3.6-27b',
                'reasoning_effort' => 'none',
                'reasoning_format' => 'hidden',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => [
                        ['type' => 'text', 'text' => 'Extract the reminder details from this image.'],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
                    ]],
                ],
                'temperature' => 0.2,
            ]);

        if ($response->failed()) {
            Log::error('Groq vision API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'AI tak dapat proses gambar tu sekarang. Cuba lagi sekejap.',
            ], 422);
        }

        $parsed = $this->extractJson($response->json('choices.0.message.content'));

        if (! $parsed || empty($parsed['detected']) || empty($parsed['due_date'])) {
            return response()->json([
                'success' => false,
                'error' => 'Tak dapat kesan tarikh/subjek dalam gambar tu. Cuba gambar yang lebih jelas, atau isi manual.',
            ], 422);
        }

        $type = in_array($parsed['type'] ?? null, ['Exam', 'Assignment', 'Quiz', 'Other'], true)
            ? $parsed['type']
            : 'Other';

        $time = $parsed['due_time'] ?? match ($type) {
            'Exam', 'Quiz' => '09:00',
            default => '23:59',
        };

        try {
            $dueAt = Carbon::createFromFormat('Y-m-d H:i', $parsed['due_date'] . ' ' . $time);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Tarikh dalam gambar tu tak jelas. Cuba isi manual ye.',
            ], 422);
        }

        // Kalau tarikh yang dikesan dah lepas (biasanya sebab gambar takde tahun/AI silap tahun), cuba tahun akan datang
        $safety = 0;
        while ($dueAt->isPast() && $safety < 3) {
            $dueAt->addYear();
            $safety++;
        }

        $leadHours = match ($type) {
            'Exam' => 3,
            'Assignment' => 6,
            'Quiz' => 1,
            default => 1,
        };

        $subject = trim((string) ($parsed['subject'] ?? '')) ?: "{$type} Reminder";

        $reminder = $request->user()->reminders()->create([
            'subject' => Str::limit($subject, 150, ''),
            'type' => $type,
            'due_at' => $dueAt,
            'lead_hours' => $leadHours,
        ]);

        return response()->json(['success' => true, 'reminder' => $this->toRaw($reminder)]);
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
        if (preg_match('/\{.*\}/s', $raw, $m)) {
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
        $reminder->update($data + ['notified_at' => null]);

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
        ]);

        return [
            'subject' => $data['subject'],
            'type' => $data['type'] ?? 'Other',
            'due_at' => $data['due_at'],
            'lead_hours' => $data['lead_hours'] ?? 1,
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
