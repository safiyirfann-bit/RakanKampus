<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(
    basePath: dirname(__DIR__)
)

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // Render sits in front of the app as a proxy — trust it so request()->ip()
        // is the student's real IP (shown in Privacy & Security), not Render's.
        $middleware->trustProxies(at: '*');

        $middleware->alias([

            'admin' => \App\Http\Middleware\AdminMiddleware::class,

        ]);

        // Applies the logged-in student's saved language preference (see
        // the "language" column on users) to every request so translated
        // pages render in the right language without needing to opt in.
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\LogUserActivity::class,
            \App\Http\Middleware\AllowPrefetch::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {

        // Session/CSRF token expired (419), e.g. the app or a tab was left open for hours and
        // then a form was sent. Laravel turns TokenMismatchException into an HttpException(419)
        // before render callbacks run, so match on that. Instead of the bare "419 Page Expired"
        // page: logging out just finishes the logout; any other form goes back to the page it
        // came from (or the login page) with a fresh token and a short note to try again.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => __('Your session has expired. Please refresh the page and try again.')], 419);
            }
            if ($request->is('logout')) {
                \Illuminate\Support\Facades\Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login');
            }

            $back = url()->previous();
            $target = ($back && $back !== $request->fullUrl()) ? redirect()->to($back) : redirect()->route('login');

            return $target->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('status', __('Your session expired, so nothing was sent. Please try again.'));
        });

    })

    ->create();