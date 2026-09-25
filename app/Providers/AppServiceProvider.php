<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by('login:'.$email.'|'.$request->ip()),
                Limit::perMinute(20)->by('login-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('emails', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinutes(10, 3)->by('mail:'.$email),
                Limit::perMinute(10)->by('mail-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('tokens', function (Request $request) {
            return Limit::perMinute(10)->by('tokens:'.$request->ip());
        });
    }
}