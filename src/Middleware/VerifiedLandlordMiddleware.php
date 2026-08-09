<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Redirect;
use App\Helpers\Flash;
use App\Core\Database;

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

        $userId = Auth::user('id');
        $profile = Database::fetch(
            "SELECT verification_status FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$profile || $profile['verification_status'] !== 'approved') {
            Flash::set('warning', 'You must verify your identity and property ownership before publishing listings.');
            return Redirect::to('/landlord/verification');
        }

        return $next($request, $response);
    }
}
