<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    protected $fillable = [
        'user_id', 'subject', 'day_of_week', 'start_time', 'end_time', 'room', 'lecturer',
    ];

    public const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
