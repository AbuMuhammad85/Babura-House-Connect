<?php

namespace App\Helpers;

class Redirect
{
    public static function to(string $url): void
    {
        header("Location: " . $url);
        exit;
    }

    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::to($referer);
    }
}
