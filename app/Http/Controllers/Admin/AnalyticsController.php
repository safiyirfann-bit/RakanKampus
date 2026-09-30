<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * How students use RakanKampus: students, who's online, questions per day,
 * busiest hours and most asked topics. (Database internals are not shown.)
 */
class AnalyticsController extends Controller
{
    private const RANGES = [7, 30, 90];

    private const ONLINE_WINDOW_SECONDS = 300;

    public function index(Request $request)
    {
        $range = (int) $request->query('range', 30);
        if (! in_array($range, self::RANGES, true)) {
            $range = 30;
        }

        $since = now()->subDays($range - 1)->startOfDay();
        $prevSince = now()->subDays($range * 2 - 1)->startOfDay();

        // ---- Students ------------------------------------------------------
        $students = DB::table('users')->where('role', 'student');
        $totalStudents = (clone $students)->count();
        $newStudents = (clone $students)->where('created_at', '>=', $since)->count();

        // Last activity per user (from sessions) — for "online now" and the user lists.
        $lastActive = [];
        if (Schema::hasTable('sessions')) {
            $lastActive = DB::table('sessions')
                ->whereNotNull('user_id')
                ->selectRaw('user_id, MAX(last_activity) as last_activity')
                ->groupBy('user_id')
                ->pluck('last_activity', 'user_id')
                ->all();
        }
        $cutoff = now()->subSeconds(self::ONLINE_WINDOW_SECONDS)->timestamp;

        $userList = DB::table('users')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'email', 'student_id', 'role', 'created_at'])
            ->map(function ($u) use ($lastActive, $cutoff) {
                $ts = $lastActive[$u->id] ?? null;
                $u->last_active = $ts ? Carbon::createFromTimestamp($ts, config('app.timezone')) : null;
                $u->online = $ts !== null && $ts >= $cutoff;

                return $u;
            });
        $onlineUsers = $userList->where('online', true)->sortByDesc(fn ($u) => $u->last_active)->values();

        // ---- Questions -----------------------------------------------------
        $questions = DB::table('chat_messages')->where('sender', 'user');
        $asked = (clone $questions)->where('created_at', '>=', $since)->count();
        $askedPrev = (clone $questions)->whereBetween('created_at', [$prevSince, $since])->count();
        $missed = Schema::hasTable('unanswered_questions')
            ? (int) DB::table('unanswered_questions')->where('updated_at', '>=', $since)->sum('asked_count')
            : 0;
        $answeredRate = $asked > 0 ? max(0, min(100, (int) round((1 - $missed / $asked) * 100))) : null;

        // ---- Per day: questions + active students ------------------------------
        $qPerDay = (clone $questions)->where('created_at', '>=', $since)->get(['created_at'])
            ->groupBy(fn ($m) => Carbon::parse($m->created_at)->toDateString())->map->count();
        $activePerDay = collect();
        $byHour = array_fill(0, 24, 0);
        if (Schema::hasTable('user_activity_logs')) {
            $logs = DB::table('user_activity_logs')->where('active_at', '>=', $since)->get(['user_id', 'active_at']);
            $activePerDay = $logs->groupBy(fn ($r) => Carbon::parse($r->active_at)->toDateString())
                ->map(fn ($rows) => $rows->pluck('user_id')->unique()->count());
            foreach ($logs as $r) {
                $byHour[(int) Carbon::parse($r->active_at)->format('G')]++;
            }
        }
        $daily = collect(range($range - 1, 0))->map(function ($d) use ($qPerDay, $activePerDay) {
            $day = now()->subDays($d);
            $key = $day->toDateString();

            return ['d' => $day->format('j M'), 'q' => (int) ($qPerDay[$key] ?? 0), 'a' => (int) ($activePerDay[$key] ?? 0)];
        })->values()->all();
        $peakHour = max($byHour) > 0 ? array_search(max($byHour), $byHour, true) : null;

        // ---- Most asked topics ---------------------------------------------
        $topics = [];
        if (Schema::hasColumn('chat_messages', 'knowledge_base_id')) {
            $topics = DB::table('chat_messages')
                ->join('knowledge_bases', 'knowledge_bases.id', '=', 'chat_messages.knowledge_base_id')
                ->leftJoin('information', 'information.id', '=', 'knowledge_bases.information_id')
                ->where('chat_messages.created_at', '>=', $since)
                ->groupBy('information.id', 'information.main_topic')
                ->selectRaw('information.id as id, information.main_topic as name, COUNT(*) as count')
                ->orderByDesc('count')
                ->limit(6)
                ->get()
                ->map(fn ($r) => ['id' => $r->id, 'name' => $r->name ?: 'Other', 'count' => (int) $r->count])
                ->all();
        }

        return view('admin.analytics', [
            'range' => $range,
            'ranges' => self::RANGES,
            'totalStudents' => $totalStudents,
            'newStudents' => $newStudents,
            'onlineNow' => $onlineUsers->count(),
            'userList' => $userList,
            'onlineUsers' => $onlineUsers,
            'recentUsers' => $userList->where('role', '!=', 'admin')->sortByDesc(fn ($u) => $u->last_active?->timestamp ?? 0)->take(6)->values(),
            'asked' => $asked,
            'askedPrev' => $askedPrev,
            'answeredRate' => $answeredRate,
            'missed' => $missed,
            'daily' => $daily,
            'byHour' => $byHour,
            'peakHour' => $peakHour,
            'topics' => $topics,
            'unansweredCount' => UnansweredQuestion::where('status', 'pending')->count(),
            'unreadFeedbackCount' => Feedback::where('is_read', false)->count(),
        ]);
    }
}
