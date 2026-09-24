<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'student_id',
        'email',
        'password',
        'role',
        'photo',
        'faculty',
        'phone',
        'notification_settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_settings' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
    public function chatConversations()
{
    return $this->hasMany(ChatConversation::class);
}

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }
}