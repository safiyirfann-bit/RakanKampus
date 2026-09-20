<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    /**
     * Lets an external pinger (e.g. cron-job.org) run the reminder check every minute
     * on hosts with no cron. Disabled unless CRON_TOKEN is set.
     */
    public function reminders(Request $request)
    {
        $expected = (string) config('services.cron.token');
        $given = (string) ($request->query('token') ?? $request->bearerToken());

        abort_if($expected === '' || ! hash_equals($expected, $given), 404);

        Artisan::call('reminders:send-due');
        $output = Artisan::output();

        // Reuses the same external cron ping as reminders (no second cron-job.org
        // entry needed) to also check for classes starting soon today.
        Artisan::call('classes:send-upcoming');
        $output .= Artisan::output();

        return response(trim($output), 200)->header('Content-Type', 'text/plain');
    }
}
