<?php

namespace App\Core;

class Config
{
    protected static array $config = [];
    protected static string $configPath = '';

    public static function setPath(string $path): void
    {
        self::$configPath = $path;
    }

    public static function load(string $file): void
    {
        $filePath = self::$configPath . '/' . $file . '.php';
        if (file_exists($filePath)) {
            self::$config[$file] = require $filePath;
        }
    }

    public static function get(string $key, $default = null)
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);

        if (!isset(self::$config[$file])) {
            self::load($file);
        }

        $array = self::$config[$file] ?? null;
        if (empty($parts)) {
            return $array ?? $default;
        }

        foreach ($parts as $part) {
            if (is_array($array) && array_key_exists($part, $array)) {
                $array = $array[$part];
            } else {
                return $default;
            }
        }

        return $array;
    }
}
