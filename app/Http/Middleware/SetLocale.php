<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported UI languages. Keep in sync with the options shown on the
     * student "Language" settings page.
     */
    public const SUPPORTED = ['en', 'ms', 'zh', 'ta'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->language && in_array($user->language, self::SUPPORTED, true)) {
            App::setLocale($user->language);
            // Relative times like "2 hours ago" (diffForHumans) follow the same language.
            \Carbon\Carbon::setLocale($user->language);
        } elseif (! $user && in_array($request->session()->get('guest_locale'), self::SUPPORTED, true)) {
            // Language picked on the login page (before signing in)
            App::setLocale($request->session()->get('guest_locale'));
            \Carbon\Carbon::setLocale($request->session()->get('guest_locale'));
        }

        return $next($request);
    }
}
