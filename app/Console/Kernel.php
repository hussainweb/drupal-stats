<?php

namespace App\Console;

use App\DrupalStats\Console\Commands\GetNewUsers;
use App\DrupalStats\Console\Commands\GetNodes;
use App\DrupalStats\Console\Commands\GetPiftCiJob;
use App\DrupalStats\Console\Commands\GetTerms;
use App\DrupalStats\Console\Commands\GetUsers;
use App\DrupalStats\Console\Commands\UpdateNodes;
use App\DrupalStats\Console\Commands\UpdatePiftCiJob;
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
        GetNewUsers::class,
        GetNodes::class,
        GetTerms::class,
        GetUsers::class,
        GetPiftCiJob::class,
        UpdateNodes::class,
        UpdatePiftCiJob::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('dsupdate:nodes project_release')->dailyAt('23:00');
        $schedule->command('dsupdate:nodes project_module')->dailyAt('1:00');
        $schedule->command('dsupdate:nodes project_theme')->dailyAt('1:30');
        $schedule->command('dsupdate:nodes project_theme_engine')->dailyAt('2:00');
        $schedule->command('dsupdate:nodes project_theme_drupalorg')->dailyAt('2:10');
        $schedule->command('dsupdate:nodes project_core')->dailyAt('2:20');
        $schedule->command('dsupdate:nodes project_distribution')->dailyAt('2:30');
        $schedule->command('dsupdate:nodes organization')->dailyAt('2:40');
        $schedule->command('dsupdate:nodes casestudy')->dailyAt('2:50');
        $schedule->command('dsupdate:nodes book')->dailyAt('3:00');
        $schedule->command('dsupdate:nodes project_issue')->dailyAt('3:30');
//        $schedule->command('dsget:new-users')->dailyAt('4:30');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'../DrupalStats/Console/Commands');
        require base_path('routes/console.php');
    }
}
