<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseViewerController extends Controller
{
    /**
     * Read-only viewer for a fixed allow-list of app tables. No raw SQL
     * input is ever taken from the request — the table name is matched
     * against this list, never interpolated from user input.
     */
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
    ];

    private const HIDDEN_COLUMNS = [
        'users' => ['password', 'remember_token'],
        'push_subscriptions' => ['public_key', 'auth_token'],
    ];

    public function index(Request $request)
    {
        $table = $request->query('table', 'users');

        if (! in_array($table, self::TABLES, true) || ! Schema::hasTable($table)) {
            $table = 'users';
        }

        $columns = Schema::getColumnListing($table);
        $hidden = self::HIDDEN_COLUMNS[$table] ?? [];
        $visibleColumns = array_values(array_diff($columns, $hidden));
        $hasId = in_array('id', $columns, true);

        $orderColumn = $hasId ? 'id' : $columns[0];

        $rows = DB::table($table)->orderByDesc($orderColumn)->paginate(50);

        $counts = [];
        foreach (self::TABLES as $t) {
            if (Schema::hasTable($t)) {
                $counts[$t] = DB::table($t)->count();
            }
        }

        return view('admin.database', [
            'tables' => self::TABLES,
            'table' => $table,
            'columns' => $visibleColumns,
            'rows' => $rows,
            'counts' => $counts,
            'hasId' => $hasId,
        ]);
    }

    /**
     * Delete a single row from an allow-listed table by its id. Table and
     * id are both validated before touching the database — the table name
     * is never taken as free text, and an admin can never delete their own
     * user row (which would break their own session mid-request).
     */
    public function destroy(Request $request, string $table, int $id)
    {
        if (! in_array($table, self::TABLES, true) || ! Schema::hasTable($table)) {
            abort(404);
        }

        if (! in_array('id', Schema::getColumnListing($table), true)) {
            abort(404);
        }

        if ($table === 'users' && $id === Auth::id()) {
            return back()->with('db_viewer_error', 'Tak boleh delete akaun admin yang sedang log masuk.');
        }

        $deleted = DB::table($table)->where('id', $id)->delete();

        return redirect()
            ->route('admin.database', ['table' => $table])
            ->with('db_viewer_status', $deleted ? 'Rekod #'.$id.' dah dipadam.' : 'Rekod #'.$id.' tak dijumpai.');
    }
}
