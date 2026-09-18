<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'subject', 'type', 'due_at', 'lead_hours'];

    protected $casts = [
        'due_at' => 'datetime',
        'lead_hours' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
