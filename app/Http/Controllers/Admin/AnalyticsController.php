<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\UnansweredQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AnalyticsController extends Controller
{
    /** Tables shown in the summary (fixed allow-list, never taken from input). */
    private const TABLES = [
        'users',
        'profiles',
        'information',
        'knowledge_bases',
        'feedback',
        'chat_conversations',
        'chat_messages',
        'unanswered_questions',
        'reminders',
        'class_schedules',
        'push_subscriptions',
        'sessions',
    ];

    private const RANGES = [7, 30, 90];

    private const ONLINE_WINDOW_SECONDS = 300;

    public function index(Request $request)
    {
        $range = (int) $request->query('range', 30);
        if (! in_array($range, self::RANGES, true)) {
            $range = 30;
        }

        $tableFilter = $request->query('table', 'all');
        if ($tableFilter !== 'all' && ! in_array($tableFilter, self::TABLES, true)) {
            $tableFilter = 'all';
        }

        $since = now()->subDays($range)->startOfDay();
        $today = now()->startOfDay();

        // ---- Table summary -------------------------------------------------
        $summary = [];
        $totalRecords = 0;
        $updatedToday = 0;

        foreach (self::TABLES as $t) {
            if (! Schema::hasTable($t)) {
                continue;
            }

            $cols = Schema::getColumnListing($t);
            $hasCreated = in_array('created_at', $cols, true);
            $hasUpdated = in_array('updated_at', $cols, true);

            $row = [
                'table' => $t,
                'rows' => DB::table($t)->count(),
                'first' => $hasCreated ? DB::table($t)->min('created_at') : null,
                'last_created' => $hasCreated ? DB::table($t)->max('created_at') : null,
                'last_updated' => $hasUpdated ? DB::table($t)->max('updated_at') : null,
                'added' => $hasCreated ? DB::table($t)->where('created_at', '>=', $since)->count() : null,
                'updated_today' => $hasUpdated ? DB::table($t)->where('updated_at', '>=', $today)->count() : 0,
            ];

            $totalRecords += $row['rows'];
            $updatedToday += $row['updated_today'];
            $summary[] = $row;
        }

        $visibleSummary = $tableFilter === 'all'
            ? $summary
            : array_values(array_filter($summary, fn ($r) => $r['table'] === $tableFilter));

        // ---- Users ---------------------------------------------------------
        $totalUsers = DB::table('users')->count();
        $newThisWeek = DB::table('users')->where('created_at', '>=', now()->subDays(7))->count();

        $onlineNow = 0;
        if (Schema::hasTable('sessions')) {
            $onlineNow = DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', now()->subSeconds(self::ONLINE_WINDOW_SECONDS)->timestamp)
                ->distinct('user_id')
                ->count('user_id');
        }

        // ---- Activity by hour + heatmap -----------------------------------
        $byHour = array_fill(0, 24, 0);
        $heatmap = array_fill(1, 7, array_fill(0, 24, 0)); // 1 = Mon … 7 = Sun

        if (Schema::hasTable('user_activity_logs')) {
            DB::table('user_activity_logs')
                ->where('active_at', '>=', $since)
                ->orderBy('id')
                ->select('id', 'active_at')
                ->chunk(2000, function ($rows) use (&$byHour, &$heatmap) {
                    foreach ($rows as $r) {
                        $ts = strtotime((string) $r->active_at);
                        $h = (int) date('G', $ts);
                        $d = (int) date('N', $ts);
                        $byHour[$h]++;
                        $heatmap[$d][$h]++;
                    }
                });
        }

        // ---- Most asked topics --------------------------------------------
        $topics = [];
        if (Schema::hasColumn('chat_messages', 'knowledge_base_id')) {
            $topics = DB::table('chat_messages')
                ->join('knowledge_bases', 'knowledge_bases.id', '=', 'chat_messages.knowledge_base_id')
                ->leftJoin('information', 'information.id', '=', 'knowledge_bases.information_id')
                ->where('chat_messages.sender', 'user')
                ->where('chat_messages.created_at', '>=', $since)
                ->groupBy('information.main_topic')
                ->selectRaw('information.main_topic as name, COUNT(*) as count')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->map(fn ($r) => ['name' => $r->name ?: 'Other', 'count' => (int) $r->count])
                ->all();
        }

        $unansweredAsked = Schema::hasTable('unanswered_questions')
            ? (int) DB::table('unanswered_questions')->where('updated_at', '>=', $since)->sum('asked_count')
            : 0;

        $peakHour = max($byHour) > 0 ? array_search(max($byHour), $byHour, true) : null;
        $heatMax = max(array_map('max', $heatmap));

        return view('admin.analytics', [
            'range' => $range,
            'ranges' => self::RANGES,
            'tableFilter' => $tableFilter,
            'tables' => array_column($summary, 'table'),
            'summary' => $visibleSummary,
            'totalRecords' => $totalRecords,
            'tableCount' => count($summary),
            'updatedToday' => $updatedToday,
            'totalUsers' => $totalUsers,
            'newThisWeek' => $newThisWeek,
            'onlineNow' => $onlineNow,
            'byHour' => $byHour,
            'peakHour' => $peakHour,
            'heatmap' => $heatmap,
            'heatMax' => $heatMax,
            'topics' => $topics,
            'unansweredAsked' => $unansweredAsked,
            'unansweredCount' => UnansweredQuestion::where('status', 'pending')->count(),
            'unreadFeedbackCount' => Feedback::where('is_read', false)->count(),
        ]);
    }
}
