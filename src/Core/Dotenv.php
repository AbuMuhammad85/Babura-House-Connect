<?php

namespace App\Core;

class Dotenv
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function load(): void
    {
        if (!file_exists($this->path)) {
            return;
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0 || empty($line)) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match('/^\'(.*)\'$/', $value, $matches)) {
                    $value = $matches[1];
                }

                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}
