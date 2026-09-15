<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedUsersFromSecret extends Command
{
    protected $signature = 'app:seed-users-secret {--path=/etc/secrets/users-seed.json}';

    protected $description = 'Import users from a JSON file (Render Secret File) if present; skips users whose email already exists';

    public function handle(): int
    {
        $path = $this->option('path');

        if (! file_exists($path)) {
            $this->info("No secret file at {$path}, skipping.");

            return self::SUCCESS;
        }

        $users = json_decode(file_get_contents($path), true);

        $imported = 0;

        foreach ($users as $user) {
            if (DB::table('users')->where('email', $user['email'])->exists()) {
                continue;
            }

            DB::table('users')->insert([
                'id' => $user['id'],
                'name' => $user['name'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'faculty' => $user['faculty'],
                'phone' => $user['phone'],
                'student_id' => $user['student_id'],
                'password' => $user['password'],
                'is_admin' => (bool) $user['is_admin'],
                'role' => $user['role'],
                'photo' => $user['photo'],
                'notification_settings' => $user['notification_settings'],
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
            ]);

            $imported++;
        }

        if ($imported > 0 && DB::connection()->getDriverName() === 'pgsql') {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE((SELECT MAX(id) FROM users), 1))"
            );
        }

        $this->info("Imported {$imported} user(s) from secret file.");

        return self::SUCCESS;
    }
}
