<?php

namespace App\Console;

use App\Jobs\DeleteExpiredTokens;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // cron job to run the scheduler every minute
    // * * * * * php /home/your-user/public_html/artisan schedule:run >> /dev/null 2>&1 
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

        // expire pending bookings every minute to release holds quickly
        $schedule->command('bookings:expire-pending')->everyMinute()->withoutOverlapping();
        
        // Remove expired personal access tokens daily
        $schedule->job(new DeleteExpiredTokens())->daily()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
