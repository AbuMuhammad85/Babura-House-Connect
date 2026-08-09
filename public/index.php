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
$router->post('/house/{id}/inquire', [PublicController::class, 'sendInquiry']);
$router->post('/house/{id}/report', [PublicController::class, 'reportHouse']);
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
        $tg->post('/favorites/add', [TenantController::class, 'addFavorite']);
        $tg->post('/favorites/remove', [TenantController::class, 'removeFavorite']);
        
        $tg->get('/recently-viewed', [TenantController::class, 'recentlyViewed'])->name('tenant.recently_viewed');
        
        $tg->get('/notifications', [TenantController::class, 'notifications'])->name('tenant.notifications');
        $tg->post('/notifications/mark-read', [TenantController::class, 'markNotificationRead']);
        $tg->post('/notifications/mark-all-read', [TenantController::class, 'markAllNotificationsRead']);
        $tg->post('/notifications/delete', [TenantController::class, 'deleteNotification']);
        
        $tg->get('/reviews', [TenantController::class, 'reviews'])->name('tenant.reviews');
        $tg->post('/reviews/create', [TenantController::class, 'createReview']);
        
        $tg->get('/inquiries', [TenantController::class, 'inquiries'])->name('tenant.inquiries');
        
        $tg->get('/profile', [TenantController::class, 'profile'])->name('tenant.profile');
        $tg->post('/profile', [TenantController::class, 'handleUpdateProfile']);
        
        $tg->get('/settings', [TenantController::class, 'settings'])->name('tenant.settings');
        $tg->post('/settings/password', [TenantController::class, 'handleUpdatePassword']);
    });

    // Landlord Portal
    $r->group(['prefix' => '/landlord', 'middleware' => 'landlord'], function(Router $lg) {
        $lg->get('/dashboard', [LandlordController::class, 'dashboard'])->name('landlord.dashboard');
        $lg->get('/verification', [LandlordController::class, 'verification'])->name('landlord.verification');
        $lg->post('/verification', [LandlordController::class, 'submitVerification']);
        
        $lg->get('/listings', [LandlordController::class, 'manageListings'])->name('landlord.listings');
        $lg->get('/listings/edit/{id}', [LandlordController::class, 'editListing'])->name('landlord.edit_listing');
        $lg->post('/listings/edit/{id}', [LandlordController::class, 'handleEditListing']);
        $lg->post('/listings/deactivate', [LandlordController::class, 'deactivateListing']);
        
        $lg->get('/analytics', [LandlordController::class, 'analytics'])->name('landlord.analytics');
        $lg->get('/subscription', [LandlordController::class, 'subscription'])->name('landlord.subscription');
        
        $lg->get('/profile', [LandlordController::class, 'profile'])->name('landlord.profile');
        $lg->post('/profile', [LandlordController::class, 'handleUpdateProfile']);
        
        $lg->get('/settings', [LandlordController::class, 'settings'])->name('landlord.settings');
        $lg->post('/settings/password', [LandlordController::class, 'handleUpdatePassword']);
        
        $lg->get('/inquiries', [LandlordController::class, 'inquiries'])->name('landlord.inquiries');
        $lg->post('/inquiries/update', [LandlordController::class, 'updateInquiryStatus']);

        $lg->get('/notifications', [LandlordController::class, 'notifications'])->name('landlord.notifications');
        $lg->post('/notifications/mark-read', [LandlordController::class, 'markNotificationRead']);
        $lg->post('/notifications/mark-all-read', [LandlordController::class, 'markAllNotificationsRead']);
        $lg->post('/notifications/delete', [LandlordController::class, 'deleteNotification']);

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
        $ag->post('/verifications/approve', [AdminController::class, 'approveVerification']);
        $ag->post('/verifications/reject', [AdminController::class, 'rejectVerification']);
        $ag->get('/verifications/file/{id}/{type}', [AdminController::class, 'serveFile'])->name('admin.verifications.file');
        $ag->get('/manage-houses', [AdminController::class, 'manageHouses'])->name('admin.manage_houses');
        $ag->post('/houses/approve', [AdminController::class, 'approveListing']);
        $ag->post('/houses/reject', [AdminController::class, 'rejectListing']);
        $ag->post('/houses/deactivate', [AdminController::class, 'deactivateListing']);
        $ag->get('/manage-users', [AdminController::class, 'manageUsers'])->name('admin.manage_users');
        $ag->get('/tenants/view/{id}', [AdminController::class, 'viewTenant']);
        $ag->post('/users/suspend', [AdminController::class, 'suspendUser']);
        $ag->post('/users/activate', [AdminController::class, 'activateUser']);
        
        $ag->get('/manage-reviews', [AdminController::class, 'manageReviews'])->name('admin.manage_reviews');
        $ag->post('/reviews/publish', [AdminController::class, 'publishReview']);
        $ag->post('/reviews/hide', [AdminController::class, 'hideReview']);
        
        $ag->get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        $ag->post('/reports/update', [AdminController::class, 'updateReportStatus']);
        
        $ag->get('/activity-logs', [AdminController::class, 'activityLogs'])->name('admin.activity_logs');
        
        $ag->get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
        $ag->post('/notifications/mark-read', [AdminController::class, 'markNotificationRead']);
        $ag->post('/notifications/mark-all-read', [AdminController::class, 'markAllNotificationsRead']);
        $ag->post('/notifications/delete', [AdminController::class, 'deleteNotification']);
        
        $ag->get('/areas', [AdminController::class, 'areas'])->name('admin.areas');
        $ag->post('/areas/create', [AdminController::class, 'createArea']);
        $ag->post('/areas/toggle', [AdminController::class, 'toggleArea']);
        
        $ag->get('/subscriptions', [AdminController::class, 'subscriptions'])->name('admin.subscriptions');
        $ag->get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
        
        $ag->get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        $ag->post('/settings', [AdminController::class, 'updateSettings']);
    });
});

echo $router->resolve();
