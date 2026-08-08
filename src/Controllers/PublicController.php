<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

class PublicController extends BaseController
{
    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        
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
            ],
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

        return $this->render('public/home', [
            'title' => 'A Trusted House Renting Platform for Babura',
            'listings' => $listings
        ]);
    }

    public function browse(Request $request, Response $response)
    {
        $this->setLayout('main');
        
        $body = $request->getBody();
        $location = $body['location'] ?? '';
        $type = $body['type'] ?? '';
        $price = $body['price'] ?? '';

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
            ],
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
            ],
            [
                'id' => 5,
                'title' => 'Standard 2 Bedroom Flat',
                'location' => 'Tashar Dan-Baba, Babura',
                'price' => 80000,
                'period' => 'year',
                'beds' => 2,
                'baths' => 1,
                'size' => 85,
                'image' => '/assets/images/house5.jpg',
                'type' => 'Flat',
                'verified' => true
            ],
            [
                'id' => 6,
                'title' => 'Furnished Room & Parlor Self-Contain',
                'location' => 'Kofar Arewa, Babura',
                'price' => 75000,
                'period' => 'year',
                'beds' => 1,
                'baths' => 1,
                'size' => 55,
                'image' => '/assets/images/house6.jpg',
                'type' => 'Self-Contain',
                'verified' => false
            ]
        ];

        return $this->render('public/browse', [
            'title' => 'Browse Houses in Babura',
            'listings' => $listings,
            'filters' => [
                'location' => $location,
                'type' => $type,
                'price' => $price
            ]
        ]);
    }

    public function details(Request $request, Response $response, $id)
    {
        $this->setLayout('main');
        
        $house = [
            'id' => $id,
            'title' => 'Luxury 3 Bedroom Flat',
            'location' => 'Kofar Gabas, Babura, Jigawa State',
            'price' => 150000,
            'period' => 'year',
            'beds' => 3,
            'baths' => 2,
            'size' => 120,
            'images' => [
                '/assets/images/house1.jpg',
                '/assets/images/house2.jpg',
                '/assets/images/house3.jpg'
            ],
            'type' => 'Flat',
            'verified' => true,
            'description' => 'A beautifully finished 3-bedroom flat situated in a secure and serene location in Kofar Gabas, Babura. This property features a spacious living room, fully tiled floors, fitted kitchen, and robust electricity supply. Close proximity to key landmarks, market, and transportation hubs. Perfect for families or professionals seeking modern comfort.',
            'amenities' => ['Fenced Yard', '24/7 Security Patrol', 'Dedicated Water Pump', 'Prepaid Electricity Meter', 'Ample Parking Space', 'Tiled Floors'],
            'landlord' => [
                'id' => 12,
                'name' => 'Alhaji Ibrahim Babura',
                'avatar' => '/assets/images/landlord1.jpg',
                'verified' => true,
                'phone' => '+234 803 123 4567',
                'joined' => 'June 2024',
                'listings_count' => 8
            ],
            'reviews' => [
                [
                    'name' => 'Musa Haruna',
                    'rating' => 5,
                    'date' => '2 weeks ago',
                    'comment' => 'The property is exactly as shown in the images. Landlord Alhaji Ibrahim is very cooperative and helpful. Water supply is steady!'
                ],
                [
                    'name' => 'Fatima Usman',
                    'rating' => 4,
                    'date' => '1 month ago',
                    'comment' => 'Very clean and spacious rooms. Security is excellent. Only issue is the access road which gets muddy during heavy rains.'
                ]
            ]
        ];

        return $this->render('public/details', [
            'title' => $house['title'],
            'house' => $house
        ]);
    }

    public function landlordProfile(Request $request, Response $response, $id)
    {
        $this->setLayout('main');
        
        $landlord = [
            'id' => $id,
            'name' => 'Alhaji Ibrahim Babura',
            'avatar' => '/assets/images/landlord1.jpg',
            'verified' => true,
            'joined' => 'June 2024',
            'phone' => '+234 803 123 4567',
            'email' => 'ibrahim.babura@example.com',
            'about' => 'Real estate investor and licensed landlord managing rental assets in Babura for over 5 years. Committed to providing premium housing standard with excellent services and quick maintenance response times for tenants.',
            'rating' => 4.8,
            'reviews_count' => 14,
            'listings' => [
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
            ]
        ];

        return $this->render('public/landlord_profile', [
            'title' => $landlord['name'] . ' Profile',
            'landlord' => $landlord
        ]);
    }

    public function about(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('public/about', [
            'title' => 'About Us - Babura House Connect'
        ]);
    }

    public function contact(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('public/contact', [
            'title' => 'Contact Us - Babura House Connect'
        ]);
    }

    public function privacy(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('public/privacy', [
            'title' => 'Privacy Policy - Babura House Connect'
        ]);
    }

    public function terms(Request $request, Response $response)
    {
        $this->setLayout('main');
        return $this->render('public/terms', [
            'title' => 'Terms & Conditions - Babura House Connect'
        ]);
    }
}
