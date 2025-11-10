<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\TaskDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RemoveDocument extends Command
{
    protected $signature = 'app:remove-document';
    protected $description = 'Delete documents older than 90 days';

    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(90)->format('Y-m-d H:i:s');

        Log::channel('cron')->info('Delete documents older than 90 days');

        Task::whereIn('status_id', [1, 8])
            ->chunk(100, function ($tasks) use ($cutoffDate) {
                $taskIds = $tasks->pluck('id')->toArray();

                $documents = TaskDocument::whereIn('task_id', $taskIds)
                    ->where('created_at', '<', $cutoffDate)
                    ->get();

                foreach ($documents as $document) {
                    if ($document->document && Storage::disk('public')->exists('admin/' . basename($document->document))) {
                        Storage::disk('public')->delete('admin/' . basename($document->document));
                    }else{
                        Log::channel('cron')->info('Delete documents older than 90 days failed'); 
                    }
                    $document->delete();
                }
            });

        $this->info('Delete documents older than 90 days completed');

        Log::channel('cron')->info('Delete documents older than 90 days completed');
    }
}