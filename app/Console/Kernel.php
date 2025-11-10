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
        $schedule->command('app:task-due')->dailyAt('09:00')->timezone('Asia/Kolkata');
        $schedule->command('app:clear-notification')->dailyAt('09:00')->timezone('Asia/Kolkata');
        $schedule->command('app:task-create')->dailyAt('09:01')->timezone('Asia/Kolkata');
        $schedule->command('app:remove-document')->dailyAt('09:02')->timezone('Asia/Kolkata');
        $schedule->command('app:send-monthly')->dailyAt('09:00')->timezone('Asia/Kolkata');
        $schedule->command('app:send-weekly-overdue-report')->weeklyOn(1, '09:00')->timezone('Asia/Kolkata');
        $schedule->command('app:ialert-bac-remider')->dailyAt('15:00')->timezone('Asia/Kolkata');
        $schedule->command('app:ialert-customer-followup-reminder')->dailyAt('15:00')->timezone('Asia/Kolkata');
        $schedule->command('app:ialert-payment-commited-reminder')->dailyAt('15:00')->timezone('Asia/Kolkata');
        $schedule->command('app:ialert-wcr-reminder')->dailyAt('15:00')->timezone('Asia/Kolkata');

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
