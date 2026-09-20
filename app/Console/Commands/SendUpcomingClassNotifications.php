<?php

namespace App\Console\Commands;

use App\Models\ClassSchedule;
use App\Notifications\ClassStartingSoon;
use Illuminate\Console\Command;
use Throwable;

class SendUpcomingClassNotifications extends Command
{
    protected $signature = 'classes:send-upcoming';

    protected $description = 'Send push notifications for classes starting soon today';

    /**
     * How many minutes before a class starts to send the "starting soon" push.
     */
    private const LEAD_MINUTES = 15;

    public function handle(): int
    {
        $today = now()->startOfDay();
        $todayName = now()->format('l'); // e.g. "Monday" — matches ClassSchedule::DAYS

        $sent = 0;
        $failed = 0;

        ClassSchedule::with('user')
            ->where('day_of_week', $todayName)
            ->where(function ($query) use ($today) {
                $query->whereNull('last_notified_date')
                    ->orWhere('last_notified_date', '<', $today->toDateString());
            })
            ->each(function (ClassSchedule $schedule) use ($today, &$sent, &$failed) {
                if (! $schedule->user) {
                    return;
                }

                $startAt = $today->copy()->setTimeFromTimeString($schedule->start_time.':00');
                $notifyAt = $startAt->copy()->subMinutes(self::LEAD_MINUTES);

                // Only notify once we've reached the lead window, and only while the
                // class hasn't started yet (mirrors SendDueReminders' due_at > now() guard
                // so a cron gap doesn't fire a stale "starting soon" for a class already over).
                if ($notifyAt->isFuture() || $startAt->isPast()) {
                    return;
                }

                $dnd = $schedule->user->notification_settings['dnd_until'] ?? null;
                if ($dnd && now()->lt($dnd)) {
                    return;
                }

                if ($schedule->user->pushSubscriptions()->doesntExist()) {
                    return;
                }

                $minutesLeft = max(0, (int) round(now()->diffInMinutes($startAt, false)));

                try {
                    $schedule->user->notify(new ClassStartingSoon($schedule, $minutesLeft));
                    $schedule->forceFill(['last_notified_date' => $today->toDateString()])->save();
                    $sent++;
                } catch (Throwable $e) {
                    // Same defensive handling as SendDueReminders: one bad subscription or
                    // transient failure shouldn't crash the whole cron run.
                    $failed++;
                    report($e);
                    $this->error("Failed to notify class #{$schedule->id}: {$e->getMessage()}");
                }
            });

        $this->info("Sent {$sent} class notification(s)." . ($failed ? " {$failed} failed (see logs)." : ''));

        return self::SUCCESS;
    }
}
