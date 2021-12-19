<?php

namespace App\Console\Commands;

use App\Jobs\FetchRecords as FetchRecordsJob;
use Illuminate\Console\Command;

class FetchRecords extends Command
{
    protected $signature = 'fetch';

    protected $description = 'fetches amount of prices of all Steam games';

    public function handle()
    {
        FetchRecordsJob::dispatch('us');
    }

}
