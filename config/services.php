<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'groq' => [
    'key' => env('GROQ_API_KEY'),
],

    // "Read aloud" in the chat: Microsoft Azure neural voices (free tier F0 = 500k characters/month).
    // Leave the key empty and the chat falls back to the browser's own voice.
    'azure_tts' => [
        'key' => env('AZURE_SPEECH_KEY', ''),
        'region' => env('AZURE_SPEECH_REGION', 'southeastasia'),
        'voice_ms' => env('AZURE_SPEECH_VOICE_MS', 'ms-MY-YasminNeural'),
        'voice_en' => env('AZURE_SPEECH_VOICE_EN', 'en-US-JennyNeural'),
        'endpoint' => env('AZURE_SPEECH_ENDPOINT'), // only for testing; normally built from the region
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'cron' => [
        'token' => env('CRON_TOKEN'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Email over HTTPS (used for "Forgot password" codes)
    'brevo' => [
        'key' => env('BREVO_API_KEY', ''),
        'endpoint' => env('BREVO_ENDPOINT', 'https://api.brevo.com/v3/smtp/email'),
    ],

];
