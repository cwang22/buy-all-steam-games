<?php

namespace App\Console\Commands;

use App\Jobs\FetchRecords as FetchRecordsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FetchRecords extends Command
{
    protected $signature = 'fetch {countries*}';

    protected $description = 'fetches amount of prices of all Steam games';

    public function handle()
    {
        foreach ($this->argument('countries') as $country) {
            FetchRecordsJob::dispatch(Str::upper($country));
        }
    }

}
