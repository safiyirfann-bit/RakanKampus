<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnansweredQuestionController extends Controller
{
    public function index()
    {
        $questions = UnansweredQuestion::where('status', 'pending')
            ->orderByDesc('asked_count')
            ->latest()
            ->get();

        $topics = Information::orderBy('main_topic')->get();

        return view('admin.unanswered', compact('questions', 'topics'));
    }

    // Isi jawapan + terus tambah ke Knowledge Base + tandakan selesai (satu langkah)
    public function storeAndResolve(Request $request, UnansweredQuestion $unansweredQuestion)
    {
        $data = $request->validate([
            'information_id' => 'required|exists:information,id',
            'answer' => 'required|string',
            'keywords' => 'nullable|string|max:255',
        ]);

        $information = Information::findOrFail($data['information_id']);

        $information->knowledgeEntries()->create([
            'intent' => Str::limit($unansweredQuestion->question, 50, ''),
            'question' => $unansweredQuestion->question,
            'answer' => $data['answer'],
            'category' => $information->main_topic,
            'keywords' => $data['keywords'] ?? null,
        ]);

        $unansweredQuestion->update(['status' => 'resolved']);

        return redirect()->route('admin.unanswered.index')
            ->with('status', 'Jawapan ditambah ke knowledge base & soalan ditandakan selesai!');
    }

    // Abaikan soalan tanpa tambah ke KB (untuk soalan spam/tak relevan)
    public function resolve(UnansweredQuestion $unansweredQuestion)
    {
        $unansweredQuestion->update(['status' => 'resolved']);

        return redirect()->back()->with('status', 'Ditandakan selesai.');
    }
}