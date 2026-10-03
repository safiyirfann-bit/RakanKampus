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

    /** Small-talk categories from sapaan.csv: the bot can reply to them, but they are never suggested as questions. */
    public const SMALL_TALK_CATEGORIES = ['Greeting', 'Thanks', 'Goodbye', 'Profanity', 'Acknowledge', 'Sorry'];

    /** Only entries that make sense as a suggested / sample question. */
    public function scopeSuggestable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('category')->orWhereNotIn('category', self::SMALL_TALK_CATEGORIES);
        });
    }

    public function information()
    {
        return $this->belongsTo(Information::class);
    }
}