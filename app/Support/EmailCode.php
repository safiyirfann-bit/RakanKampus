<?php

namespace App\Support;

use App\Models\PasswordOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * 6-digit codes sent by email, shared by "forgot password" and "verify email at sign-up".
 * Codes are stored hashed, expire after a few minutes, allow 5 wrong tries,
 * and each email can only ask for a new one every 60s (max 5 per hour).
 */
class EmailCode
{
    public const MINUTES = 5;
    public const MAX_ATTEMPTS = 5;
    public const RESEND_SECONDS = 60;
    public const MAX_PER_HOUR = 5;

    /**
     * Make a code and hand it to $send (which emails it). Pass $send = null to make the
     * code without emailing anything (used so unknown emails look the same as real ones).
     * Returns an error message for the student, or null when all went well.
     */
    public static function issue(string $email, string $purpose, ?callable $send): ?string
    {
        $mine = PasswordOtp::where('email', $email)->where('purpose', $purpose);

        $last = (clone $mine)->latest('id')->first();
        if ($last && $last->created_at->diffInSeconds(now()) < self::RESEND_SECONDS) {
            return null; // one was just sent; don't spam, just carry on to the code screen
        }
        if ((clone $mine)->where('created_at', '>=', now()->subHour())->count() >= self::MAX_PER_HOUR) {
            return 'Too many codes requested. Please try again in an hour.';
        }

        $code = (string) random_int(100000, 999999);

        // Older codes stop working as soon as a new one is made
        (clone $mine)->whereNull('used_at')->update(['used_at' => now()]);
        $otp = PasswordOtp::create([
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::MINUTES),
        ]);

        if (! $send) {
            return null;
        }

        try {
            $send($code);
        } catch (\Throwable $e) {
            Log::error("Email code ({$purpose}) failed: " . $e->getMessage());
            $otp->delete();

            return 'We couldn\'t send the email right now. Please try again in a few minutes.';
        }

        return null;
    }

    /** Checks a typed code. Returns null when correct (and marks it used), or an error message. */
    public static function check(string $email, string $purpose, string $typed): ?string
    {
        $code = preg_replace('/\D/', '', $typed);
        if (strlen($code) !== 6) {
            return 'Enter the 6-digit code from the email.';
        }

        $otp = PasswordOtp::where('email', $email)->where('purpose', $purpose)
            ->whereNull('used_at')->latest('id')->first();

        if (! $otp || $otp->expires_at->isPast()) {
            return 'This code has expired. Tap “Resend code” to get a new one.';
        }
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return 'Too many wrong tries. Tap “Resend code” to get a new one.';
        }
        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            $left = self::MAX_ATTEMPTS - $otp->attempts;

            return $left > 0
                ? "That code isn't right. {$left} " . ($left === 1 ? 'try' : 'tries') . ' left.'
                : 'Too many wrong tries. Tap “Resend code” to get a new one.';
        }

        $otp->update(['used_at' => now()]);

        return null;
    }

    /** Seconds until this email may ask for another code. */
    public static function waitSeconds(string $email, string $purpose): int
    {
        $last = PasswordOtp::where('email', $email)->where('purpose', $purpose)->latest('id')->first();

        return $last ? (int) ceil(max(0, self::RESEND_SECONDS - $last->created_at->diffInSeconds(now()))) : 0;
    }

    public static function clear(string $email, string $purpose): void
    {
        PasswordOtp::where('email', $email)->where('purpose', $purpose)->delete();
    }

    public static function mask(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $shown = mb_substr($name, 0, min(2, mb_strlen($name)));

        return $shown . str_repeat('•', max(1, mb_strlen($name) - mb_strlen($shown))) . '@' . $domain;
    }
}
