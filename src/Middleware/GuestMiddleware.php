<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Redirect;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next)
    {
        if (Auth::check()) {
            $role = Auth::user()['role'] ?? 'tenant';
            return Redirect::to("/{$role}/dashboard");
        }
        return $next($request, $response);
    }
}
