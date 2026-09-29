<?php

namespace App\Http\Controllers;

use App\Models\Information;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $unansweredCount = UnansweredQuestion::where('status', 'pending')->count();
        $unreadFeedbackCount = \App\Models\Feedback::where('is_read', false)->count();

        // Numbers for the green hero banner
        $students = \App\Models\User::where('role', 'student');
        $studentCount = (clone $students)->count();
        $newStudents = (clone $students)->where('created_at', '>=', now()->subDays(7))->count();
        $userQuestions = \App\Models\ChatMessage::where('sender', 'user');
        $questionsAsked = (clone $userQuestions)->count();
        $askedThisWeek = (clone $userQuestions)->where('created_at', '>=', now()->subDays(7))->count();
        $askedLastWeek = (clone $userQuestions)->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        $missedAsks = (int) UnansweredQuestion::sum('asked_count');
        $answeredRate = $questionsAsked > 0 ? max(0, min(100, (int) round((1 - $missedAsks / $questionsAsked) * 100))) : null;

        // Questions asked per day, last 30 days (the chart switches between 7 / 14 / 30)
        $perDay = (clone $userQuestions)->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->get(['created_at'])
            ->groupBy(fn ($m) => $m->created_at->toDateString())
            ->map->count();
        $dailyQuestions = collect(range(29, 0))->map(function ($d) use ($perDay) {
            $day = now()->subDays($d);

            return ['d' => $day->format('j M'), 'n' => (int) ($perDay[$day->toDateString()] ?? 0)];
        })->all();

        // Topics the bot answered from most this week
        $usage = \App\Support\TopicLook::usage(now()->subDays(7));
        $topTopics = Information::whereIn('id', $usage->keys())->get(['id', 'main_topic'])
            ->map(fn ($i) => ['id' => $i->id, 'name' => $i->main_topic, 'n' => $usage[$i->id]])
            ->sortByDesc('n')->take(5)->values();

        // What needs the admin now
        $pendingQuestions = UnansweredQuestion::where('status', 'pending')->orderByDesc('asked_count')->orderByDesc('updated_at')->take(3)->get();
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
            'unansweredCount', 'unreadFeedbackCount',
            'studentCount', 'newStudents', 'questionsAsked', 'askedThisWeek', 'askedLastWeek', 'answeredRate',
            'dailyQuestions', 'topTopics', 'pendingQuestions', 'latestFeedback', 'onlineToday'
        ));
    }

    /** Knowledge base: every topic in one table */
    public function knowledge()
    {
        $informations = Information::withCount('knowledgeEntries')->latest('updated_at')->get();
        $usage = \App\Support\TopicLook::usage(now()->subDays(7));
        $usageLastWeek = \App\Support\TopicLook::usage(now()->subDays(14), now()->subDays(7));

        return view('admin.knowledge', [
            'informations' => $informations,
            'usage' => $usage,
            'usedThisWeek' => (int) $usage->sum(),
            'usedLastWeek' => (int) $usageLastWeek->sum(),
            'entryCount' => (int) $informations->sum('knowledge_entries_count'),
            'unansweredCount' => UnansweredQuestion::where('status', 'pending')->count(),
            'unreadFeedbackCount' => \App\Models\Feedback::where('is_read', false)->count(),
        ]);
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

        return redirect()->route('admin.knowledge')->with('status', 'Topic added. Open it to add its Q&A.');
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

        if ($request->input('back') === 'topic') {
            return redirect()->route('admin.information.show', $information->id)
                ->with('status', 'Topic updated successfully.');
        }

        return redirect()->route('admin.knowledge')
            ->with('status', 'Topic updated successfully.');
    }

    public function destroy(Information $information)
    {
        $information->delete();

        return redirect()->route('admin.knowledge')
            ->with('status', 'Topic deleted.');
    }
}