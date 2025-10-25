<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Monitor question usage anomalies daily
        $schedule->command('questions:monitor')
            ->daily()
            ->withoutOverlapping()
            ->runInBackground();
            
        // Check for anomalies weekly
        $schedule->command('questions:check-anomalies')
            ->weekly()
            ->withoutOverlapping();
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
