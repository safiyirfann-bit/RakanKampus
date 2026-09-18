<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $reminders = $request->user()
            ->reminders()
            ->orderBy('due_at')
            ->get()
            ->map(fn (Reminder $r) => $this->transform($r));

        return view('reminders', [
            'user' => $request->user(),
            'reminders' => $reminders,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'type' => 'nullable|string|in:Exam,Assignment,Quiz,Other',
            'due_at' => 'required|date',
            'lead_hours' => 'nullable|numeric|min:0',
        ]);

        $reminder = $request->user()->reminders()->create([
            'subject' => $data['subject'],
            'type' => $data['type'] ?? 'Other',
            'due_at' => $data['due_at'],
            'lead_hours' => $data['lead_hours'] ?? 1,
        ]);

        return response()->json(['success' => true, 'reminder' => $this->transform($reminder)]);
    }

    public function update(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'subject' => 'required|string|max:150',
            'type' => 'nullable|string|in:Exam,Assignment,Quiz,Other',
            'due_at' => 'required|date',
            'lead_hours' => 'nullable|numeric|min:0',
        ]);

        $reminder->update([
            'subject' => $data['subject'],
            'type' => $data['type'] ?? 'Other',
            'due_at' => $data['due_at'],
            'lead_hours' => $data['lead_hours'] ?? 1,
        ]);

        return response()->json(['success' => true, 'reminder' => $this->transform($reminder)]);
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);

        $reminder->delete();

        return response()->json(['success' => true]);
    }

    private function transform(Reminder $reminder): array
    {
        $due = $reminder->due_at;
        $diffHours = now()->diffInMinutes($due, false) / 60;
        $leadHours = (float) $reminder->lead_hours;

        if ($diffHours <= 0) {
            $status = 'Passed';
            $statusColor = '#94a3b8';
            $dotBg = '#e2e8f0';
            $dotStroke = '#94a3b8';
        } elseif ($diffHours <= $leadHours) {
            $h = (int) floor($diffHours);
            $m = (int) floor(($diffHours - $h) * 60);
            $status = "🔔 Notified · {$h}h {$m}m left";
            $statusColor = '#ea580c';
            $dotBg = '#ffedd5';
            $dotStroke = '#ea580c';
        } elseif ($diffHours <= 24) {
            $status = 'Upcoming · in ' . (int) floor($diffHours) . 'h';
            $statusColor = '#2563eb';
            $dotBg = '#dbeafe';
            $dotStroke = '#2563eb';
        } else {
            $status = 'Upcoming · in ' . (int) floor($diffHours / 24) . 'd';
            $statusColor = '#0d9488';
            $dotBg = '#ccfbf1';
            $dotStroke = '#0d9488';
        }

        $typeStyles = [
            'Assignment' => ['color' => '#0d9488', 'bg' => '#f0fdfa'],
            'Quiz' => ['color' => '#7c3aed', 'bg' => '#f5f3ff'],
            'Other' => ['color' => '#64748b', 'bg' => '#f1f5f9'],
            'Exam' => ['color' => '#6366f1', 'bg' => '#eef2ff'],
        ];
        $typeStyle = $typeStyles[$reminder->type] ?? $typeStyles['Other'];

        $leadLabel = $leadHours < 1
            ? round($leadHours * 60) . ' min'
            : (rtrim(rtrim(number_format($leadHours, 1), '0'), '.') . ' hour' . ($leadHours == 1 ? '' : 's'));

        return [
            'id' => $reminder->id,
            'subject' => $reminder->subject,
            'type' => $reminder->type,
            'type_color' => $typeStyle['color'],
            'type_bg' => $typeStyle['bg'],
            'due_at' => $due->toIso8601String(),
            'due_at_input' => $due->format('Y-m-d\TH:i'),
            'when_text' => $due->format('j M · H:i'),
            'lead_hours' => $leadHours,
            'lead_label' => $leadLabel,
            'status_text' => $status,
            'status_color' => $statusColor,
            'dot_bg' => $dotBg,
            'dot_stroke' => $dotStroke,
        ];
    }
}
