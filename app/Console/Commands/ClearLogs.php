<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class ClearLogs extends Command
{
    // The name and signature of the command
    protected $signature = 'logs:clear';

    // The console command description
    protected $description = 'Clear all log files in the storage/logs directory';

    // Execute the console command
    public function handle()
    {
        // Specify the log directory
        $logPath = storage_path('logs');

        // Clear all .log files in the directory
        foreach (glob($logPath . '/*.log') as $logFile) {
            file_put_contents($logFile, ''); // Empty the log file
        }

        // Output message to the console
        $this->info('All log files have been cleared successfully.');
    }
}
