<?php

namespace App\Console;

use App\Console\Commands\FetchRecords;
use App\Jobs\FetchRecords as FetchRecordsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule):void
    {
        $schedule->job(new FetchRecordsJob('US'))->daily();
        $schedule->job(new FetchRecordsJob('CN'))->daily();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands():void
    {
        $this->load(__DIR__.'/Commands');
    }
}
