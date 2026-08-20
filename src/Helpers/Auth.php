<?php

namespace App\Helpers;

use App\Repositories\UserRepository;

class Auth
{
    protected static ?array $cachedUser = null;

    /**
     * Set up session variables and regenerate session ID to prevent fixation.
     */
    public static function login(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        self::$cachedUser = $user;
    }

    /**
     * Terminate user session and clear variables.
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }

        self::$cachedUser = null;
        session_unset();
        session_destroy();
    }

    /**
     * Check if a user ID is present in the session.
     */
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Retrieve current user details from the database.
     */
    public static function user($key = null)
    {
        if (!self::check()) {
            return null;
        }

        if (self::$cachedUser === null) {
            $repo = new UserRepository();
            self::$cachedUser = $repo->findById((int)$_SESSION['user_id']);
        }

        if (self::$cachedUser === null) {
            return null;
        }

        if ($key !== null) {
            return self::$cachedUser[$key] ?? null;
        }

        return self::$cachedUser;
    }

    /**
     * Retrieve the authenticated user's role from the database.
     */
    public static function role(): ?string
    {
        return self::user('role');
    }

    /**
     * Generate avatar initials dynamically from the actual user's full_name (up to 3 characters).
     */
    public static function initials(): string
    {
        $name = self::user('full_name') ?? '';
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }
        return !empty($initials) ? mb_substr($initials, 0, 3) : 'U';
    }
}
