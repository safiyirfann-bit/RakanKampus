<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnansweredQuestion extends Model
{
    protected $fillable = ['user_id', 'question', 'asked_count', 'status'];
}