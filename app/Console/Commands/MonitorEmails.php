<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:emails {--follow : Follow log file in real-time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor email sending in real-time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📧 Email Monitoring Started...');
        $this->newLine();

        if ($this->option('follow')) {
            $this->info('🔍 Following log file in real-time...');
            $this->info('Press Ctrl+C to stop monitoring');
            $this->newLine();

            $logFile = storage_path('logs/laravel.log');

            if (!file_exists($logFile)) {
                $this->error('❌ Log file not found: ' . $logFile);
                return 1;
            }

            // Get initial file size
            $lastSize = filesize($logFile);

            while (true) {
                clearstatcache();
                $currentSize = filesize($logFile);

                if ($currentSize > $lastSize) {
                    // Read new content
                    $handle = fopen($logFile, 'r');
                    fseek($handle, $lastSize);
                    $newContent = fread($handle, $currentSize - $lastSize);
                    fclose($handle);

                    // Parse and display email-related logs
                    $lines = explode("\n", $newContent);
                    foreach ($lines as $line) {
                        if (preg_match('/email|mail|booking.*confirmation|payment.*confirmation/i', $line)) {
                            $this->parseAndDisplayLogLine($line);
                        }
                    }

                    $lastSize = $currentSize;
                }

                sleep(1);
            }
        } else {
            // Show recent email logs
            $this->info('📋 Recent Email Logs:');
            $this->newLine();

            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                $lines = file($logFile);
                $emailLines = [];

                foreach (array_reverse($lines) as $line) {
                    if (preg_match('/email|mail|booking.*confirmation|payment.*confirmation/i', $line)) {
                        $emailLines[] = $line;
                        if (count($emailLines) >= 20) break;
                    }
                }

                if (empty($emailLines)) {
                    $this->warn('No email-related logs found in recent entries.');
                } else {
                    foreach (array_reverse($emailLines) as $line) {
                        $this->parseAndDisplayLogLine($line);
                    }
                }
            } else {
                $this->error('❌ Log file not found: ' . $logFile);
            }
        }

        return 0;
    }

    /**
     * Parse and display a log line with color coding
     */
    private function parseAndDisplayLogLine(string $line): void
    {
        // Extract timestamp
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            $timestamp = $matches[1];
            $this->line('🕐 ' . $timestamp);
        }

        // Color code based on log level and content
        if (strpos($line, 'ERROR') !== false) {
            $this->error('❌ ' . $this->cleanLogLine($line));
        } elseif (strpos($line, 'WARNING') !== false) {
            $this->warn('⚠️  ' . $this->cleanLogLine($line));
        } elseif (strpos($line, 'INFO') !== false) {
            if (strpos($line, 'sent successfully') !== false) {
                $this->info('✅ ' . $this->cleanLogLine($line));
            } elseif (strpos($line, 'Failed to send') !== false) {
                $this->error('❌ ' . $this->cleanLogLine($line));
            } else {
                $this->line('ℹ️  ' . $this->cleanLogLine($line));
            }
        } else {
            $this->line('📝 ' . $this->cleanLogLine($line));
        }

        $this->newLine();
    }

    /**
     * Clean log line for display
     */
    private function cleanLogLine(string $line): string
    {
        // Remove timestamp and log level
        $line = preg_replace('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] local\.(INFO|ERROR|WARNING): /', '', $line);

        // Truncate if too long
        if (strlen($line) > 120) {
            $line = substr($line, 0, 120) . '...';
        }

        return $line;
    }
}
