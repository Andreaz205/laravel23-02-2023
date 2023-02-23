<?php

namespace App\Console;

use App\Events\Vk\GetGroupPosts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            event(new GetGroupPosts(env('VK_GROUP_ID'), env('VK_SERVICE_KEY'), 10));
        })->everyMinute();
        $schedule->command('queue:restart')->everyFiveMinutes()->withoutOverlapping();
//        $schedule->command('queue:work --sleep=3 --tries=3')->everyMinute()->sendOutputTo(storage_path() . '/logs/queue-jobs.log')->withoutOverlapping();
        $schedule->command('queue:work --tries=3 --stop-when-empty')->everyMinute();
//        $schedule->command('queue:work --tries=3')->everyMinute();

        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
