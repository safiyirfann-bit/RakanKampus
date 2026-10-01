<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Turns a chat answer into natural speech (MP3) so "Read aloud" sounds like a real
 * person in Malay and English — on the web and in the app.
 * Provider: ElevenLabs if ELEVENLABS_API_KEY is set, otherwise Microsoft Azure.
 * Each clip is cached by its text, so the same answer only uses quota once.
 */
class SpeechController extends Controller
{
    public const MAX_CHARS = 1500; // keeps each clip inside the free quotas

    public static function provider(): ?string
    {
        if ((string) config('services.elevenlabs.key') !== '') {
            return 'elevenlabs';
        }

        return (string) config('services.azure_tts.key') !== '' ? 'azure' : null;
    }

    public static function enabled(): bool
    {
        return self::provider() !== null;
    }

    public function speak(Request $request)
    {
        abort_unless(self::enabled(), 404);

        $data = $request->validate([
            'text' => 'required|string|max:' . (self::MAX_CHARS * 2),
            'lang' => 'nullable|in:ms-MY,en-US',
        ]);

        $text = $this->clean($data['text']);
        abort_if($text === '', 422);
        $lang = $data['lang'] ?? 'ms-MY';
        $provider = self::provider();

        $voiceKey = $provider === 'elevenlabs'
            ? config('services.elevenlabs.voice') . '|' . config('services.elevenlabs.model') . '|' . $lang
            : ($lang === 'en-US' ? config('services.azure_tts.voice_en') : config('services.azure_tts.voice_ms'));
        $path = 'tts/' . $provider . '-' . sha1($voiceKey . '|' . $text) . '.mp3';
        $disk = Storage::disk('local');

        if (! $disk->exists($path)) {
            $res = $provider === 'elevenlabs' ? $this->elevenLabs($text, $lang) : $this->azure($text, $lang);

            $type = (string) $res->header('Content-Type');
            if ($res->failed() || $res->body() === '' || str_contains($type, 'json')) {
                Log::warning("TTS ({$provider}) failed", ['status' => $res->status(), 'body' => mb_substr($res->body(), 0, 300)]);

                // Say why (e.g. ElevenLabs "detected_unusual_activity" or "paid_plan_required")
                // so it can be fixed without digging through server logs. No keys are included.
                $detail = $res->json('detail');
                $reason = is_array($detail)
                    ? trim(($detail['status'] ?? '') . ': ' . ($detail['message'] ?? ''), ': ')
                    : (is_string($detail) ? $detail : mb_substr(strip_tags($res->body()), 0, 160));

                return response()->json([
                    'error' => 'tts_failed',
                    'provider' => $provider,
                    'status' => $res->status(),
                    'reason' => mb_substr((string) $reason, 0, 240),
                ], 502);
            }
            $disk->put($path, $res->body());
        }

        return response($disk->get($path), 200, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    private function elevenLabs(string $text, string $lang)
    {
        $cfg = config('services.elevenlabs');

        return Http::withHeaders([
            'xi-api-key' => $cfg['key'],
            'Accept' => 'audio/mpeg',
        ])->timeout(30)->post(rtrim($cfg['endpoint'], '/') . '/' . $cfg['voice'] . '?output_format=mp3_44100_64', [
            'text' => $text,
            'model_id' => $cfg['model'],
            'language_code' => $lang === 'en-US' ? 'en' : 'ms',
            'voice_settings' => ['stability' => 0.5, 'similarity_boost' => 0.75, 'speed' => 0.95],
        ]);
    }

    private function azure(string $text, string $lang)
    {
        $cfg = config('services.azure_tts');
        $voice = $lang === 'en-US' ? $cfg['voice_en'] : $cfg['voice_ms'];
        $ssml = '<speak version="1.0" xmlns="http://www.w3.org/2001/10/synthesis" xml:lang="' . $lang . '">'
            . '<voice name="' . e($voice) . '"><prosody rate="-4%">' . htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</prosody></voice></speak>';
        $endpoint = $cfg['endpoint'] ?: "https://{$cfg['region']}.tts.speech.microsoft.com/cognitiveservices/v1";

        return Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $cfg['key'],
            'X-Microsoft-OutputFormat' => 'audio-24khz-48kbitrate-mono-mp3',
            'User-Agent' => 'RakanKampus',
        ])->withBody($ssml, 'application/ssml+xml')->timeout(20)->post($endpoint);
    }

    /** Strip what shouldn't be read out loud: links, emoji, markdown symbols. */
    private function clean(string $text): string
    {
        $text = preg_replace('~https?://\S+~u', '', $text);
        $text = preg_replace('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}\x{FE0F}]/u', '', $text);
        $text = preg_replace('/^\s*(\d+)[.)]\s+/mu', '$1. ', $text);
        $text = preg_replace('/[*_#|`>~]+/u', ' ', $text);
        $text = preg_replace('/[ \t]{2,}/u', ' ', $text);

        return trim(mb_substr($text, 0, self::MAX_CHARS));
    }
}
