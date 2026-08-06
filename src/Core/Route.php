<?php

namespace App\Core;

class Route
{
    public string $method;
    public string $path;
    public $callback;
    public array $middlewares = [];
    public ?string $name = null;
    protected string $regex;
    protected array $paramNames = [];

    public function __construct(string $method, string $path, $callback)
    {
        $this->method = strtolower($method);
        $this->path = '/' . trim($path, '/');
        $this->callback = $callback;
        $this->compileRegex();
    }

    protected function compileRegex(): void
    {
        preg_match_all('/\{([A-Za-z0-9_]+)\}/', $this->path, $matches);
        $this->paramNames = $matches[1];

        $pattern = preg_replace('/\{[A-Za-z0-9_]+\}/', '([^/]+)', $this->path);
        $this->regex = '#^' . $pattern . '$#i';
    }

    public function matches(string $path, &$params = []): bool
    {
        $cleanPath = '/' . trim($path, '/');
        if (preg_match($this->regex, $cleanPath, $matches)) {
            array_shift($matches);
            $params = [];
            foreach ($this->paramNames as $index => $name) {
                if (isset($matches[$index])) {
                    $params[$name] = urldecode($matches[$index]);
                }
            }
            return true;
        }
        return false;
    }

    public function middleware(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
