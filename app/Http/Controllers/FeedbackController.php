<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        Feedback::create([
            'user_name' => auth()->user()->first_name ?? 'Student',
            'feedback' => $request->feedback,
            'feature_request' => $request->feature_request,
        ]);

        return back()->with('success', 'Feedback sent successfully!');
    }

    public function inbox()
    {
        $feedbacks = Feedback::latest()->get();
        $unansweredCount = UnansweredQuestion::where('status', 'pending')->count();

        Feedback::where('is_read', false)->update(['is_read' => true]);

        return view('admin.inbox', compact('feedbacks', 'unansweredCount'));
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return response()->json(['success' => true]);
    }
}