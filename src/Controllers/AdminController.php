<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;

class AdminController extends BaseController
{
    public function __construct()
    {
        $this->setLayout('admin');
    }

    public function login(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('admin/login', [
            'title' => 'Admin Login - Babura House Connect'
        ]);
    }

    public function handleLogin(Request $request, Response $response)
    {
        return $response->redirect('/admin/dashboard');
    }

    public function dashboard(Request $request, Response $response)
    {
        return $this->render('admin/dashboard', [
            'title' => 'Admin Control Panel',
            'stats' => [
                'total_users' => 1240,
                'total_landlords' => 156,
                'total_listings' => 482,
                'pending_verifications' => 8
            ],
            'pendingLandlords' => [
                ['id' => 1, 'name' => 'Mallam Shehu Babura', 'phone' => '+234 703 999 8888', 'date' => 'Today'],
                ['id' => 2, 'name' => 'Alhaji Yusuf Jigawa', 'phone' => '+234 813 444 5555', 'date' => 'Yesterday']
            ]
        ]);
    }

    public function verifyLandlords(Request $request, Response $response)
    {
        $landlords = [
            ['id' => 1, 'name' => 'Mallam Shehu Babura', 'email' => 'shehu@example.com', 'phone' => '+234 703 999 8888', 'status' => 'Pending Verification', 'date' => 'Today'],
            ['id' => 2, 'name' => 'Alhaji Yusuf Jigawa', 'email' => 'yusuf@example.com', 'phone' => '+234 813 444 5555', 'status' => 'Pending Verification', 'date' => 'Yesterday'],
            ['id' => 3, 'name' => 'Garba Haruna', 'email' => 'garba@example.com', 'phone' => '+234 802 777 6666', 'status' => 'Verified', 'date' => 'August 2, 2026']
        ];

        return $this->render('admin/verify_landlords', [
            'title' => 'Verify Landlords',
            'landlords' => $landlords
        ]);
    }

    public function manageHouses(Request $request, Response $response)
    {
        $houses = [
            ['id' => 1, 'title' => 'Luxury 3 Bedroom Flat', 'landlord' => 'Alhaji Ibrahim Babura', 'price' => 150000, 'status' => 'Approved'],
            ['id' => 3, 'title' => 'Single Room Self-Contain', 'landlord' => 'Mallam Shehu Babura', 'price' => 45000, 'status' => 'Pending Review']
        ];

        return $this->render('admin/manage_houses', [
            'title' => 'Manage Listings',
            'houses' => $houses
        ]);
    }

    public function manageUsers(Request $request, Response $response)
    {
        $users = [
            ['id' => 1, 'name' => 'Garba Danladi', 'email' => 'garba@example.com', 'role' => 'Tenant', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Alhaji Ibrahim Babura', 'email' => 'ibrahim.babura@example.com', 'role' => 'Landlord', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Admin User', 'email' => 'admin@houseconnect.ng', 'role' => 'Admin', 'status' => 'Active']
        ];

        return $this->render('admin/manage_users', [
            'title' => 'Manage Users',
            'users' => $users
        ]);
    }

    public function manageReviews(Request $request, Response $response)
    {
        $reviews = [
            ['id' => 1, 'user' => 'Garba Danladi', 'house' => 'Luxury 3 Bedroom Flat', 'rating' => 5, 'comment' => 'This place is excellent and water flows constantly.', 'status' => 'Approved']
        ];

        return $this->render('admin/manage_reviews', [
            'title' => 'Manage Reviews',
            'reviews' => $reviews
        ]);
    }

    public function reports(Request $request, Response $response)
    {
        return $this->render('admin/reports', [
            'title' => 'System Reports'
        ]);
    }

    public function subscriptions(Request $request, Response $response)
    {
        return $this->render('admin/subscriptions', [
            'title' => 'Subscription Packages'
        ]);
    }

    public function analytics(Request $request, Response $response)
    {
        return $this->render('admin/analytics', [
            'title' => 'Platform Analytics'
        ]);
    }

    public function settings(Request $request, Response $response)
    {
        return $this->render('admin/settings', [
            'title' => 'System Settings'
        ]);
    }
}
