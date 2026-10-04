<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Feedback;
use App\Models\Information;
use App\Models\KnowledgeBase;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function show(Information $information)
    {
        $entries = $information->knowledgeEntries()->latest()->get();

        // How often the bot used this topic's answers (chat_messages.knowledge_base_id).
        $ids = $entries->pluck('id');
        $askCounts = collect();
        $askedThisWeek = 0;
        $askedLastWeek = 0;
        try {
            if ($ids->isNotEmpty()) {
                $askCounts = ChatMessage::whereIn('knowledge_base_id', $ids)
                    ->selectRaw('knowledge_base_id, COUNT(*) as n')
                    ->groupBy('knowledge_base_id')
                    ->pluck('n', 'knowledge_base_id');
                $askedThisWeek = ChatMessage::whereIn('knowledge_base_id', $ids)->where('created_at', '>=', now()->subDays(7))->count();
                $askedLastWeek = ChatMessage::whereIn('knowledge_base_id', $ids)->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
            }
        } catch (\Throwable $e) {
            // column not migrated yet: show zeros
        }

        return view('admin.knowledge-detail', [
            'information' => $information,
            'entries' => $entries,
            'askCounts' => $askCounts,
            'askedThisWeek' => $askedThisWeek,
            'askedLastWeek' => $askedLastWeek,
            'unansweredCount' => UnansweredQuestion::where('status', 'pending')->count(),
            'unreadFeedbackCount' => Feedback::where('is_read', false)->count(),
        ]);
    }

    public function store(Request $request, Information $information)
    {
        $data = $request->validate([
            'intent' => 'required|string|max:255',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'coordinates' => 'nullable|string|max:2000',
        ]);
        $data = $this->withCoordinates($data);

        $information->knowledgeEntries()->create($data);

        return redirect()
            ->route('admin.information.show', $information->id)
            ->with('status', 'Entry added successfully.');
    }

    public function update(Request $request, Information $information, KnowledgeBase $entry)
    {
        $data = $request->validate([
            'intent' => 'required|string|max:255',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'coordinates' => 'nullable|string|max:2000',
        ]);
        $data = $this->withCoordinates($data);

        $entry->update($data);

        return redirect()
            ->route('admin.information.show', $information->id)
            ->with('status', 'Entry updated successfully.');
    }

    public function destroy(Information $information, KnowledgeBase $entry)
    {
        $entry->delete();

        return redirect()
            ->route('admin.information.show', $information->id)
            ->with('status', 'Entry deleted successfully.');
    }

    /** "coordinates" box (numbers or a Google Maps link) → latitude / longitude columns. */
    private function withCoordinates(array $data): array
    {
        [$data['latitude'], $data['longitude']] = KnowledgeBase::parseCoordinates($data['coordinates'] ?? null);
        unset($data['coordinates']);
        if ($data['latitude'] === null) {
            $data['location_name'] = null;
        }

        return $data;
    }
}
