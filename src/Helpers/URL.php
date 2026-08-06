<?php

namespace App\Helpers;

use App\Core\Config;

class URL
{
    protected static $router;

    public static function setRouter($router): void
    {
        self::$router = $router;
    }

    public static function to(string $path): string
    {
        return '/' . ltrim($path, '/');
    }

    public static function asset(string $path): string
    {
        return self::to($path);
    }

    public static function route(string $name, array $params = []): string
    {
        if (self::$router) {
            return self::$router->getNamedRouteUrl($name, $params);
        }
        return '#';
    }

    public static function base(): string
    {
        return Config::get('app.url', 'http://localhost:8000');
    }
}
