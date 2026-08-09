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
            if (strpos($request->getPath(), '/admin') === 0) {
                Flash::set('error', 'Please sign in to access the administration area.');
                return Redirect::to('/admin/login');
            }
            Flash::set('error', 'Please sign in to access your portal account.');
            return Redirect::to('/login');
        }

        $userId = Auth::user('id');
        $user = \App\Core\Database::fetch("SELECT status FROM users WHERE id = :id", ['id' => $userId]);

        if (!$user || $user['status'] !== 'active') {
            Auth::logout();
            Flash::set('error', 'Your account has been suspended or deactivated.');
            return Redirect::to('/login');
        }

        return $next($request, $response);
    }
}
