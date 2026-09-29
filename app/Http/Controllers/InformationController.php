<?php

namespace App\Http\Controllers;

use App\Models\Information;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $informations = Information::withCount('knowledgeEntries')->latest('updated_at')->get();
        $unansweredCount = UnansweredQuestion::where('status', 'pending')->count();
        $unreadFeedbackCount = \App\Models\Feedback::where('is_read', false)->count();

        // Numbers for the green hero banner
        $students = \App\Models\User::where('role', 'student');
        $studentCount = (clone $students)->count();
        $newStudents = (clone $students)->where('created_at', '>=', now()->subDays(7))->count();
        $signupSpark = collect(range(7, 0))->map(fn ($d) => (clone $students)->whereDate('created_at', now()->subDays($d)->toDateString())->count())->all();
        $questionsAsked = \App\Models\ChatMessage::where('sender', 'user')->count();
        $entryCount = \App\Models\KnowledgeBase::count();
        $missedAsks = (int) UnansweredQuestion::sum('asked_count');
        $answeredRate = $questionsAsked > 0 ? max(0, min(100, (int) round((1 - $missedAsks / $questionsAsked) * 100))) : null;

        // Right-hand column: what needs the admin now
        $pendingQuestions = UnansweredQuestion::where('status', 'pending')->orderByDesc('updated_at')->take(3)->get();
        $latestFeedback = \App\Models\Feedback::latest()->take(3)->get();

        // Students online today, by hour (8 am – 10 pm) from the hourly activity log
        $hours = range(8, 22);
        $byHour = \Illuminate\Support\Facades\DB::table('user_activity_logs')
            ->whereDate('active_at', now()->toDateString())
            ->get(['user_id', 'active_at'])
            ->groupBy(fn ($r) => (int) \Illuminate\Support\Carbon::parse($r->active_at)->format('G'))
            ->map(fn ($rows) => $rows->pluck('user_id')->unique()->count());
        $onlineToday = collect($hours)->mapWithKeys(fn ($h) => [$h => (int) ($byHour[$h] ?? 0)])->all();

        return view('admin.dashboard', compact(
            'informations', 'unansweredCount', 'unreadFeedbackCount',
            'studentCount', 'newStudents', 'signupSpark', 'questionsAsked', 'entryCount', 'answeredRate',
            'pendingQuestions', 'latestFeedback', 'onlineToday'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'main_topic' => 'required',
            'description' => 'required',
        ]);

        Information::create([
            'main_topic' => $request->main_topic,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('status', 'Information added successfully!');
    }

    public function update(Request $request, Information $information)
    {
        $request->validate([
            'main_topic' => 'required',
            'description' => 'required',
        ]);

        $information->update([
            'main_topic' => $request->main_topic,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('status', 'Information updated successfully!');
    }

    public function destroy(Information $information)
    {
        $information->delete();

        return redirect()->route('admin.dashboard')
            ->with('status', 'Information deleted successfully!');
    }
}