<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Nightwatch\Facades\Nightwatch;
use Laravel\Nightwatch\Records\Exception;
use Laravel\Nightwatch\Records\OutgoingRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The Steam app list API takes the key as a query parameter, so it
        // shows up in request URLs and in connection error messages.
        Nightwatch::redactOutgoingRequests(function (OutgoingRequest $request) {
            $request->url = self::redactSteamKey($request->url);
        });

        Nightwatch::redactExceptions(function (Exception $exception) {
            $exception->message = self::redactSteamKey($exception->message);
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    public static function redactSteamKey(string $value): string
    {
        return preg_replace('/([?&]key=)[^&\s`\'"]+/i', '$1[redacted]', $value);
    }
}
