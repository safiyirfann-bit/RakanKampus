<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Faster page changes (see partials/fast-nav). When the browser downloads a page
 * ahead of time because the student is touching/hovering a link, it sends
 * "Sec-Purpose: prefetch". Pages are normally "no-cache", which makes the browser
 * throw that copy away and download the page again on the tap — so for prefetch
 * requests only, let this one browser keep the page for 30 seconds.
 * Normal visits are untouched (always fresh).
 */
class AllowPrefetch
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $purpose = strtolower($request->header('Sec-Purpose', $request->header('Purpose', '')));

        if ($request->isMethod('GET')
            && str_contains($purpose, 'prefetch')
            && $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type', 'text/html'), 'text/html')) {
            $response->headers->set('Cache-Control', 'private, max-age=30');
        }

        return $response;
    }
}
