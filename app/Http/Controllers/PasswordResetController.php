<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode;
use App\Models\User;
use App\Support\EmailCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Forgot password in three steps:
 *   1. enter your email   -> we email a 6-digit code (valid 5 min)
 *   2. type the code      -> max 5 wrong tries, then ask for a new code
 *   3. set a new password -> other devices are signed out, back to login
 * The page never says whether an email is registered.
 */
class PasswordResetController extends Controller
{
    private const PURPOSE = 'reset';
    private const VERIFIED_MINUTES = 15;

    // ---------- step 1: email ----------

    public function showRequest(): View
    {
        return view('auth.forgot-password', ['minutes' => EmailCode::MINUTES]);
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => 'required|email|max:255']);
        $email = mb_strtolower(trim($data['email']));

        $error = $this->issue($email);
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

        return view('auth.forgot-password-code', [
            'maskedEmail' => EmailCode::mask($email),
            'resendIn' => EmailCode::waitSeconds($email, self::PURPOSE),
            'minutes' => EmailCode::MINUTES,
        ]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('pw_reset_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        $error = $this->issue($email);

        return redirect()->route('password.verify')
            ->with($error ? 'otp_error' : 'otp_status', $error ?: 'We sent you a new code.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $email = $request->session()->get('pw_reset_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        if ($error = EmailCode::check($email, self::PURPOSE, (string) $request->input('code'))) {
            return back()->with('otp_error', $error);
        }

        // Correct code — but only a real account can go on to set a password
        if (! $this->findUser($email)) {
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

        $user = $this->findUser($email);
        abort_unless($user, 404);
        $user->password = $request->password; // hashed by the User model
        $user->save();

        // Sign the account out everywhere else and clear the codes
        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
        EmailCode::clear($email, self::PURPOSE);
        $request->session()->forget(['pw_reset_email', 'pw_reset_verified']);

        return redirect()->route('login')->with('success', 'Password changed! Log in with your new password.');
    }

    // ---------- helpers ----------

    private function issue(string $email): ?string
    {
        $user = $this->findUser($email);

        // Unknown emails still get a (never-sent) code so the page looks identical
        return EmailCode::issue($email, self::PURPOSE, $user ? function (string $code) use ($user) {
            $name = $user->first_name ?: $user->name ?: 'there';
            Mail::to($user->email, $name)->send(new PasswordResetCode($code, $name, EmailCode::MINUTES));
        } : null);
    }

    private function findUser(string $email): ?User
    {
        return User::whereRaw('LOWER(email) = ?', [$email])->first();
    }

    private function verifiedEmail(Request $request): ?string
    {
        $v = $request->session()->get('pw_reset_verified');

        return ($v && ($v['until'] ?? 0) >= now()->timestamp) ? $v['email'] : null;
    }
}
