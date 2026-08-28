<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(
    basePath: dirname(__DIR__)
)

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([

            'admin' => \App\Http\Middleware\AdminMiddleware::class,

        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {

        // Session/CSRF token expired (419) — instead of showing the
        // "Page Expired" error page, just send the user to login.
        // This covers logout (and any other form) being submitted
        // long after the page was first loaded.
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->route('login')
                ->with('status', 'Sesi anda telah tamat. Sila log masuk semula.');
        });

    })

    ->create();