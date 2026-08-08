<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Helpers\URL;
use App\Controllers\PublicController;
use App\Controllers\AuthController;
use App\Controllers\TenantController;
use App\Controllers\LandlordController;
use App\Controllers\AdminController;

$request = new Request();
$response = new Response();
$router = new Router($request, $response);

// Register Router with URL helper
URL::setRouter($router);

// --- PUBLIC PORTAL ROUTES ---
$router->get('/', [PublicController::class, 'index'])->name('home');
$router->get('/browse', [PublicController::class, 'browse'])->name('browse');
$router->get('/house/{id}', [PublicController::class, 'details'])->name('house.details');
$router->get('/landlord/{id}', [PublicController::class, 'landlordProfile'])->name('landlord.public_profile');
$router->get('/about', [PublicController::class, 'about'])->name('about');
$router->get('/contact', [PublicController::class, 'contact'])->name('contact');
$router->get('/privacy', [PublicController::class, 'privacy'])->name('privacy');
$router->get('/terms', [PublicController::class, 'terms'])->name('terms');

// --- GUEST-ONLY AUTHENTICATION ROUTES ---
$router->group(['middleware' => 'guest'], function(Router $r) {
    $r->get('/login', [AuthController::class, 'login'])->name('login');
    $r->post('/login', [AuthController::class, 'handleLogin']);
    $r->get('/register', [AuthController::class, 'register'])->name('register');
    $r->post('/register', [AuthController::class, 'handleRegister']);
    $r->get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot_password');
    $r->post('/forgot-password', [AuthController::class, 'handleForgotPassword']);
    
    $r->get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
    $r->post('/admin/login', [AdminController::class, 'handleLogin']);
});

// --- AUTHORIZED PROTECTED PORTALS ---
$router->group(['middleware' => 'auth'], function(Router $r) {
    $r->get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Tenant Portal
    $r->group(['prefix' => '/tenant', 'middleware' => 'tenant'], function(Router $tg) {
        $tg->get('/dashboard', [TenantController::class, 'dashboard'])->name('tenant.dashboard');
        $tg->get('/favorites', [TenantController::class, 'favorites'])->name('tenant.favorites');
        $tg->get('/recently-viewed', [TenantController::class, 'recentlyViewed'])->name('tenant.recently_viewed');
        $tg->get('/notifications', [TenantController::class, 'notifications'])->name('tenant.notifications');
        $tg->get('/reviews', [TenantController::class, 'reviews'])->name('tenant.reviews');
        $tg->get('/profile', [TenantController::class, 'profile'])->name('tenant.profile');
        $tg->get('/settings', [TenantController::class, 'settings'])->name('tenant.settings');
    });

    // Landlord Portal
    $r->group(['prefix' => '/landlord', 'middleware' => 'landlord'], function(Router $lg) {
        $lg->get('/dashboard', [LandlordController::class, 'dashboard'])->name('landlord.dashboard');
        $lg->get('/verification', [LandlordController::class, 'verification'])->name('landlord.verification');
        
        $lg->get('/listings', [LandlordController::class, 'manageListings'])->name('landlord.listings');
        $lg->get('/listings/edit/{id}', [LandlordController::class, 'editListing'])->name('landlord.edit_listing');
        $lg->post('/listings/edit/{id}', [LandlordController::class, 'handleEditListing']);
        
        $lg->get('/analytics', [LandlordController::class, 'analytics'])->name('landlord.analytics');
        $lg->get('/subscription', [LandlordController::class, 'subscription'])->name('landlord.subscription');
        $lg->get('/profile', [LandlordController::class, 'profile'])->name('landlord.profile');
        $lg->get('/settings', [LandlordController::class, 'settings'])->name('landlord.settings');

        // Verified Landlords Only
        $lg->group(['middleware' => 'verified_landlord'], function(Router $vlg) {
            $vlg->get('/add-house', [LandlordController::class, 'addHouse'])->name('landlord.add_house');
            $vlg->post('/add-house', [LandlordController::class, 'handleAddHouse']);
        });
    });

    // Admin Portal
    $r->group(['prefix' => '/admin', 'middleware' => 'admin'], function(Router $ag) {
        $ag->get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        $ag->get('/verify-landlords', [AdminController::class, 'verifyLandlords'])->name('admin.verify_landlords');
        $ag->get('/manage-houses', [AdminController::class, 'manageHouses'])->name('admin.manage_houses');
        $ag->get('/manage-users', [AdminController::class, 'manageUsers'])->name('admin.manage_users');
        $ag->get('/manage-reviews', [AdminController::class, 'manageReviews'])->name('admin.manage_reviews');
        $ag->get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        $ag->get('/subscriptions', [AdminController::class, 'subscriptions'])->name('admin.subscriptions');
        $ag->get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
        $ag->get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    });
});

echo $router->resolve();
