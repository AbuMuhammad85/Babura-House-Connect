<?php

namespace App\Helpers;

class Flash
{
    public static function set(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function get(string $key): ?string
    {
        $message = $_SESSION['_flash'][$key] ?? null;
        if ($message !== null) {
            unset($_SESSION['_flash'][$key]);
        }
        return $message;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }
}
