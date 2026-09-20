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
        $left = $this->minutesLeft <= 1 ? 'now' : "{$this->minutesLeft} min";

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
