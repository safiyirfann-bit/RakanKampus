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

    public function aiCapture(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:6144', // 6MB
        ]);

        $file = $request->file('photo');
        $dataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

        $days = implode('", "', ClassSchedule::DAYS);

        $systemPrompt = 'You extract a weekly class timetable from a photo for a Malaysian polytechnic student app. '
            . "The photo may be in English or Bahasa Melayu (e.g. Isnin=Monday, Selasa=Tuesday, Rabu=Wednesday, Khamis=Thursday, Jumaat=Friday, Sabtu=Saturday, Ahad=Sunday) — always translate day names to English in your reply. "
            . 'Reply with ONLY a single JSON object, no markdown, no code fences, no explanation, in exactly this shape: '
            . '{"detected": true|false, "classes": [{"subject": string, "day_of_week": "' . $days . '", "start_time": "HH:MM", "end_time": "HH:MM", "room": string|null, "lecturer": string|null}]}. '
            . 'Use 24-hour HH:MM for times. Set detected=false and classes=[] ONLY if the image clearly is not a class timetable at all. '
            . "Extract every class slot you can find, one entry per class per day (if a subject repeats on multiple days, list it once per day it occurs). subject should be a short course name, not the whole cell text.";

        $response = Http::withToken(config('services.groq.key'))
            ->timeout(45)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'qwen/qwen3.8-27b',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => [
                        ['type' => 'text', 'text' => 'Extract every class from this timetable image.'],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
                    ]],
                ],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            Log::error('Groq vision API error (timetable)', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'AI tak dapat proses gambar tu sekarang. Cuba lagi sekejap.',
            ], 422);
        }

        $parsed = $this->extractJson($response->json('choices.0.message.content'));
        $classes = $parsed['classes'] ?? null;

        if (! $parsed || empty($parsed['detected']) || ! is_array($classes) || count($classes) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Tak dapat kesan jadual kelas dalam gambar tu. Cuba gambar yang lebih jelas, atau isi manual.',
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
