<?php

namespace App\Helpers;

use App\Core\Request;

class CSRF
{
    /**
     * Retrieve or generate a secure CSRF token.
     */
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Generate HTML hidden input field representing the token.
     */
    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Verify token matches the session value.
     */
    public static function validate(Request $request): bool
    {
        if ($request->getMethod() !== 'post') {
            return true;
        }

        $body = $request->getBody();
        $token = $body['csrf_token'] ?? '';
        
        return hash_equals(self::token(), $token);
    }
}
