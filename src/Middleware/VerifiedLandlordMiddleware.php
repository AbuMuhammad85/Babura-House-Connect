<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Redirect;
use App\Helpers\Flash;

class VerifiedLandlordMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next)
    {
        if (Auth::role() !== 'landlord') {
            $response->setStatusCode(403);
            $controller = new \App\Controllers\BaseController();
            echo $controller->render('public/403', ['title' => 'Access Denied']);
            exit;
        }

        // Mock check verified field inside the current user details
        $user = Auth::user();
        if (empty($user['verified'])) {
            Flash::set('warning', 'You must verify your identity and property ownership before publishing listings.');
            return Redirect::to('/landlord/verification');
        }

        return $next($request, $response);
    }
}
