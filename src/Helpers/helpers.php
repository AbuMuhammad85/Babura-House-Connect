<?php

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('component')) {
    function component(string $name, array $data = []): void
    {
        extract($data);
        $componentFile = dirname(__DIR__) . "/Views/components/{$name}.php";
        if (file_exists($componentFile)) {
            include $componentFile;
        } else {
            echo "<!-- Component '{$name}' not found -->";
        }
    }
}

if (!function_exists('isActive')) {
    function isActive(string $path, string $activeClass = 'active'): string
    {
        if (!empty($_GET['url'])) {
            $currentPath = '/' . trim($_GET['url'], '/');
        } else {
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $position = strpos($uri, '?');
            if ($position !== false) {
                $uri = substr($uri, 0, $position);
            }
            
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $basePath = dirname($scriptName);
            $basePath = str_replace('\\', '/', $basePath);
            
            if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
            }
            
            $currentPath = '/' . trim($uri, '/');
        }
        
        $currentPath = $currentPath === '' ? '/' : $currentPath;
        
        if ($path === '/' && $currentPath === '/') {
            return $activeClass;
        }
        
        if ($path !== '/' && strpos($currentPath, $path) === 0) {
            return $activeClass;
        }
        
        return '';
    }
}

if (!function_exists('formatNaira')) {
    function formatNaira(float $amount): string
    {
        return '₦' . number_format($amount, 0, '.', ',');
    }
}

if (!function_exists('truncateText')) {
    function truncateText(string $text, int $limit = 60, string $end = '...'): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return mb_substr($text, 0, $limit) . $end;
    }
}
