<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    ];

    private const HIDDEN_COLUMNS = [
        'users' => ['password', 'remember_token'],
    ];

    public function index(Request $request)
    {
        $table = $request->query('table', 'users');

        if (! in_array($table, self::TABLES, true)) {
            $table = 'users';
        }

        $columns = Schema::getColumnListing($table);
        $hidden = self::HIDDEN_COLUMNS[$table] ?? [];
        $visibleColumns = array_values(array_diff($columns, $hidden));

        $rows = DB::table($table)->orderByDesc('id')->paginate(50);

        return view('admin.database', [
            'tables' => self::TABLES,
            'table' => $table,
            'columns' => $visibleColumns,
            'rows' => $rows,
        ]);
    }
}
