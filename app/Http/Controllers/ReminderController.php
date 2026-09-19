<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

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
