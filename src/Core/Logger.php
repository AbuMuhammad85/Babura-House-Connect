<?php

namespace App\Core;

class Logger
{
    protected string $logPath;

    public function __construct(string $logPath)
    {
        $this->logPath = $logPath;
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    public function log(string $file, string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] [{$level}]: {$message}{$contextStr}" . PHP_EOL;
        
        $filePath = $this->logPath . '/' . $file;
        file_put_contents($filePath, $logMessage, FILE_APPEND);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('app.log', 'INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error.log', 'ERROR', $message, $context);
    }

    public function activity(string $message, array $context = []): void
    {
        $this->log('activity.log', 'ACTIVITY', $message, $context);
    }
}
