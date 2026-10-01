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

        // MAIL_MAILER=brevo: send email over Brevo's HTTPS API (Render's free plan blocks SMTP)
        Mail::extend('brevo', fn () => new BrevoTransport(
            (string) config('services.brevo.key'),
            (string) config('services.brevo.endpoint', 'https://api.brevo.com/v3/smtp/email'),
        ));
    }
}
