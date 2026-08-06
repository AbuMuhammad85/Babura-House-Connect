<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Controllers\BaseController;

class LandlordMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next)
    {
        if (Auth::role() !== 'landlord') {
            $response->setStatusCode(403);
            $controller = new BaseController();
            echo $controller->render('public/403', ['title' => 'Access Denied']);
            exit;
        }
        return $next($request, $response);
    }
}
