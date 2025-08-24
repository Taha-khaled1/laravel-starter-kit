<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StartQueueWorker extends Command
{
    // Command signature to call from terminal
    protected $signature = 'queue:start';

    // Description of the command
    protected $description = 'Start the Laravel queue worker in the background.';

    public function handle()
    {
        try {
            Log::info('Starting queue worker via Laravel command.');

            // Run queue worker in the background
            $process = new Process(['php', 'artisan', 'queue:work', '--daemon']);
            $process->setTimeout(0); // Run indefinitely
            $process->start();

            Log::info('Queue worker started successfully.');
            $this->info('Queue worker started successfully.');
        } catch (ProcessFailedException $exception) {
            Log::error('Failed to start queue worker.', ['error' => $exception->getMessage()]);
            $this->error('Failed to start queue worker: ' . $exception->getMessage());
        }
    }
}
