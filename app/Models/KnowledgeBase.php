<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'information_id',
        'intent',
        'question',
        'answer',
        'category',
        'keywords',
    ];

    public function information()
    {
        return $this->belongsTo(Information::class);
    }
}