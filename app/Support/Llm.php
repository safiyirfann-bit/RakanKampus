<?php

namespace App\Support;

/**
 * Which AI the app talks to (chat answers, search rewrite, marquee translation,
 * and photo reading when there is no Gemini key).
 *
 * OPENAI_API_KEY set  → OpenAI (default model gpt-4.1-mini, change with OPENAI_MODEL)
 * otherwise           → Groq (GROQ_API_KEY), as before
 *
 * Both speak the same "chat completions" format, so every call site just asks this
 * class for the URL, key and model and sends the same request.
 */
class Llm
{
    public static function usingOpenAi(): bool
    {
        return (string) config('services.openai.key') !== '';
    }

    public static function key(): string
    {
        return (string) (self::usingOpenAi() ? config('services.openai.key') : config('services.groq.key'));
    }

    public static function configured(): bool
    {
        return self::key() !== '';
    }

    public static function url(): string
    {
        return self::usingOpenAi()
            ? 'https://api.openai.com/v1/chat/completions'
            : 'https://api.groq.com/openai/v1/chat/completions';
    }

    /**
     * @param string $purpose 'chat' (student answers), 'small' (quick rewrite / translate), 'vision' (read a photo)
     */
    public static function model(string $purpose = 'chat'): string
    {
        if (self::usingOpenAi()) {
            return match ($purpose) {
                'small' => (string) config('services.openai.small_model'),
                'vision' => (string) config('services.openai.vision_model'),
                default => (string) config('services.openai.model'),
            };
        }

        return match ($purpose) {
            'small' => 'openai/gpt-oss-20b',
            'vision' => 'qwen/qwen3.8-27b',
            default => 'openai/gpt-oss-120b',
        };
    }

    /** Groq-only request options (reasoning settings) are dropped when talking to OpenAI. */
    public static function payload(array $payload): array
    {
        if (self::usingOpenAi()) {
            unset($payload['reasoning_effort'], $payload['reasoning_format']);
        }

        return $payload;
    }

    public static function name(): string
    {
        return self::usingOpenAi() ? 'OpenAI' : 'Groq';
    }
}
