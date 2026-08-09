<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Repositories\HouseRepository;
use App\Core\Database;

class PublicController extends BaseController
{
    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        
        $repo = new HouseRepository();
        // Load approved, featured houses first
        $featuredListings = $repo->getApprovedListings(['featured' => 1]);
        
        // Fallback: If no featured listings, load any approved listings
        if (empty($featuredListings)) {
            $featuredListings = $repo->getApprovedListings();
        }

        // Limit to 4 for the homepage
        $listings = array_slice($featuredListings, 0, 4);

        // Map database fields to template keys
        $mappedListings = [];
        foreach ($listings as $house) {
            $mappedListings[] = [
                'id' => $house['id'],
                'title' => $house['title'],
                'location' => $house['area_name'] . ', Babura',
                'price' => $house['rent_amount'],
                'period' => $house['rent_period'],
                'beds' => $house['bedrooms'],
                'baths' => $house['bathrooms'],
                'size' => $house['size'] ?? 0,
                'image' => $house['thumbnail'] ?? '',
                'type' => ucfirst(str_replace('_', ' ', $house['house_type'])),
                'verified' => ($house['landlord_verification'] === 'approved')
            ];
        }

        return $this->render('public/home', [
            'title' => 'A Trusted House Renting Platform for Babura',
            'listings' => $mappedListings
        ]);
    }

    public function browse(Request $request, Response $response)
    {
        $this->setLayout('main');
        
        $body = $request->getBody();
        $areaId = $body['area_id'] ?? '';
        $type = $body['type'] ?? '';
        $price = $body['price'] ?? '';
        $minPrice = $body['min_price'] ?? '';
        $maxPrice = $body['max_price'] ?? '';
        $bedrooms = $body['bedrooms'] ?? '';
        $bathrooms = $body['bathrooms'] ?? '';
        $rentPeriod = $body['rent_period'] ?? '';
        $amenities = $body['amenities'] ?? '';

        // Match location text to area from Home page hero text input
        if (empty($areaId) && !empty($body['location'])) {
            $loc = trim($body['location']);
            $matchedArea = Database::fetch("SELECT id FROM areas WHERE name LIKE :name", ['name' => '%' . $loc . '%']);
            if ($matchedArea) {
                $areaId = $matchedArea['id'];
            }
        }

        $filters = [
            'area_id' => $areaId,
            'type' => $type,
            'price' => $price,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'rent_period' => $rentPeriod,
            'amenities' => $amenities
        ];

        $repo = new HouseRepository();
        $houses = $repo->getApprovedListings($filters);
        $areas = Database::fetchAll("SELECT * FROM areas WHERE is_active = 1 ORDER BY name ASC");

        $mappedListings = [];
        foreach ($houses as $house) {
            $mappedListings[] = [
                'id' => $house['id'],
                'title' => $house['title'],
                'location' => $house['area_name'] . ', Babura',
                'price' => $house['rent_amount'],
                'period' => $house['rent_period'],
                'beds' => $house['bedrooms'],
                'baths' => $house['bathrooms'],
                'size' => $house['size'] ?? 0,
                'image' => $house['thumbnail'] ?? '',
                'type' => ucfirst(str_replace('_', ' ', $house['house_type'])),
                'verified' => ($house['landlord_verification'] === 'approved')
            ];
        }

        return $this->render('public/browse', [
            'title' => 'Browse Houses in Babura',
            'listings' => $mappedListings,
            'areas' => $areas,
            'filters' => $filters
        ]);
    }

    public function details(Request $request, Response $response, $id)
    {
        $this->setLayout('main');
        
        $id = (int)$id;
        $repo = new HouseRepository();
        $house = $repo->findDetailsById($id);

        if (!$house) {
            $response->setStatusCode(404);
            return $this->render('public/404', ['title' => 'Listing Not Found']);
        }

        // Access Control: Block pending or rejected views for general public
        if ($house['status'] !== 'published') {
            $canView = false;
            if (Auth::check()) {
                if (Auth::role() === 'admin') {
                    $canView = true;
                } elseif (Auth::role() === 'landlord') {
                    $profile = Database::fetch("SELECT id FROM landlord_profiles WHERE user_id = :user_id", ['user_id' => Auth::user('id')]);
                    if ($profile && $house['landlord_id'] == $profile['id']) {
                        $canView = true;
                    }
                }
            }
            if (!$canView) {
                $response->setStatusCode(404);
                return $this->render('public/404', ['title' => 'Listing Not Found']);
            }
        }

        // Increment views count and track recently viewed for logged-in tenants
        Database::query("UPDATE houses SET views_count = views_count + 1 WHERE id = :id", ['id' => $id]);
        if (Auth::check() && Auth::role() === 'tenant') {
            Database::query(
                "INSERT INTO recently_viewed (tenant_id, house_id) 
                 VALUES (:tenant_id, :house_id) 
                 ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP",
                [
                    'tenant_id' => Auth::user('id'),
                    'house_id' => $id
                ]
            );
        }

        // Load images and videos
        $images = array_column($repo->getImages($id), 'file_path');
        $video = $repo->getVideo($id);

        $listingsCount = $repo->countApprovedLandlordListings($house['landlord_id']);

        $mappedHouse = [
            'id' => $house['id'],
            'title' => $house['title'],
            'location' => $house['area_name'] . ', Babura',
            'address' => $house['address'],
            'price' => $house['rent_amount'],
            'period' => $house['rent_period'],
            'beds' => $house['bedrooms'],
            'baths' => $house['bathrooms'],
            'size' => $house['size'] ?? 0,
            'images' => $images,
            'video' => $video ? $video['file_path'] : null,
            'type' => ucfirst(str_replace('_', ' ', $house['house_type'])),
            'verified' => ($house['landlord_verification'] === 'approved'),
            'description' => $house['description'],
            'amenities' => array_map('trim', explode(',', $house['amenities'] ?? '')),
            'landlord' => [
                'id' => $house['landlord_id'],
                'name' => $house['landlord_name'],
                'verified' => ($house['landlord_verification'] === 'approved'),
                'phone' => $house['landlord_phone'],
                'joined' => date('M Y', strtotime($house['created_at'])),
                'listings_count' => $listingsCount
            ],
            'reviews' => (function($houseId) {
                $reviewsSql = "SELECT r.*, u.full_name AS reviewer_name, u.profile_photo AS reviewer_photo
                               FROM reviews r
                               JOIN users u ON r.reviewer_id = u.id
                               WHERE r.house_id = :house_id AND r.status = 'published'
                               ORDER BY r.id DESC";
                $reviews = \App\Core\Database::fetchAll($reviewsSql, ['house_id' => $houseId]);
                $mapped = [];
                foreach ($reviews as $rev) {
                    $mapped[] = [
                        'name' => $rev['reviewer_name'],
                        'avatar' => $rev['reviewer_photo'] ?: null,
                        'rating' => $rev['rating'],
                        'comment' => $rev['comment'],
                        'date' => date('F j, Y', strtotime($rev['created_at']))
                    ];
                }
                return $mapped;
            })($id)
        ];

        $isFavorited = false;
        if (Auth::check() && Auth::role() === 'tenant') {
            $existing = Database::fetch(
                "SELECT id FROM favorites WHERE tenant_id = :tenant_id AND house_id = :house_id",
                ['tenant_id' => Auth::user('id'), 'house_id' => $id]
            );
            $isFavorited = ($existing !== false);
        }

        return $this->render('public/details', [
            'title' => $mappedHouse['title'],
            'house' => $mappedHouse,
            'isFavorited' => $isFavorited
        ]);
    }

    public function landlordProfile(Request $request, Response $response, $id)
    {
        $this->setLayout('main');
        
        $id = (int)$id;
        $profile = Database::fetch(
            "SELECT lp.*, u.full_name, u.created_at AS user_created 
             FROM landlord_profiles lp 
             JOIN users u ON lp.user_id = u.id 
             WHERE lp.id = :id",
            ['id' => $id]
        );

        if (!$profile) {
            $response->setStatusCode(404);
            return $this->render('public/404', ['title' => 'Profile Not Found']);
        }

        $repo = new HouseRepository();
        $houses = $repo->getApprovedLandlordListings($id);

        $mappedListings = [];
        foreach ($houses as $house) {
            $mappedListings[] = [
                'id' => $house['id'],
                'title' => $house['title'],
                'location' => $house['area_name'] . ', Babura',
                'price' => $house['rent_amount'],
                'period' => $house['rent_period'],
                'beds' => $house['bedrooms'],
                'baths' => $house['bathrooms'],
                'size' => $house['size'] ?? 0,
                'image' => $house['thumbnail'] ?? '',
                'type' => ucfirst(str_replace('_', ' ', $house['house_type'])),
                'verified' => ($profile['verification_status'] === 'approved')
            ];
        }

        $landlord = [
            'id' => $profile['id'],
            'name' => $profile['full_name'],
            'avatar' => null,
            'verified' => ($profile['verification_status'] === 'approved'),
            'joined' => date('M Y', strtotime($profile['user_created'])),
            'phone' => Auth::check() ? Database::fetch("SELECT phone FROM users WHERE id = :id", ['id' => $profile['user_id']])['phone'] : '[Sign In to View Contact]',
            'email' => Auth::check() ? Database::fetch("SELECT email FROM users WHERE id = :id", ['id' => $profile['user_id']])['email'] : '[Sign In to View Contact]',
            'about' => $profile['bio'] ?: 'No biography added yet.',
            'rating' => $profile['rating_average'],
            'reviews_count' => $profile['rating_count'],
            'listings' => $mappedListings
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

    public function sendInquiry(Request $request, Response $response, $id)
    {
        $id = (int)$id;
        $repo = new HouseRepository();
        $house = $repo->findById($id);

        if (!$house || $house['status'] !== 'published') {
            $response->setStatusCode(404);
            return $this->render('public/404', ['title' => 'Listing Not Found']);
        }

        if (!Auth::check()) {
            \App\Helpers\Flash::set('error', 'You must be logged in to send an inquiry.');
            return \App\Helpers\Redirect::to('/login');
        }

        if (Auth::role() !== 'tenant') {
            \App\Helpers\Flash::set('error', 'Only tenants can send inquiries.');
            return \App\Helpers\Redirect::to('/house/' . $id);
        }

        $body = $request->getBody();
        $message = trim($body['message'] ?? '');

        if (empty($message)) {
            \App\Helpers\Flash::set('error', 'Message cannot be empty.');
            return \App\Helpers\Redirect::to('/house/' . $id);
        }

        $tenantId = Auth::user('id');
        $landlordId = $house['landlord_id'];

        Database::beginTransaction();
        try {
            Database::query(
                "INSERT INTO house_inquiries (house_id, tenant_id, landlord_id, message, status) 
                 VALUES (:house_id, :tenant_id, :landlord_id, :message, 'pending')",
                [
                    'house_id' => $id,
                    'tenant_id' => $tenantId,
                    'landlord_id' => $landlordId,
                    'message' => $message
                ]
            );

            $landlord = Database::fetch("SELECT user_id FROM landlord_profiles WHERE id = :id", ['id' => $landlordId]);

            Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'new_inquiry', 'New Rental Inquiry', :msg)",
                [
                    'user_id' => $landlord['user_id'],
                    'msg' => "You have received a new inquiry from " . Auth::user('full_name') . " regarding your property: '" . $house['title'] . "'."
                ]
            );

            Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'inquiry_sent', 'Inquiry Sent Successfully', :msg)",
                [
                    'user_id' => $tenantId,
                    'msg' => "Your inquiry for property '" . $house['title'] . "' has been sent to the landlord."
                ]
            );

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $tenantId,
                    'action' => 'INQUIRY_SENT',
                    'description' => "Sent inquiry for house: '{$house['title']}' (ID: {$id}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            Database::commit();
            \App\Helpers\Flash::set('success', 'Your inquiry has been successfully sent to the landlord!');
        } catch (\Exception $e) {
            Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to send inquiry: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/house/' . $id);
    }

    public function reportHouse(Request $request, Response $response, $id)
    {
        $id = (int)$id;
        $repo = new HouseRepository();
        $house = $repo->findById($id);

        if (!$house || $house['status'] !== 'published') {
            $response->setStatusCode(404);
            return $this->render('public/404', ['title' => 'Listing Not Found']);
        }

        if (!Auth::check()) {
            \App\Helpers\Flash::set('error', 'You must be logged in to report a listing.');
            return \App\Helpers\Redirect::to('/login');
        }

        $body = $request->getBody();
        $reason = trim($body['reason'] ?? '');
        $description = trim($body['description'] ?? '');

        if (empty($reason) || empty($description)) {
            \App\Helpers\Flash::set('error', 'Please fill in all report fields.');
            return \App\Helpers\Redirect::to('/house/' . $id);
        }

        $reporterId = Auth::user('id');

        $existing = Database::fetch(
            "SELECT id FROM reports WHERE reporter_id = :reporter_id AND house_id = :house_id AND status = 'pending'",
            ['reporter_id' => $reporterId, 'house_id' => $id]
        );

        if ($existing) {
            \App\Helpers\Flash::set('error', 'You have already submitted a pending report for this listing.');
            return \App\Helpers\Redirect::to('/house/' . $id);
        }

        Database::beginTransaction();
        try {
            Database::query(
                "INSERT INTO reports (reporter_id, house_id, landlord_id, reason, description, status) 
                 VALUES (:reporter_id, :house_id, :landlord_id, :reason, :description, 'pending')",
                [
                    'reporter_id' => $reporterId,
                    'house_id' => $id,
                    'landlord_id' => $house['landlord_id'],
                    'reason' => $reason,
                    'description' => $description
                ]
            );

            $admins = Database::fetchAll("SELECT id FROM users WHERE role = 'admin'");
            foreach ($admins as $admin) {
                Database::query(
                    "INSERT INTO notifications (user_id, type, title, message) 
                     VALUES (:user_id, 'new_report', 'New Listing Report', :msg)",
                    [
                        'user_id' => $admin['id'],
                        'msg' => "A new property report was submitted for house: '" . $house['title'] . "' (Reason: " . ucfirst($reason) . ")."
                    ]
                );
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $reporterId,
                    'action' => 'HOUSE_REPORTED',
                    'description' => "Reported property listing (ID: {$id}) for reason: {$reason}.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            Database::commit();
            \App\Helpers\Flash::set('success', 'Thank you. The listing has been reported to administrators for review.');
        } catch (\Exception $e) {
            Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to submit report: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/house/' . $id);
    }
}
