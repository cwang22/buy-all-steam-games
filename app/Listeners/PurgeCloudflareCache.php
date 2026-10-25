<?php

namespace App\Listeners;

use App\Events\RecordCreated;
use App\Support\Cloudflare;
use Illuminate\Support\Facades\Log;
use Throwable;

class PurgeCloudflareCache
{
    /**
     * Pages are cached at Cloudflare for a day, so clear them once a new record
     * lands. A failed purge is only logged: the record is already saved.
     */
    public function handle(RecordCreated $event): void
    {
        if (!Cloudflare::configured()) {
            return;
        }

        try {
            Cloudflare::purge();

            Log::info("[fetch:{$event->record->cc}] Purged Cloudflare cache");
        } catch (Throwable $e) {
            Log::warning("[fetch:{$event->record->cc}] Cloudflare purge failed: {$e->getMessage()}");
        }
    }
}
