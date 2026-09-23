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

        // Main (first) notification for each reminder — unchanged from before.
        Reminder::with('user')
            ->whereNull('notified_at')
            ->where('due_at', '>', now())
            ->each(function (Reminder $reminder) use (&$sent, &$failed) {
                $notifyAt = $reminder->due_at->copy()->subMinutes((int) round($reminder->lead_hours * 60));

                if ($notifyAt->isFuture() || ! $reminder->user) {
                    return;
                }

                if (! $this->canNotify($reminder->user)) {
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

        // Extra "remind me many times" notifications — a reminder can have several
        // additional lead times (repeat_lead_hours) beyond the main one, e.g. remind
        // again 1 day before AND 1 hour before. notified_leads tracks which of those
        // have already fired so each one only sends once.
        Reminder::with('user')
            ->whereNotNull('repeat_lead_hours')
            ->where('due_at', '>', now())
            ->each(function (Reminder $reminder) use (&$sent, &$failed) {
                if (! $reminder->user || ! $this->canNotify($reminder->user)) {
                    return;
                }

                $alreadyNotified = $reminder->notified_leads ?? [];
                $dueMinutesLeft = 0;
                $changed = false;

                foreach ($reminder->repeat_lead_hours as $leadHours) {
                    if (in_array($leadHours, $alreadyNotified, false)) {
                        continue;
                    }

                    $notifyAt = $reminder->due_at->copy()->subMinutes((int) round($leadHours * 60));

                    if ($notifyAt->isFuture()) {
                        continue;
                    }

                    try {
                        $reminder->user->notify(new ReminderDue($reminder));
                        $alreadyNotified[] = $leadHours;
                        $changed = true;
                        $sent++;
                    } catch (Throwable $e) {
                        $failed++;
                        report($e);
                        $this->error("Failed to send repeat notification for reminder #{$reminder->id}: {$e->getMessage()}");
                    }
                }

                if ($changed) {
                    $reminder->forceFill(['notified_leads' => $alreadyNotified])->save();
                }
            });

        $this->info("Sent {$sent} reminder notification(s)." . ($failed ? " {$failed} failed (see logs)." : ''));

        return self::SUCCESS;
    }

    private function canNotify(\App\Models\User $user): bool
    {
        $dnd = $user->notification_settings['dnd_until'] ?? null;
        if ($dnd && now()->lt($dnd)) {
            return false;
        }

        return $user->pushSubscriptions()->exists();
    }
}
