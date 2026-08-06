<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Redirect;
use App\Helpers\Flash;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next)
    {
        if (!Auth::check()) {
            Flash::set('error', 'Please sign in to access your portal account.');
            return Redirect::to('/login');
        }
        return $next($request, $response);
    }
}
