<?php

namespace App\Helpers;

class Auth
{
    public static function login(array $user): void
    {
        $_SESSION['user'] = $user;
    }

    public static function logout(): void
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        session_unset();
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']);
    }

    public static function user($key = null)
    {
        $user = $_SESSION['user'] ?? null;
        if ($user === null) {
            return null;
        }

        if ($key !== null) {
            return $user[$key] ?? null;
        }

        return $user;
    }

    public static function role(): ?string
    {
        return self::user('role');
    }
}
