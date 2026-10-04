<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    protected $fillable = [
        'user_id', 'subject', 'day_of_week', 'start_time', 'end_time', 'room', 'lecturer', 'last_notified_date', 'notify_offsets', 'notified_keys',
    ];

    protected $casts = [
        'last_notified_date' => 'date',
        'notify_offsets' => 'array',
        'notified_keys' => 'array',
    ];

    public const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    /** Default when a class has never had its alerts set: one alert 15 minutes before. */
    public const DEFAULT_NOTIFY_OFFSETS = [15];

    public const MAX_NOTIFY_OFFSETS = 3;

    /** Longest allowed alert: 7 days before. */
    public const MAX_NOTIFY_MINUTES = 10080;

    /** @return array<int, int> minutes before the class, largest first */
    public function notifyOffsets(): array
    {
        $offsets = $this->notify_offsets ?? self::DEFAULT_NOTIFY_OFFSETS;
        $offsets = array_values(array_unique(array_map('intval', (array) $offsets)));
        rsort($offsets);

        return $offsets;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
