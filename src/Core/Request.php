<?php

namespace App\Core;

class Request
{
    public function getPath(): string
    {
        $path = $_GET['url'] ?? '/';
        $path = '/' . ltrim($path, '/');
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                if ($key === 'url') continue;
                if (is_array($value)) {
                    $body[$key] = filter_var_array($value, FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $body[$key] = filter_var_array($value, FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
        return $body;
    }
}
