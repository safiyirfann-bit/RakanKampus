<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Notifications for the RakanKampus Android app.
 *
 * The app is a WebView, and WebView can't receive Web Push. So instead the app asks
 * this endpoint for every reminder / class alert coming up in the next 8 days and
 * schedules them itself on the phone (Android AlarmManager). They then pop up on
 * time even when the app is closed and with no server or cron involved.
 * The app re-syncs whenever a page opens or a reminder / class is saved.
 */
class AppNotificationController extends Controller
{
    private const DAYS_AHEAD = 8;

    public function index(Request $request)
    {
        $user = $request->user();
        $now = now();
        $until = $now->copy()->addDays(self::DAYS_AHEAD);
        $settings = $user->notification_settings ?? [];
        $dnd = isset($settings['dnd_until']) ? Carbon::parse($settings['dnd_until']) : null;
        $items = [];

        $add = function (string $id, Carbon $at, string $title, string $body, string $url) use (&$items, $now, $until, $dnd) {
            if ($at->lte($now) || $at->gt($until) || ($dnd && $at->lt($dnd))) {
                return;
            }
            $items[] = ['id' => $id, 'at' => $at->getTimestampMs(), 'title' => $title, 'body' => $body, 'url' => $url];
        };

        // Profile → Notification Settings → "Reminder notifications"
        if ($settings['reminder_notifications'] ?? true) {
            $user->reminders()->where('due_at', '>', $now)->where('due_at', '<=', $until->copy()->addDays(30))->get()
                ->each(function ($r) use ($add) {
                    $leads = array_merge([(float) $r->lead_hours], array_map('floatval', (array) ($r->repeat_lead_hours ?? [])));
                    foreach (array_unique($leads) as $lead) {
                        $mins = (int) round($lead * 60);
                        $add(
                            "r{$r->id}-{$mins}",
                            $r->due_at->copy()->subMinutes($mins),
                            __(':type due in :time', ['type' => __($r->type), 'time' => $this->left($mins)]),
                            $r->subject . ' · ' . $r->due_at->translatedFormat('j M, h:i A'),
                            route('student.reminders', [], false),
                        );
                    }
                });
        }

        // Profile → Notification Settings → "Class starting soon"
        if ($settings['class_notifications'] ?? true) {
            $user->classSchedules()->get()->each(function (ClassSchedule $c) use ($add, $now) {
                if (! preg_match('/^\d{2}:\d{2}/', (string) $c->start_time)) {
                    return;
                }
                $meta = collect([$c->room, $c->lecturer])->filter()->implode(' · ');
                for ($i = 0; $i <= self::DAYS_AHEAD; $i++) {
                    $date = $now->copy()->startOfDay()->addDays($i);
                    if ($date->format('l') !== $c->day_of_week) {
                        continue;
                    }
                    $start = $date->copy()->setTimeFromTimeString(substr($c->start_time, 0, 5) . ':00');
                    foreach ($c->notifyOffsets() as $mins) {
                        $add(
                            "c{$c->id}-{$start->format('Ymd')}-{$mins}",
                            $start->copy()->subMinutes($mins),
                            __(':subject starts in :time', ['subject' => $c->subject, 'time' => $this->left($mins)]),
                            $meta !== '' ? $meta : $c->start_time . ' - ' . $c->end_time,
                            route('student.timetable', [], false),
                        );
                    }
                }
            });
        }

        usort($items, fn ($a, $b) => $a['at'] <=> $b['at']);

        return response()->json(['items' => array_slice($items, 0, 400)]);
    }

    private function left(int $m): string
    {
        if ($m < 60) {
            return $m . ' ' . __('min');
        }
        if ($m < 1440) {
            $h = intdiv($m, 60);
            $r = $m % 60;
            return $h . ' ' . __($h === 1 ? 'hour' : 'hours') . ($r ? ' ' . $r . ' ' . __('min') : '');
        }
        $d = intdiv($m, 1440);
        $h = intdiv($m % 1440, 60);
        return $d . ' ' . __($d === 1 ? 'day' : 'days') . ($h ? ' ' . $h . ' ' . __($h === 1 ? 'hour' : 'hours') : '');
    }
}
