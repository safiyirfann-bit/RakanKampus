<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_name',
        'feedback',
        'feature_request',
        'student_id',
        'issue_type',
        'issue_report',
        'is_read',
    ];
}