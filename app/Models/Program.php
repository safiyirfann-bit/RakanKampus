<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A one-off programme on the timetable that runs for one or more days (not weekly). */
class Program extends Model
{
    public const COLORS = ['amber', 'violet', 'pink', 'green', 'blue'];

    /** Longest programme allowed (e.g. a full orientation fortnight). */
    public const MAX_DAYS = 31;

    protected $fillable = ['user_id', 'title', 'start_date', 'end_date', 'all_day', 'start_time', 'end_time', 'place', 'color'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'all_day' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toRaw(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date->toDateString(),
            'all_day' => $this->all_day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'place' => $this->place,
            'color' => $this->color,
        ];
    }
}
