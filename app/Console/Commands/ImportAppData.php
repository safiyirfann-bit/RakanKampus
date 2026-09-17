<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAppData extends Command
{
    protected $signature = 'app:import-data {--path=migration-export} {--truncate}';

    protected $description = 'Import JSON files produced by app:export-data into the DESTINATION database (e.g. Render Postgres)';

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

    private const BOOLEAN_COLUMNS = [
        'users' => ['is_admin'],
        'feedback' => ['is_read'],
    ];

    public function handle(): int
    {
        $path = storage_path('app/' . $this->option('path'));

        if (! is_dir($path)) {
            $this->error("Path not found: {$path}. Copy the exported JSON files there first.");

            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            foreach (array_reverse(self::TABLES) as $table) {
                DB::table($table)->delete();
                $this->info("Cleared {$table}");
            }
        }

        foreach (self::TABLES as $table) {
            $file = "{$path}/{$table}.json";

            if (! file_exists($file)) {
                $this->warn("Skipping {$table}: {$file} not found");
                continue;
            }

            $rows = json_decode(file_get_contents($file), true);

            foreach (array_chunk($rows, 200) as $chunk) {
                foreach ($chunk as &$row) {
                    foreach (self::BOOLEAN_COLUMNS[$table] ?? [] as $column) {
                        if (array_key_exists($column, $row)) {
                            $row[$column] = (bool) $row[$column];
                        }
                    }
                }
                unset($row);

                DB::table($table)->insert($chunk);
            }

            if (DB::connection()->getDriverName() === 'pgsql') {
                DB::statement(
                    "SELECT setval(pg_get_serial_sequence('{$table}', 'id'), COALESCE((SELECT MAX(id) FROM {$table}), 1))"
                );
            }

            $this->info('Imported ' . $table . ': ' . count($rows) . ' rows');
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
