<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;

class TenantController extends BaseController
{
    public function __construct()
    {
        $this->setLayout('tenant');
    }

    public function dashboard(Request $request, Response $response)
    {
        return $this->render('tenant/dashboard', [
            'title' => 'Tenant Dashboard',
            'recentActivities' => [
                ['type' => 'favorite', 'message' => 'You favorited "Luxury 3 Bedroom Flat"', 'time' => '2 hours ago'],
                ['type' => 'view', 'message' => 'You viewed "Single Room Self-Contain"', 'time' => '1 day ago'],
                ['type' => 'review', 'message' => 'Your review for "Cozy 2 Bedroom Bungalow" was approved', 'time' => '3 days ago']
            ],
            'stats' => [
                'favorites' => 12,
                'viewed' => 38,
                'reviews' => 4,
                'notifications' => 3
            ]
        ]);
    }

    public function favorites(Request $request, Response $response)
    {
        $listings = [
            [
                'id' => 1,
                'title' => 'Luxury 3 Bedroom Flat',
                'location' => 'Kofar Gabas, Babura',
                'price' => 150000,
                'period' => 'year',
                'beds' => 3,
                'baths' => 2,
                'size' => 120,
                'image' => '/assets/images/house1.jpg',
                'type' => 'Flat',
                'verified' => true
            ],
            [
                'id' => 2,
                'title' => 'Cozy 2 Bedroom Bungalow',
                'location' => 'Sabo Gari, Babura',
                'price' => 120000,
                'period' => 'year',
                'beds' => 2,
                'baths' => 2,
                'size' => 95,
                'image' => '/assets/images/house2.jpg',
                'type' => 'Bungalow',
                'verified' => true
            ]
        ];

        return $this->render('tenant/favorites', [
            'title' => 'My Favorites',
            'listings' => $listings
        ]);
    }

    public function recentlyViewed(Request $request, Response $response)
    {
        $listings = [
            [
                'id' => 3,
                'title' => 'Single Room Self-Contain',
                'location' => 'Near Federal University Dutse Campus, Babura',
                'price' => 45000,
                'period' => 'year',
                'beds' => 1,
                'baths' => 1,
                'size' => 40,
                'image' => '/assets/images/house3.jpg',
                'type' => 'Self-Contain',
                'verified' => false
            ],
            [
                'id' => 4,
                'title' => 'Modern 4 Bedroom Duplex',
                'location' => 'GRA, Babura',
                'price' => 350000,
                'period' => 'year',
                'beds' => 4,
                'baths' => 4,
                'size' => 250,
                'image' => '/assets/images/house4.jpg',
                'type' => 'Duplex',
                'verified' => true
            ]
        ];

        return $this->render('tenant/recently_viewed', [
            'title' => 'Recently Viewed',
            'listings' => $listings
        ]);
    }

    public function notifications(Request $request, Response $response)
    {
        $notifications = [
            [
                'id' => 1,
                'title' => 'Rent Payment Due',
                'message' => 'Your rent payment for Luxury 3 Bedroom Flat is due in 30 days.',
                'date' => 'August 5, 2026',
                'read' => false
            ],
            [
                'id' => 2,
                'title' => 'Listing Update',
                'message' => 'Alhaji Ibrahim Babura added a new listing in Kofar Gabas.',
                'date' => 'August 4, 2026',
                'read' => true
            ]
        ];

        return $this->render('tenant/notifications', [
            'title' => 'Notifications',
            'notifications' => $notifications
        ]);
    }

    public function reviews(Request $request, Response $response)
    {
        $reviews = [
            [
                'id' => 1,
                'house' => 'Luxury 3 Bedroom Flat',
                'rating' => 5,
                'comment' => 'This place is excellent and water flows constantly.',
                'date' => 'July 20, 2026',
                'status' => 'approved'
            ]
        ];

        return $this->render('tenant/reviews', [
            'title' => 'My Reviews',
            'reviews' => $reviews
        ]);
    }

    public function profile(Request $request, Response $response)
    {
        $profile = [
            'name' => Auth::user('name') ?: 'Garba Danladi',
            'email' => Auth::user('email') ?: 'garba.danladi@example.com',
            'phone' => '+234 809 999 8888',
            'address' => 'Sabo Gari, Babura, Jigawa State',
            'occupation' => 'Civil Servant'
        ];

        return $this->render('tenant/profile', [
            'title' => 'My Profile',
            'profile' => $profile
        ]);
    }

    public function settings(Request $request, Response $response)
    {
        return $this->render('tenant/settings', [
            'title' => 'Account Settings'
        ]);
    }
}
