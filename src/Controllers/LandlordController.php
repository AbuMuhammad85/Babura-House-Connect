<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;

class LandlordController extends BaseController
{
    public function __construct()
    {
        $this->setLayout('landlord');
    }

    public function dashboard(Request $request, Response $response)
    {
        return $this->render('landlord/dashboard', [
            'title' => 'Landlord Dashboard',
            'stats' => [
                'active_listings' => 5,
                'total_views' => 1250,
                'inquiries' => 28,
                'monthly_earnings' => 450000
            ],
            'recentInquiries' => [
                ['name' => 'Garba Danladi', 'house' => 'Luxury 3 Bedroom Flat', 'date' => '3 hours ago', 'status' => 'Pending'],
                ['name' => 'Aminu Bello', 'house' => 'Single Room Self-Contain', 'date' => '1 day ago', 'status' => 'Contacted'],
                ['name' => 'Halima Yusuf', 'house' => 'Cozy 2 Bedroom Bungalow', 'date' => '2 days ago', 'status' => 'Closed']
            ]
        ]);
    }

    public function verification(Request $request, Response $response)
    {
        return $this->render('landlord/verification', [
            'title' => 'Identity Verification',
            'status' => 'pending'
        ]);
    }

    public function addHouse(Request $request, Response $response)
    {
        return $this->render('landlord/add_house', [
            'title' => 'Add New House Listing'
        ]);
    }

    public function handleAddHouse(Request $request, Response $response)
    {
        return $response->redirect('/landlord/listings');
    }

    public function manageListings(Request $request, Response $response)
    {
        $listings = [
            [
                'id' => 1,
                'title' => 'Luxury 3 Bedroom Flat',
                'location' => 'Kofar Gabas, Babura',
                'price' => 150000,
                'status' => 'Active',
                'views' => 420,
                'verified' => true
            ],
            [
                'id' => 3,
                'title' => 'Single Room Self-Contain',
                'location' => 'Near Federal University Dutse Campus, Babura',
                'price' => 45000,
                'status' => 'Pending Review',
                'views' => 88,
                'verified' => false
            ]
        ];

        return $this->render('landlord/manage_listings', [
            'title' => 'Manage Listings',
            'listings' => $listings
        ]);
    }

    public function editListing(Request $request, Response $response, $id)
    {
        $house = [
            'id' => $id,
            'title' => 'Luxury 3 Bedroom Flat',
            'location' => 'Kofar Gabas, Babura',
            'price' => 150000,
            'period' => 'year',
            'beds' => 3,
            'baths' => 2,
            'size' => 120,
            'description' => 'A beautifully finished 3-bedroom flat situated in a secure and serene location in Kofar Gabas, Babura.',
            'amenities' => 'Fenced Yard, 24/7 Security Patrol, Dedicated Water Pump, Prepaid Electricity Meter, Ample Parking Space, Tiled Floors'
        ];

        return $this->render('landlord/edit_listing', [
            'title' => 'Edit Listing - ' . $house['title'],
            'house' => $house
        ]);
    }

    public function handleEditListing(Request $request, Response $response, $id)
    {
        return $response->redirect('/landlord/listings');
    }

    public function analytics(Request $request, Response $response)
    {
        return $this->render('landlord/analytics', [
            'title' => 'Listing Analytics'
        ]);
    }

    public function subscription(Request $request, Response $response)
    {
        return $this->render('landlord/subscription', [
            'title' => 'My Subscription Plans'
        ]);
    }

    public function profile(Request $request, Response $response)
    {
        $profile = [
            'name' => Auth::user('name') ?: 'Alhaji Ibrahim Babura',
            'email' => Auth::user('email') ?: 'ibrahim.babura@example.com',
            'phone' => '+234 803 123 4567',
            'address' => 'Kofar Gabas, Babura, Jigawa State',
            'company' => 'Babura Properties Ltd'
        ];

        return $this->render('landlord/profile', [
            'title' => 'Landlord Profile',
            'profile' => $profile
        ]);
    }

    public function settings(Request $request, Response $response)
    {
        return $this->render('landlord/settings', [
            'title' => 'Settings'
        ]);
    }
}
