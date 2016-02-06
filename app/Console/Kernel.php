<?php

namespace App\Console;

use App\DrupalStats\Console\Commands\GetNodes;
use App\DrupalStats\Console\Commands\GetPiftCiJob;
use App\DrupalStats\Console\Commands\GetTerms;
use App\DrupalStats\Console\Commands\GetUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        GetNodes::class,
        GetTerms::class,
        GetUsers::class,
        GetPiftCiJob::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}
