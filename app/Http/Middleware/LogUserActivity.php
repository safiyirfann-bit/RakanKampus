<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records that the logged-in user was active during the current hour
 * (one row per user per hour). The session remembers the last hour
 * logged, so the database is touched at most once an hour per user.
 */
class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && $request->hasSession()) {
            $hour = now()->startOfHour()->format('Y-m-d H:00:00');

            if ($request->session()->get('_activity_hour') !== $hour) {
                try {
                    DB::table('user_activity_logs')->insertOrIgnore([
                        'user_id' => Auth::id(),
                        'active_at' => $hour,
                    ]);
                    $request->session()->put('_activity_hour', $hour);
                } catch (\Throwable $e) {
                    // Never break a page because of analytics logging.
                    report($e);
                }
            }
        }

        return $response;
    }
}
