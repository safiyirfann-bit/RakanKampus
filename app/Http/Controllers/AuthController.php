<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode;
use App\Models\User;
use App\Support\EmailCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Show student login page
     */
    public function showLogin()
    {
        // If someone is already logged in (e.g. still has an admin session
        // open) and lands on the student login page, send them to where
        // they already belong instead of showing a login form that will
        // just fight with their existing session.
        if (Auth::check()) {
            return redirect()->to(Auth::user()->isAdmin() ? route('admin.dashboard') : route('student.home'));
        }

        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {

        // Reject admin accounts from student login
        if (Auth::user()->isAdmin()) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'This is an admin account. Please use the Administrator login.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Language / theme picked on the login page carry on into the app
        $picked = array_filter([
            'language' => in_array($request->session()->pull('guest_locale'), \App\Http\Middleware\SetLocale::SUPPORTED, true) ? app()->getLocale() : null,
            'theme' => in_array($request->input('theme_pick'), ['light', 'dark'], true) ? $request->input('theme_pick') : null,
        ]);
        if ($picked) {
            Auth::user()->forceFill($picked)->saveQuietly();
        }

        Auth::user()->forceFill(['last_login_at' => now()])->saveQuietly();
        $this->recordLogin($request);

        return redirect()->route('student.home');
    }

    return back()->withErrors([
        'email' => 'Incorrect email or password.',
    ])->onlyInput('email');
}

    /**
     * Show register page
     */
    public function showRegister(Request $request)
    {
        // Coming back from "Change details" on the code screen: refill the form
        $pending = $this->pendingSignup($request);
        if ($pending && ! $request->session()->hasOldInput()) {
            $request->session()->now('_old_input', \Illuminate\Support\Arr::only($pending, ['first_name', 'last_name', 'student_id', 'email']));
        }

        return view('auth.register');
    }

    /**
     * Process register
     */
    public function register(Request $request)
    {
        // Matric numbers are stored in one format (upper case, no spaces/dashes),
        // so "01dit24f1128" and "01DIT24F1128" can't register twice.
        $request->merge(['student_id' => \App\Support\MatricNumber::normalise($request->student_id)]);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            // Only PUO students: the matric number must look like 01DIT24F1128.
            'student_id' => [
                'required', 'string', 'max:20', 'unique:users,student_id',
                function ($attribute, $value, $fail) {
                    if (! \App\Support\MatricNumber::isValid($value)) {
                        $fail('Please enter a valid PUO matric number (e.g. 01DKA23F0456). The programme code (e.g. DIT, DEE, DKA) must be a PUO programme.');
                    }
                },
            ],
            'email'      => 'required|email|unique:users,email',
            'faculty'    => 'nullable|string|max:255',
            'password'   => 'required|min:6|confirmed',
        ]);

        // Don't create the account yet: first prove the email is real.
        // The details wait in the session until the 6-digit code is typed in.
        $pending = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'student_id' => $request->student_id,
            'email'      => $request->email,
            'faculty'    => \App\Support\MatricNumber::programme($request->student_id) ?? $request->faculty,
            'password'   => Hash::make($request->password),
            'until'      => now()->addMinutes(30)->timestamp,
        ];

        if ($error = $this->sendSignupCode($pending)) {
            return back()->withInput($request->except('password', 'password_confirmation'))->withErrors(['email' => $error]);
        }

        $request->session()->put('reg_pending', $pending);

        return redirect()->route('register.verify');
    }

    /**
     * Sign-up step 2: type the code that was emailed
     */
    public function showRegisterVerify(Request $request)
    {
        $pending = $this->pendingSignup($request);
        if (! $pending) {
            return redirect()->route('register');
        }
        $email = mb_strtolower($pending['email']);

        return view('auth.register-verify', [
            'maskedEmail' => EmailCode::mask($email),
            'resendIn'    => EmailCode::waitSeconds($email, 'register'),
            'minutes'     => EmailCode::MINUTES,
        ]);
    }

    public function registerResend(Request $request)
    {
        $pending = $this->pendingSignup($request);
        if (! $pending) {
            return redirect()->route('register');
        }

        $error = $this->sendSignupCode($pending);

        return redirect()->route('register.verify')
            ->with($error ? 'otp_error' : 'otp_status', $error ?: 'We sent you a new code.');
    }

    public function registerVerify(Request $request)
    {
        $pending = $this->pendingSignup($request);
        if (! $pending) {
            return redirect()->route('register')->withErrors(['email' => 'Your sign-up timed out. Please fill in the form again.']);
        }
        $email = mb_strtolower($pending['email']);

        if ($error = EmailCode::check($email, 'register', (string) $request->input('code'))) {
            return back()->with('otp_error', $error);
        }

        // Someone may have taken the email or matric number while this one was waiting
        if (User::whereRaw('LOWER(email) = ?', [$email])->exists() || User::where('student_id', $pending['student_id'])->exists()) {
            $request->session()->forget('reg_pending');

            return redirect()->route('register')->withErrors(['email' => 'This email or matric number was registered in the meantime. Try logging in instead.']);
        }

        User::create([
            'name'       => $pending['first_name'] . ' ' . $pending['last_name'],
            'first_name' => $pending['first_name'],
            'last_name'  => $pending['last_name'],
            'student_id' => $pending['student_id'],
            'email'      => $pending['email'],
            'faculty'    => $pending['faculty'],
            'password'   => $pending['password'], // already hashed; the model keeps it as is
            'role'       => 'student',
        ]);

        EmailCode::clear($email, 'register');
        $request->session()->forget('reg_pending');

        return redirect()->route('login')->with('success', 'Email verified and account created! Please log in.');
    }

    private function pendingSignup(Request $request): ?array
    {
        $p = $request->session()->get('reg_pending');

        return ($p && ($p['until'] ?? 0) >= now()->timestamp) ? $p : null;
    }

    private function sendSignupCode(array $pending): ?string
    {
        return EmailCode::issue(mb_strtolower($pending['email']), 'register', function (string $code) use ($pending) {
            Mail::to($pending['email'], $pending['first_name'])
                ->send(new PasswordResetCode($code, $pending['first_name'], EmailCode::MINUTES, 'register'));
        });
    }

    /**
     * Show admin login page
     */
    public function showAdminLogin()
    {
        if (Auth::check()) {
            return redirect()->to(Auth::user()->isAdmin() ? route('admin.dashboard') : route('student.home'));
        }

        return view('auth.admin-login');
    }

    /**
     * Process admin login
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            // Only rotate the session once we've confirmed this really is an
            // admin account — regenerating it for a rejected (non-admin)
            // attempt and then immediately logging out again was leaving the
            // already-rendered login page holding a stale CSRF token, which
            // surfaced as a confusing error until the page was refreshed.
            if (! Auth::user()->isAdmin()) {

                Auth::logout();

                return back()->withErrors([
                    'email' => 'Access is for admin only.',
                ]);
            }

            $request->session()->regenerate();

            // Language / theme picked on the login page carry on into the app
        $picked = array_filter([
            'language' => in_array($request->session()->pull('guest_locale'), \App\Http\Middleware\SetLocale::SUPPORTED, true) ? app()->getLocale() : null,
            'theme' => in_array($request->input('theme_pick'), ['light', 'dark'], true) ? $request->input('theme_pick') : null,
        ]);
        if ($picked) {
            Auth::user()->forceFill($picked)->saveQuietly();
        }

        Auth::user()->forceFill(['last_login_at' => now()])->saveQuietly();
            $this->recordLogin($request);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Incorrect admin email or password.',
        ])->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** Sign-in history shown under Profile → Privacy & Security. Never blocks a login. */
    private function recordLogin(Request $request): void
    {
        try {
            \Illuminate\Support\Facades\DB::table('login_activities')->insert([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
