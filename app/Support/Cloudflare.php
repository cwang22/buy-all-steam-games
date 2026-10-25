<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class Cloudflare
{
    public static function configured(): bool
    {
        return !empty(config('services.cloudflare.zone_id')) && !empty(config('services.cloudflare.api_token'));
    }

    /**
     * Clear everything Cloudflare has cached for the zone.
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public static function purge(): void
    {
        $zone = config('services.cloudflare.zone_id');

        Http::withToken(config('services.cloudflare.api_token'))
            ->post("https://api.cloudflare.com/client/v4/zones/{$zone}/purge_cache", ['purge_everything' => true])
            ->throw();
    }
}
