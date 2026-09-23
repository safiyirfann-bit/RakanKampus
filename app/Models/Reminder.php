<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'subject', 'type', 'due_at', 'lead_hours', 'repeat_lead_hours', 'notified_at', 'notified_leads'];

    protected $casts = [
        'due_at' => 'datetime',
        'notified_at' => 'datetime',
        'lead_hours' => 'float',
        'repeat_lead_hours' => 'array',
        'notified_leads' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
