<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Turns a chat answer into natural speech (MP3) with Microsoft Azure neural voices,
 * so "Read aloud" sounds like a real person in Malay and English — on the web and in the app.
 * Each clip is cached by its text, so the same answer is only paid for once.
 */
class SpeechController extends Controller
{
    public const MAX_CHARS = 2500;

    public static function enabled(): bool
    {
        return (string) config('services.azure_tts.key') !== '';
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
        $cfg = config('services.azure_tts');
        $voice = $lang === 'en-US' ? $cfg['voice_en'] : $cfg['voice_ms'];

        $path = 'tts/' . sha1($voice . '|' . $text) . '.mp3';
        $disk = Storage::disk('local');

        if (! $disk->exists($path)) {
            $ssml = '<speak version="1.0" xmlns="http://www.w3.org/2001/10/synthesis" xml:lang="' . $lang . '">'
                . '<voice name="' . e($voice) . '"><prosody rate="-4%">' . htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</prosody></voice></speak>';

            $endpoint = $cfg['endpoint'] ?: "https://{$cfg['region']}.tts.speech.microsoft.com/cognitiveservices/v1";
            $res = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $cfg['key'],
                'X-Microsoft-OutputFormat' => 'audio-24khz-48kbitrate-mono-mp3',
                'User-Agent' => 'RakanKampus',
            ])->withBody($ssml, 'application/ssml+xml')->timeout(20)->post($endpoint);

            if ($res->failed() || $res->body() === '') {
                Log::warning('Azure TTS failed', ['status' => $res->status(), 'body' => mb_substr($res->body(), 0, 300)]);

                return response()->json(['error' => 'tts_failed'], 502);
            }
            $disk->put($path, $res->body());
        }

        return response($disk->get($path), 200, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'private, max-age=86400',
        ]);
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
