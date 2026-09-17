<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportAppData extends Command
{
    protected $signature = 'app:export-data {--path=migration-export}';

    protected $description = 'Export app tables to JSON files (run against the SOURCE database, e.g. local MySQL)';

    /**
     * Order matters: parents before children (matches foreign key dependencies).
     */
    private const TABLES = [
        'users',
        'information',
        'knowledge_bases',
        'profiles',
        'chat_conversations',
        'chat_messages',
        'unanswered_questions',
        'feedback',
    ];

    public function handle(): int
    {
        $path = storage_path('app/' . $this->option('path'));

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        foreach (self::TABLES as $table) {
            $rows = DB::table($table)->get();

            file_put_contents(
                "{$path}/{$table}.json",
                $rows->toJson(JSON_PRETTY_PRINT)
            );

            $this->info("Exported {$table}: {$rows->count()} rows");
        }

        $this->info("Done. Files saved to {$path}");

        return self::SUCCESS;
    }
}
