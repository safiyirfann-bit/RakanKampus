<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnansweredQuestionController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'newest');
        if (! in_array($sort, ['newest', 'oldest', 'most_asked'], true)) {
            $sort = 'newest';
        }

        $questions = UnansweredQuestion::where('status', 'pending')
            ->when($sort === 'newest', fn ($q) => $q->latest())
            ->when($sort === 'oldest', fn ($q) => $q->oldest())
            ->when($sort === 'most_asked', fn ($q) => $q->orderByDesc('asked_count')->latest())
            ->get();

        $history = UnansweredQuestion::where('status', 'resolved')
            ->latest('updated_at')
            ->take(50)
            ->get();

        $topics = Information::orderBy('main_topic')->get();

        return view('admin.unanswered', compact('questions', 'history', 'topics', 'sort'));
    }

    public function storeAndResolve(Request $request, UnansweredQuestion $unansweredQuestion)
    {
        $data = $request->validate([
            'information_id' => 'required|exists:information,id',
            'intent' => 'nullable|string|max:255',
            'question' => 'nullable|string|max:255',
            'answer' => 'required|string',
            'keywords' => 'nullable|string|max:255',
        ]);

        $information = Information::findOrFail($data['information_id']);

        $information->knowledgeEntries()->create([
            'intent' => $data['intent'] ?: Str::limit($unansweredQuestion->question, 50, ''),
            'question' => $data['question'] ?: $unansweredQuestion->question,
            'answer' => $data['answer'],
            'category' => $information->main_topic,
            'keywords' => $data['keywords'] ?? null,
        ]);

        $unansweredQuestion->update([
            'status' => 'resolved',
            'resolution' => 'answered',
        ]);

        return redirect()->route('admin.unanswered.index')
            ->with('status', 'Jawapan ditambah ke knowledge base & soalan ditandakan selesai!');
    }

    public function resolve(UnansweredQuestion $unansweredQuestion)
    {
        $unansweredQuestion->update([
            'status' => 'resolved',
            'resolution' => 'ignored',
        ]);

        return redirect()->back()->with('status', 'Ditandakan selesai.');
    }
}