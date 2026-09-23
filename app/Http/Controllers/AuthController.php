<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return redirect()->route('student.home');
    }

    return back()->withErrors([
        'email' => 'Incorrect email or password.',
    ])->onlyInput('email');
}

    /**
     * Show register page
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Process register
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'student_id' => 'required|string|max:255|unique:users,student_id',
            'email'      => 'required|email|unique:users,email',
            'faculty'    => 'nullable|string|max:255',
            'password'   => 'required|min:6|confirmed',
        ]);

        User::create([
            'name'       => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'student_id' => $request->student_id,
            'email'      => $request->email,
            'faculty'    => $request->faculty,
            'password'   => $request->password, // auto hashed by User model
            'role'       => 'student',
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
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
}