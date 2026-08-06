<?php

namespace App\Core;

class ErrorHandler
{
    protected Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function register(): void
    {
        error_reporting(E_ALL);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (error_reporting() & $level) {
            $this->logger->error("Error [{$level}]: {$message} in {$file} on line {$line}");
            if (!Config::get('app.debug')) {
                $this->render500();
            }
        }
        return false;
    }

    public function handleException(\Throwable $exception): void
    {
        $this->logger->error("Exception: " . $exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        if (Config::get('app.debug')) {
            echo "<h1>Unhandled Exception</h1>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . " on line " . $exception->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        } else {
            $this->render500();
        }
        exit;
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            $this->logger->error("Fatal Shutdown Error [{$error['type']}]: {$error['message']} in {$error['file']} on line {$error['line']}");
            if (!Config::get('app.debug')) {
                $this->render500();
            }
        }
    }

    protected function render500(): void
    {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(500);
        $controller = new \App\Controllers\BaseController();
        echo $controller->render('public/500', ['title' => 'Server Error']);
        exit;
    }
}
