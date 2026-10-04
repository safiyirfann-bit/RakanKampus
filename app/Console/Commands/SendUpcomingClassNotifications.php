<?php

namespace App\Console\Commands;

use App\Models\ClassSchedule;
use App\Notifications\ClassStartingSoon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class SendUpcomingClassNotifications extends Command
{
    protected $signature = 'classes:send-upcoming';

    protected $description = 'Send the "Notify me" push alerts for upcoming classes';

    /**
     * How long after an alert's time it may still go out (covers cron gaps on hosts
     * like Render). Past this, a missed long-range alert is skipped instead of firing
     * late, e.g. an "18 hours before" alert won't arrive 2 hours before class.
     */
    private const GRACE_MINUTES = 30;

    public function handle(): int
    {
        $now = now();
        $sent = 0;
        $failed = 0;

        ClassSchedule::with('user')->each(function (ClassSchedule $schedule) use ($now, &$sent, &$failed) {
            $user = $schedule->user;
            if (! $user) {
                return;
            }

            $due = $this->dueAlerts($schedule, $now);
            if ($due === []) {
                return;
            }

            // Profile → Notification Settings → "Class starting soon"
            $settings = $user->notification_settings ?? [];
            $dnd = $settings['dnd_until'] ?? null;
            $muted = ! ($settings['class_notifications'] ?? true)
                || ($dnd && $now->lt($dnd))
                || $user->pushSubscriptions()->doesntExist();

            $keys = $this->recentKeys($schedule, $now);

            // Several alerts due in the same run (e.g. after a cron gap): send just the
            // one closest to the class, and mark the rest as handled.
            $send = $muted ? null : end($due);

            try {
                if ($send) {
                    $user->notify(new ClassStartingSoon($schedule, max(0, (int) round($now->diffInMinutes($send['start'], false)))));
                    $sent++;
                }
                foreach ($due as $alert) {
                    $keys[] = $alert['key'];
                }
                $schedule->forceFill(['notified_keys' => array_values(array_unique($keys))])->save();
            } catch (Throwable $e) {
                // One bad subscription or transient failure shouldn't crash the whole cron run.
                $failed++;
                report($e);
                $this->error("Failed to notify class #{$schedule->id}: {$e->getMessage()}");
            }
        });

        $this->info("Sent {$sent} class notification(s)." . ($failed ? " {$failed} failed (see logs)." : ''));

        return self::SUCCESS;
    }

    /**
     * Alerts of this class whose time has come (within the grace window), whose class
     * hasn't started yet, and that haven't been sent. Ordered furthest-from-class first.
     *
     * @return array<int, array{key: string, start: Carbon}>
     */
    private function dueAlerts(ClassSchedule $schedule, Carbon $now): array
    {
        $offsets = $schedule->notifyOffsets();
        if ($offsets === [] || ! preg_match('/^\d{2}:\d{2}/', (string) $schedule->start_time)) {
            return [];
        }

        $sentKeys = (array) ($schedule->notified_keys ?? []);
        $due = [];

        // The next class within the longest alert window (alerts go up to 7 days ahead).
        for ($i = 0; $i <= 7; $i++) {
            $date = $now->copy()->startOfDay()->addDays($i);
            if ($date->format('l') !== $schedule->day_of_week) {
                continue;
            }

            $start = $date->copy()->setTimeFromTimeString(substr($schedule->start_time, 0, 5) . ':00');
            if ($start->lte($now)) {
                continue;
            }

            foreach ($offsets as $minutes) {
                $notifyAt = $start->copy()->subMinutes($minutes);
                $key = $start->toDateString() . '|' . $minutes;

                if ($notifyAt->gt($now) || $notifyAt->lt($now->copy()->subMinutes(self::GRACE_MINUTES)) || in_array($key, $sentKeys, true)) {
                    continue;
                }

                $due[] = ['key' => $key, 'start' => $start];
            }
        }

        return $due;
    }

    /** Sent-alert keys still worth remembering (classes from yesterday onwards). */
    private function recentKeys(ClassSchedule $schedule, Carbon $now): array
    {
        $cutoff = $now->copy()->subDay()->toDateString();

        return array_values(array_filter(
            (array) ($schedule->notified_keys ?? []),
            fn ($k) => is_string($k) && substr($k, 0, 10) >= $cutoff
        ));
    }
}
