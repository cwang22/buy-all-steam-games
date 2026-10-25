<?php

namespace App\Providers;

use App\Events\RecordCreated;
use App\Listeners\PurgeCloudflareCache;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RecordCreated::class => [
            PurgeCloudflareCache::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        //
    }
}
