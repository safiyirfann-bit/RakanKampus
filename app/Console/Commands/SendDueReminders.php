<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Notifications\ReminderDue;
use Illuminate\Console\Command;

class SendDueReminders extends Command
{
    protected $signature = 'reminders:send-due';

    protected $description = 'Send push notifications for reminders whose lead time has been reached';

    public function handle(): int
    {
        $sent = 0;

        Reminder::with('user')
            ->whereNull('notified_at')
            ->where('due_at', '>', now())
            ->each(function (Reminder $reminder) use (&$sent) {
                $notifyAt = $reminder->due_at->copy()->subMinutes((int) round($reminder->lead_hours * 60));

                if ($notifyAt->isFuture() || ! $reminder->user) {
                    return;
                }

                $dnd = $reminder->user->notification_settings['dnd_until'] ?? null;
                if ($dnd && now()->lt($dnd)) {
                    return;
                }

                if ($reminder->user->pushSubscriptions()->doesntExist()) {
                    return;
                }

                $reminder->user->notify(new ReminderDue($reminder));
                $reminder->forceFill(['notified_at' => now()])->save();
                $sent++;
            });

        $this->info("Sent {$sent} reminder notification(s).");

        return self::SUCCESS;
    }
}
