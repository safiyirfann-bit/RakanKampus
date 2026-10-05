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
            'student_id' => auth()->user()->student_id,
            'feedback' => $request->feedback,
            'feature_request' => $request->feature_request,
        ]);

        return back()->with('success', 'Feedback sent successfully!');
    }

    public const ISSUE_TYPES = ['Chatbot issue', 'Login problem', 'Profile problem', 'Reminders or timetable', 'Other'];

    /** Help & Support → "Report a problem". Shows up in Admin → Inbox under "Report issue". */
    public function reportIssue(Request $request)
    {
        $data = $request->validate([
            'issue_type' => 'required|string|in:' . implode(',', self::ISSUE_TYPES),
            'issue_report' => 'required|string|min:5|max:2000',
        ], [
            'issue_report.required' => __('Please describe the problem.'),
            'issue_report.min' => __('Please add a little more detail.'),
        ]);

        $user = $request->user();

        Feedback::create([
            'user_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name,
            'student_id' => $user->student_id,
            'issue_type' => $data['issue_type'],
            'issue_report' => $data['issue_report'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('report_sent', __('Thanks! Your report has been sent to the admin team.'));
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