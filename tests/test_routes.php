<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Helpers\Auth;
use App\Repositories\UserRepository;

echo "==================================================\n";
echo "Babura House Connect - Complete Route Audit Suite\n";
echo "==================================================\n";

Database::beginTransaction();
try {
    Database::query("DELETE FROM users WHERE email IN ('tenant_route@example.com', 'landlord_route@example.com', 'admin_route@example.com', 'landlord_route_verified@example.com')");

    $userRepo = new UserRepository();

    // Create Test Tenant
    $tenantId = $userRepo->create([
        'role' => 'tenant',
        'full_name' => 'Bello Route Tenant',
        'email' => 'tenant_route@example.com',
        'phone' => '+234 901 000 1111',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);

    // Create Test Landlord (Unverified)
    $landlordId = $userRepo->create([
        'role' => 'landlord',
        'full_name' => 'Musa Route Landlord',
        'email' => 'landlord_route@example.com',
        'phone' => '+234 901 000 2222',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    $userRepo->createLandlordProfile($landlordId, ['verification_status' => 'pending']);

    // Create Test Landlord (Verified)
    $verifiedLandlordId = $userRepo->create([
        'role' => 'landlord',
        'full_name' => 'Musa Route Landlord Verified',
        'email' => 'landlord_route_verified@example.com',
        'phone' => '+234 901 000 4444',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    $userRepo->createLandlordProfile($verifiedLandlordId, ['verification_status' => 'approved']);

    // Create Test Admin
    $adminId = $userRepo->create([
        'role' => 'admin',
        'full_name' => 'Admin Route User',
        'email' => 'admin_route@example.com',
        'phone' => '+234 901 000 3333',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);

    // Fetch user rows
    $tenantUser = $userRepo->findById($tenantId);
    $landlordUser = $userRepo->findById($landlordId);
    $verifiedLandlordUser = $userRepo->findById($verifiedLandlordId);
    $adminUser = $userRepo->findById($adminId);

    // Initialize Request, Response, and Router to retrieve routes
    $request = new Request();
    $response = new Response();
    $router = new Router($request, $response);

    // Include the route definitions to register all routes into $router
    ob_start();
    require __DIR__ . '/../public/index.php';
    ob_end_clean(); // Clean route resolution echo output buffer

    // Extract registered routes list
    $routesProperty = new ReflectionProperty(Router::class, 'routes');
    $routesProperty->setAccessible(true);
    $registeredRoutes = $routesProperty->getValue($router);

    // 1. Verify Public Route Accessibility
    $publicRoutes = [
        'get' => ['/', '/browse', '/about', '/contact', '/privacy', '/terms'],
    ];

    foreach ($publicRoutes['get'] as $path) {
        $found = false;
        foreach ($registeredRoutes['get'] ?? [] as $route) {
            if ($route->path === $path) {
                $found = true;
                // Assert no auth/guest middlewares are assigned to public routes
                if (in_array('auth', $route->middlewares) || in_array('guest', $route->middlewares)) {
                    throw new Exception("Public route '{$path}' contains restrictive middleware.");
                }
            }
        }
        if (!$found) {
            throw new Exception("Required public route '{$path}' is not registered.");
        }
    }
    echo "[PASS] Public route accessibility & middleware verified.\n";

    // 2. Verify Dynamic Route Matches & Prevents Shadowing
    $shadowingTests = [
        '/landlord/dashboard' => '/landlord/dashboard',
        '/landlord/verification' => '/landlord/verification',
        '/landlord/listings' => '/landlord/listings',
        '/landlord/analytics' => '/landlord/analytics',
        '/landlord/profile' => '/landlord/profile',
        '/landlord/settings' => '/landlord/settings',
        '/landlord/inquiries' => '/landlord/inquiries',
        '/landlord/notifications' => '/landlord/notifications',
    ];

    foreach ($shadowingTests as $requestPath => $expectedPath) {
        $matched = null;
        $params = [];
        // First pass: try exact static
        foreach ($registeredRoutes['get'] ?? [] as $route) {
            if (empty($route->paramNames) && $route->matches($requestPath, $params)) {
                $matched = $route;
                break;
            }
        }
        if (!$matched) {
            foreach ($registeredRoutes['get'] ?? [] as $route) {
                if (!empty($route->paramNames) && $route->matches($requestPath, $params)) {
                    $matched = $route;
                    break;
                }
            }
        }
        if (!$matched || $matched->path !== $expectedPath) {
            $matchedPath = $matched ? $matched->path : 'None';
            throw new Exception("Route shadowing collision: '{$requestPath}' resolved to '{$matchedPath}', expected '{$expectedPath}'.");
        }
    }
    echo "[PASS] Dynamic route matching & shadowing prevention verified.\n";

    // 3. Verify Middleware Assignments
    $expectedMiddlewares = [
        '/tenant/dashboard' => ['auth', 'tenant'],
        '/tenant/favorites' => ['auth', 'tenant'],
        '/tenant/notifications' => ['auth', 'tenant'],
        '/landlord/dashboard' => ['auth', 'landlord'],
        '/landlord/verification' => ['auth', 'landlord'],
        '/landlord/add-house' => ['auth', 'landlord', 'verified_landlord'],
        '/admin/dashboard' => ['auth', 'admin'],
        '/admin/verify-landlords' => ['auth', 'admin']
    ];

    foreach ($expectedMiddlewares as $path => $mws) {
        $found = false;
        foreach ($registeredRoutes['get'] ?? [] as $route) {
            if ($route->path === $path) {
                $found = true;
                if ($route->middlewares !== $mws) {
                    $hasMws = implode(', ', $route->middlewares);
                    $needMws = implode(', ', $mws);
                    throw new Exception("Route '{$path}' has incorrect middlewares. Has: [{$hasMws}], Expected: [{$needMws}].");
                }
            }
        }
        if (!$found) {
            throw new Exception("Route '{$path}' not found in router.");
        }
    }
    echo "[PASS] Middleware nesting & role route assignments verified.\n";

    // 4. Verify Auth / Role Cache helper functionality
    // A. Tenant Identity verification
    Auth::login($tenantUser);
    if (!Auth::check()) {
        throw new Exception("Auth::check() failed for logged-in user.");
    }
    if (Auth::role() !== 'tenant') {
        throw new Exception("Auth::role() did not return tenant.");
    }
    echo "[PASS] Tenant role loading verified.\n";

    // B. Landlord Identity verification
    Auth::login($landlordUser);
    if (Auth::role() !== 'landlord') {
        throw new Exception("Auth::role() did not return landlord.");
    }
    $profile = Database::fetch(
        "SELECT verification_status FROM landlord_profiles WHERE user_id = :user_id",
        ['user_id' => $landlordUser['id']]
    );
    if ($profile['verification_status'] !== 'pending') {
        throw new Exception("Landlord profile verification status mismatch.");
    }
    echo "[PASS] Landlord role & verification status verified.\n";

    // C. Verified Landlord Identity verification
    Auth::login($verifiedLandlordUser);
    if (Auth::role() !== 'landlord') {
        throw new Exception("Auth::role() did not return landlord.");
    }
    $vProfile = Database::fetch(
        "SELECT verification_status FROM landlord_profiles WHERE user_id = :user_id",
        ['user_id' => $verifiedLandlordUser['id']]
    );
    if ($vProfile['verification_status'] !== 'approved') {
        throw new Exception("Verified landlord verification status mismatch.");
    }
    echo "[PASS] Verified landlord status verified.\n";

    // D. Admin Identity verification
    Auth::login($adminUser);
    if (Auth::role() !== 'admin') {
        throw new Exception("Auth::role() did not return admin.");
    }
    echo "[PASS] Admin role loading verified.\n";

    // 5. Verify POST Routes Registration
    $expectedPostRoutes = [
        '/login',
        '/register',
        '/tenant/favorites/add',
        '/tenant/favorites/remove',
        '/landlord/verification',
        '/landlord/listings/deactivate',
        '/admin/verifications/approve',
        '/admin/verifications/reject',
    ];

    foreach ($expectedPostRoutes as $path) {
        $found = false;
        foreach ($registeredRoutes['post'] ?? [] as $route) {
            if ($route->path === $path) {
                $found = true;
            }
        }
        if (!$found) {
            throw new Exception("POST route '{$path}' is not registered.");
        }
    }
    echo "[PASS] POST route registration verified.\n";

    // 6. Non-existent route returns 404
    $_GET['url'] = '/nonexistent-path-999';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $badReq = new Request();
    $badRes = new Response();
    $badRouter = new Router($badReq, $badRes);
    // register index.php routes again
    ob_start();
    require __DIR__ . '/../public/index.php';
    ob_end_clean();
    $badResolve = $badRouter->resolve();
    if (http_response_code() !== 404) {
        throw new Exception("Nonexistent route did not return 404 status. Returned status: " . http_response_code());
    }
    echo "[PASS] Nonexistent route handling (404) verified.\n";

    Database::rollBack();
    echo "==================================================\n";
    echo "Babura House Connect - ALL ROUTING CHECKS PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    Database::rollBack();
    echo "[FAIL] Route audit failed: " . $e->getMessage() . "\n";
    exit(1);
}
