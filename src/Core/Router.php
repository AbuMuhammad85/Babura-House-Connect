<?php

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $groupStack = [];
    protected array $namedRoutes = [];
    protected array $csrfExclusions = [];
    public Request $request;
    public Response $response;

    protected array $middlewareMap = [
        'guest' => \App\Middleware\GuestMiddleware::class,
        'auth' => \App\Middleware\AuthMiddleware::class,
        'tenant' => \App\Middleware\TenantMiddleware::class,
        'landlord' => \App\Middleware\LandlordMiddleware::class,
        'verified_landlord' => \App\Middleware\VerifiedLandlordMiddleware::class,
        'admin' => \App\Middleware\AdminMiddleware::class,
    ];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback): Route
    {
        return $this->addRoute('get', $path, $callback);
    }

    public function post(string $path, $callback): Route
    {
        return $this->addRoute('post', $path, $callback);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    protected function addRoute(string $method, string $path, $callback): Route
    {
        $prefix = '';
        $middlewares = [];

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $groupMiddleware = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $middlewares = array_merge($middlewares, $groupMiddleware);
            }
        }

        $fullPath = '/' . ltrim($prefix . '/' . ltrim($path, '/'), '/');
        $route = new Route($method, $fullPath, $callback);

        foreach ($middlewares as $mw) {
            $route->middleware($mw);
        }

        $this->routes[strtolower($method)][] = $route;
        return $route;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        
        if ($method === 'post' && !in_array($path, $this->csrfExclusions)) {
            if (!\App\Helpers\CSRF::validate($this->request)) {
                $this->response->setStatusCode(403);
                $controller = new \App\Controllers\BaseController();
                echo $controller->render('public/403', [
                    'title' => 'Access Denied',
                    'message' => 'CSRF verification failed. Please refresh the page and try again.'
                ]);
                exit;
            }
        }
        
        $matchedRoute = null;
        $params = [];

        // First pass: try to match static routes (those without parameters)
        foreach ($this->routes[$method] ?? [] as $route) {
            if (empty($route->paramNames) && $route->matches($path, $params)) {
                $matchedRoute = $route;
                break;
            }
        }

        // Second pass: if no static route matched, try dynamic routes (those with parameters)
        if (!$matchedRoute) {
            foreach ($this->routes[$method] ?? [] as $route) {
                if (!empty($route->paramNames) && $route->matches($path, $params)) {
                    $matchedRoute = $route;
                    break;
                }
            }
        }

        if (!$matchedRoute) {
            $this->response->setStatusCode(404);
            $controller = new \App\Controllers\BaseController();
            return $controller->render('public/404', ['title' => 'Page Not Found']);
        }

        return $this->runMiddlewarePipeline($matchedRoute->middlewares, $this->request, $this->response, function($req, $res) use ($matchedRoute, $params) {
            $callback = $matchedRoute->callback;

            if (is_array($callback)) {
                $controller = new $callback[0]();
                $action = $callback[1];
                return call_user_func_array([$controller, $action], array_merge([$req, $res], array_values($params)));
            }

            if (is_callable($callback)) {
                return call_user_func_array($callback, array_merge([$req, $res], array_values($params)));
            }

            $this->response->setStatusCode(500);
            return "Invalid Controller Callback";
        });
    }

    protected function runMiddlewarePipeline(array $middlewares, Request $request, Response $response, callable $destination)
    {
        if (empty($middlewares)) {
            return $destination($request, $response);
        }

        $middlewareName = array_shift($middlewares);
        $middlewareClass = $this->middlewareMap[$middlewareName] ?? null;

        if (!$middlewareClass) {
            return $this->runMiddlewarePipeline($middlewares, $request, $response, $destination);
        }

        $middlewareInstance = new $middlewareClass();
        return $middlewareInstance->handle($request, $response, function($req, $res) use ($middlewares, $destination) {
            return $this->runMiddlewarePipeline($middlewares, $req, $res, $destination);
        });
    }

    public function getNamedRouteUrl(string $name, array $params = []): string
    {
        $route = null;
        foreach ($this->routes as $method => $list) {
            foreach ($list as $r) {
                if ($r->name === $name) {
                    $route = $r;
                    break 2;
                }
            }
        }

        if (!$route) {
            return '#';
        }

        $path = $route->path;
        foreach ($params as $key => $val) {
            $path = str_replace('{' . $key . '}', urlencode($val), $path);
        }
        return $path;
    }
}
