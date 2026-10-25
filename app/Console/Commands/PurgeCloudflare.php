<?php

namespace App\Console\Commands;

use App\Support\Cloudflare;
use Illuminate\Console\Command;
use Throwable;

class PurgeCloudflare extends Command
{
    protected $signature = 'cloudflare:purge';

    protected $description = 'purges everything Cloudflare has cached, e.g. after a deploy changes the pages';

    public function handle(): int
    {
        // Not an error, so a deploy script can run this on servers without Cloudflare.
        if (!Cloudflare::configured()) {
            $this->warn('CLOUDFLARE_ZONE_ID or CLOUDFLARE_API_TOKEN is not set, nothing purged.');

            return 0;
        }

        try {
            Cloudflare::purge();
        } catch (Throwable $e) {
            $this->error("Cloudflare purge failed: {$e->getMessage()}");

            return 1;
        }

        $this->info('Purged Cloudflare cache.');

        return 0;
    }
}
