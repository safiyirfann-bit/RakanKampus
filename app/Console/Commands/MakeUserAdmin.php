<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'app:make-admin {email}';

    protected $description = 'Promote a user to admin by email';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->warn("No user found with email: {$this->argument('email')}");

            return self::FAILURE;
        }

        $user->update(['role' => 'admin']);

        $this->info("{$user->email} is now an admin.");

        return self::SUCCESS;
    }
}
