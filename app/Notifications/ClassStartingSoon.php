<?php

namespace App\Notifications;

use App\Models\ClassSchedule;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ClassStartingSoon extends Notification
{
    public function __construct(private ClassSchedule $schedule, private int $minutesLeft)
    {
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $m = $this->minutesLeft;
        $left = match (true) {
            $m <= 1 => 'now',
            $m < 60 => "{$m} min",
            $m < 1440 => ($h = intdiv($m + 30, 60)) . ' ' . ($h === 1 ? 'hour' : 'hours'),
            default => ($d = intdiv($m + 720, 1440)) . ' ' . ($d === 1 ? 'day' : 'days'),
        };

        $meta = collect([$this->schedule->room, $this->schedule->lecturer])
            ->filter()
            ->implode(' · ');

        return (new WebPushMessage)
            ->title($this->schedule->subject.' starts in '.$left)
            ->body($meta !== '' ? $meta : $this->schedule->start_time.' - '.$this->schedule->end_time)
            ->tag('class-'.$this->schedule->id)
            ->data(['url' => route('student.timetable', [], false)]);
    }
}
