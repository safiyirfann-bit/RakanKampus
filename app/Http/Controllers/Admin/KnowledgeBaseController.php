<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function show(Information $information)
    {
        $entries = $information->knowledgeEntries()->latest()->get();

        return view('admin.knowledge-detail', [
            'information' => $information,
            'entries' => $entries,
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
        ]);

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
        ]);

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
}