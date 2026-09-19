<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ReminderDue extends Notification
{
    public function __construct(private Reminder $reminder)
    {
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $minutes = max(1, (int) round(now()->diffInMinutes($this->reminder->due_at, false)));
        $left = $minutes >= 60
            ? intdiv($minutes, 60).'h '.($minutes % 60).'m'
            : $minutes.' min';

        return (new WebPushMessage)
            ->title($this->reminder->type.' due in '.$left)
            ->body($this->reminder->subject.' · '.$this->reminder->due_at->format('j M, h:i A'))
            ->tag('reminder-'.$this->reminder->id)
            ->data(['url' => route('student.reminders', [], false)]);
    }
}
