<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Forgot password in three steps:
 *   1. enter your email  -> we email a 6-digit code (valid 5 min)
 *   2. type the code     -> max 5 wrong tries, then ask for a new code
 *   3. set a new password -> other devices are signed out, back to login
 * The page never says whether an email is registered.
 */
class PasswordResetController extends Controller
{
    private const CODE_MINUTES = 5;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_SECONDS = 60;
    private const MAX_PER_HOUR = 5;
    private const VERIFIED_MINUTES = 15;

    // ---------- step 1: email ----------

    public function showRequest(): View
    {
        return view('auth.forgot-password', ['minutes' => self::CODE_MINUTES]);
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => 'required|email|max:255']);
        $email = mb_strtolower(trim($data['email']));

        $error = $this->issueCode($email);
        if ($error) {
            return back()->withInput()->withErrors(['email' => $error]);
        }

        $request->session()->put('pw_reset_email', $email);
        $request->session()->forget('pw_reset_verified');

        return redirect()->route('password.verify');
    }

    // ---------- step 2: code ----------

    public function showVerify(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('pw_reset_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        $last = PasswordOtp::where('email', $email)->latest('id')->first();
        $wait = $last ? max(0, self::RESEND_SECONDS - $last->created_at->diffInSeconds(now())) : 0;

        return view('auth.forgot-password-code', [
            'maskedEmail' => $this->mask($email),
            'resendIn' => (int) ceil($wait),
            'minutes' => self::CODE_MINUTES,
        ]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('pw_reset_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        $error = $this->issueCode($email);

        return redirect()->route('password.verify')
            ->with($error ? 'otp_error' : 'otp_status', $error ?: 'We sent you a new code.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $email = $request->session()->get('pw_reset_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        $code = preg_replace('/\D/', '', (string) $request->input('code'));
        if (strlen($code) !== 6) {
            return back()->with('otp_error', 'Enter the 6-digit code from the email.');
        }

        $otp = PasswordOtp::where('email', $email)->whereNull('used_at')->latest('id')->first();

        if (! $otp || $otp->expires_at->isPast()) {
            return back()->with('otp_error', 'This code has expired. Tap “Resend code” to get a new one.');
        }
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return back()->with('otp_error', 'Too many wrong tries. Tap “Resend code” to get a new one.');
        }
        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            $left = self::MAX_ATTEMPTS - $otp->attempts;

            return back()->with('otp_error', $left > 0
                ? "That code isn't right. {$left} " . ($left === 1 ? 'try' : 'tries') . ' left.'
                : 'Too many wrong tries. Tap “Resend code” to get a new one.');
        }

        // Correct code — but only a real account can go on to set a password
        $otp->update(['used_at' => now()]);
        if (! User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            return back()->with('otp_error', 'That code isn\'t right.');
        }

        $request->session()->regenerate();
        $request->session()->put('pw_reset_verified', ['email' => $email, 'until' => now()->addMinutes(self::VERIFIED_MINUTES)->timestamp]);

        return redirect()->route('password.new');
    }

    // ---------- step 3: new password ----------

    public function showNew(Request $request): View|RedirectResponse
    {
        if (! $this->verifiedEmail($request)) {
            return redirect()->route('password.request')->withErrors(['email' => 'Please start again — your reset session expired.']);
        }

        return view('auth.forgot-password-new');
    }

    public function saveNew(Request $request): RedirectResponse
    {
        $email = $this->verifiedEmail($request);
        if (! $email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Please start again — your reset session expired.']);
        }

        $request->validate(['password' => 'required|min:6|confirmed']);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->firstOrFail();
        $user->password = $request->password; // hashed by the User model
        $user->save();

        // Sign the account out everywhere else and clear the codes
        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
        PasswordOtp::where('email', $email)->delete();
        $request->session()->forget(['pw_reset_email', 'pw_reset_verified']);

        return redirect()->route('login')->with('success', 'Password changed! Log in with your new password.');
    }

    // ---------- helpers ----------

    /** Creates and emails a code. Returns an error message, or null when all went well. */
    private function issueCode(string $email): ?string
    {
        $last = PasswordOtp::where('email', $email)->latest('id')->first();
        if ($last && $last->created_at->diffInSeconds(now()) < self::RESEND_SECONDS) {
            return null; // a code was just sent; don't spam, just carry on to the code screen
        }
        if (PasswordOtp::where('email', $email)->where('created_at', '>=', now()->subHour())->count() >= self::MAX_PER_HOUR) {
            return 'Too many codes requested. Please try again in an hour.';
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        $code = (string) random_int(100000, 999999);

        // Older codes stop working as soon as a new one is made
        PasswordOtp::where('email', $email)->whereNull('used_at')->update(['used_at' => now()]);
        PasswordOtp::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_MINUTES),
        ]);

        if (! $user) {
            return null; // same screen as a real account, so emails can't be guessed
        }

        try {
            Mail::to($user->email, $user->first_name ?: $user->name)
                ->send(new PasswordResetCode($code, $user->first_name ?: $user->name ?: 'there', self::CODE_MINUTES));
        } catch (\Throwable $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());
            PasswordOtp::where('email', $email)->latest('id')->first()?->delete();

            return 'We couldn\'t send the email right now. Please try again in a few minutes.';
        }

        return null;
    }

    private function verifiedEmail(Request $request): ?string
    {
        $v = $request->session()->get('pw_reset_verified');

        return ($v && ($v['until'] ?? 0) >= now()->timestamp) ? $v['email'] : null;
    }

    private function mask(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $shown = mb_substr($name, 0, min(2, mb_strlen($name)));

        return $shown . str_repeat('•', max(1, mb_strlen($name) - mb_strlen($shown))) . '@' . $domain;
    }
}
