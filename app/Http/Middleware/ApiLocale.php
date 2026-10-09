<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mobile app: answers and messages in the student's saved language, or (before
 * login) in the language the app sends in the Accept-Language header.
 */
class ApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->language;
        if (! in_array($locale, SetLocale::SUPPORTED, true)) {
            $locale = substr((string) $request->header('Accept-Language'), 0, 2);
        }
        if (in_array($locale, SetLocale::SUPPORTED, true)) {
            App::setLocale($locale);
            \Carbon\Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
