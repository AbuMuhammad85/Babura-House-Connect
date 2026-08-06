<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next);
}
