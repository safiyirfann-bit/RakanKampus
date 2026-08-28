<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // Student submit feedback
    public function store(Request $request)
    {
        Feedback::create([
            'user_name' => auth()->user()->first_name ?? 'Student',
            'feedback' => $request->feedback,
            'feature_request' => $request->feature_request,
        ]);

        return back()->with('success', 'Feedback sent successfully!');
    }

    // Admin view inbox
    public function inbox()
    {
        $feedbacks = Feedback::latest()->get();

        return view('admin.inbox', compact('feedbacks'));
    }
}