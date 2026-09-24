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
        }

        return $next($request);
    }
}
