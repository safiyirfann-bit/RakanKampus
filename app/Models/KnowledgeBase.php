<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'information_id',
        'intent',
        'question',
        'question_ms',
        'question_en',
        'question_zh',
        'question_ta',
        'answer',
        'category',
        'keywords',
        'location_name',
        'latitude',
        'longitude',
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

    /** The app's languages that have their own question column. */
    public const LOCALES = ['ms', 'en', 'zh', 'ta'];

    /** The question in the given language (falls back to the original question). */
    public function questionFor(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $text = in_array($locale, self::LOCALES, true) ? $this->{"question_{$locale}"} : null;

        return trim((string) ($text ?: $this->question));
    }

    /** All versions of the question, used when searching. */
    public function allQuestions(): string
    {
        return implode(' | ', array_filter([
            $this->question, $this->question_ms, $this->question_en, $this->question_zh, $this->question_ta,
        ]));
    }

    /** Map pin for the chat, or null when this entry has no location. */
    public function mapData(): ?array
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return [
            'name' => $this->location_name ?: $this->questionFor(),
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    /**
     * Read "4.5912, 101.1265" or a Google Maps link (…@4.5912,101.1265… / …?q=4.5912,101.1265)
     * into [lat, lng]. Returns [null, null] when empty or not valid.
     */
    public static function parseCoordinates(?string $text): array
    {
        if (preg_match('/(-?\d{1,2}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)/', (string) $text, $m)) {
            $lat = (float) $m[1];
            $lng = (float) $m[2];
            if (abs($lat) <= 90 && abs($lng) <= 180) {
                return [$lat, $lng];
            }
        }

        return [null, null];
    }

    public function information()
    {
        return $this->belongsTo(Information::class);
    }
}