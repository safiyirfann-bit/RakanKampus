<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Notifications\ReminderDue;
use Illuminate\Console\Command;
use Throwable;

class SendDueReminders extends Command
{
    protected $signature = 'reminders:send-due';

    protected $description = 'Send push notifications for reminders whose lead time has been reached';

    public function handle(): int
    {
        $sent = 0;
        $failed = 0;

        Reminder::with('user')
            ->whereNull('notified_at')
            ->where('due_at', '>', now())
            ->each(function (Reminder $reminder) use (&$sent, &$failed) {
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

                try {
                    $reminder->user->notify(new ReminderDue($reminder));
                    $reminder->forceFill(['notified_at' => now()])->save();
                    $sent++;
                } catch (Throwable $e) {
                    // Don't let one bad subscription (expired endpoint, missing
                    // GMP/BCMath triggering a PHP notice inside minishlink/web-push,
                    // network hiccup, etc.) crash the whole cron run with a 500.
                    // Log it and keep going so other users still get notified.
                    $failed++;
                    report($e);
                    $this->error("Failed to notify reminder #{$reminder->id}: {$e->getMessage()}");
                }
            });

        $this->info("Sent {$sent} reminder notification(s)." . ($failed ? " {$failed} failed (see logs)." : ''));

        return self::SUCCESS;
    }
}
