<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class ClearNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete notifications that were created more than 60 days ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subDays(60)->toDateString();
        $deletedData = Notification::where('created_at', '<', $date)->delete();

        $this->info("Successfully deleted notifications.");
    }
}
