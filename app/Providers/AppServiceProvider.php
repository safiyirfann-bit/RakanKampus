<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')){
            URL::forceScheme('https');
        }

        // Mobile app login tokens (no extra package needed): auth:api finds the user
        // from the Bearer token and notes when it was last used.
        \Illuminate\Support\Facades\Auth::viaRequest('rk-token', function ($request) {
            $token = \App\Models\ApiToken::findByPlain($request->bearerToken());
            if (! $token || ! $token->user || $token->user->isAdmin()) {
                return null;
            }
            if (! $token->last_used_at || $token->last_used_at->lt(now()->subMinutes(10))) {
                $token->forceFill(['last_used_at' => now()])->saveQuietly();
            }
            $token->user->currentApiToken = $token;

            return $token->user;
        });

        // MAIL_MAILER=brevo: send email over Brevo's HTTPS API (Render's free plan blocks SMTP)
        Mail::extend('brevo', fn () => new BrevoTransport(
            (string) config('services.brevo.key'),
            (string) config('services.brevo.endpoint', 'https://api.brevo.com/v3/smtp/email'),
        ));
    }
}
