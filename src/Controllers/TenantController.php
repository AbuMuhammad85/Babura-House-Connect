<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Core\Database;
use App\Repositories\HouseRepository;

class TenantController extends BaseController
{
    public function __construct()
    {
        $this->setLayout('tenant');
    }

    public function dashboard(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        // Stats counts
        $favs = Database::fetch("SELECT COUNT(*) AS total FROM favorites WHERE tenant_id = :id", ['id' => $userId]);
        $viewed = Database::fetch("SELECT COUNT(*) AS total FROM recently_viewed WHERE tenant_id = :id", ['id' => $userId]);
        $revs = Database::fetch("SELECT COUNT(*) AS total FROM reviews WHERE reviewer_id = :id", ['id' => $userId]);
        $notifs = Database::fetch("SELECT COUNT(*) AS total FROM notifications WHERE user_id = :id AND read_at IS NULL", ['id' => $userId]);
        $activeInquiries = Database::fetch("SELECT COUNT(*) AS total FROM house_inquiries WHERE tenant_id = :id AND status IN ('pending', 'contacted')", ['id' => $userId]);
        $acceptedInquiries = Database::fetch("SELECT COUNT(*) AS total FROM house_inquiries WHERE tenant_id = :id AND status = 'accepted'", ['id' => $userId]);

        // Recent Activities
        $activities = Database::fetchAll(
            "SELECT action, description AS message, created_at AS time 
             FROM activity_logs 
             WHERE user_id = :id 
             ORDER BY id DESC 
             LIMIT 3",
            ['id' => $userId]
        );

        $recentActivities = [];
        foreach ($activities as $act) {
            $timeDiff = time() - strtotime($act['time']);
            if ($timeDiff < 60) {
                $timeStr = 'Just now';
            } elseif ($timeDiff < 3600) {
                $timeStr = floor($timeDiff / 60) . ' mins ago';
            } elseif ($timeDiff < 86400) {
                $timeStr = floor($timeDiff / 3600) . ' hours ago';
            } else {
                $timeStr = date('M d, Y', strtotime($act['time']));
            }

            $recentActivities[] = [
                'message' => $act['message'],
                'time' => $timeStr
            ];
        }

        // 1. Recently Viewed Houses (last 3)
        $recentViewedHouses = Database::fetchAll(
            "SELECT h.*, a.name AS area_name, lp.verification_status AS landlord_verification,
                    (SELECT file_path FROM house_images WHERE house_id = h.id AND is_primary = 1 LIMIT 1) AS thumbnail
             FROM recently_viewed rv
             JOIN houses h ON rv.house_id = h.id
             JOIN areas a ON h.area_id = a.id
             JOIN landlord_profiles lp ON h.landlord_id = lp.id
             WHERE rv.tenant_id = :tenant_id
             ORDER BY rv.updated_at DESC LIMIT 3",
            ['tenant_id' => $userId]
        );

        $viewedListings = [];
        foreach ($recentViewedHouses as $house) {
            $viewedListings[] = [
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

        // 2. Favorite Houses (last 3)
        $favHouses = Database::fetchAll(
            "SELECT h.*, a.name AS area_name, lp.verification_status AS landlord_verification,
                    (SELECT file_path FROM house_images WHERE house_id = h.id AND is_primary = 1 LIMIT 1) AS thumbnail
             FROM favorites f
             JOIN houses h ON f.house_id = h.id
             JOIN areas a ON h.area_id = a.id
             JOIN landlord_profiles lp ON h.landlord_id = lp.id
             WHERE f.tenant_id = :tenant_id
             ORDER BY f.id DESC LIMIT 3",
            ['tenant_id' => $userId]
        );

        $favListings = [];
        foreach ($favHouses as $house) {
            $favListings[] = [
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

        // 3. Recent Inquiries (last 3)
        $recentInquiries = Database::fetchAll(
            "SELECT i.*, h.title AS house_title, u.full_name AS landlord_name
             FROM house_inquiries i
             JOIN houses h ON i.house_id = h.id
             JOIN landlord_profiles lp ON i.landlord_id = lp.id
             JOIN users u ON lp.user_id = u.id
             WHERE i.tenant_id = :tenant_id
             ORDER BY i.id DESC LIMIT 3",
            ['tenant_id' => $userId]
        );

        // 4. Recent Notifications (last 3)
        $recentNotifications = Database::fetchAll(
            "SELECT * FROM notifications 
             WHERE user_id = :user_id 
             ORDER BY id DESC LIMIT 3",
            ['user_id' => $userId]
        );

        return $this->render('tenant/dashboard', [
            'title' => 'Tenant Dashboard',
            'recentActivities' => $recentActivities,
            'viewedListings' => $viewedListings,
            'favListings' => $favListings,
            'recentInquiries' => $recentInquiries,
            'recentNotifications' => $recentNotifications,
            'stats' => [
                'favorites' => (int)$favs['total'],
                'viewed' => (int)$viewed['total'],
                'reviews' => (int)$revs['total'],
                'notifications' => (int)$notifs['total'],
                'active_inquiries' => (int)$activeInquiries['total'],
                'accepted_inquiries' => (int)$acceptedInquiries['total']
            ]
        ]);
    }

    public function favorites(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        $favHouses = Database::fetchAll(
            "SELECT h.*, a.name AS area_name, lp.verification_status AS landlord_verification,
                    (SELECT file_path FROM house_images WHERE house_id = h.id AND is_primary = 1 LIMIT 1) AS thumbnail
             FROM favorites f
             JOIN houses h ON f.house_id = h.id
             JOIN areas a ON h.area_id = a.id
             JOIN landlord_profiles lp ON h.landlord_id = lp.id
             WHERE f.tenant_id = :tenant_id
             ORDER BY f.id DESC",
            ['tenant_id' => $userId]
        );

        $listings = [];
        foreach ($favHouses as $house) {
            $listings[] = [
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

        return $this->render('tenant/favorites', [
            'title' => 'My Favorites',
            'listings' => $listings
        ]);
    }

    public function addFavorite(Request $request, Response $response)
    {
        $body = $request->getBody();
        $houseId = (int) ($body['house_id'] ?? 0);

        if (!$houseId) {
            Flash::set('error', 'Invalid listing reference.');
            return Redirect::to('/browse');
        }

        $userId = Auth::user('id');

        // Check if already favorited
        $existing = Database::fetch(
            "SELECT id FROM favorites WHERE tenant_id = :tenant_id AND house_id = :house_id",
            ['tenant_id' => $userId, 'house_id' => $houseId]
        );

        if (!$existing) {
            Database::query(
                "INSERT INTO favorites (tenant_id, house_id) VALUES (:tenant_id, :house_id)",
                ['tenant_id' => $userId, 'house_id' => $houseId]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'FAVORITE_ADDED',
                    'description' => "Added house listing (ID: {$houseId}) to favorites.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            Flash::set('success', 'Added to favorites.');
        }

        // Return back to previous page
        $referer = $_SERVER['HTTP_REFERER'] ?? '/tenant/favorites';
        return Redirect::to($referer);
    }

    public function removeFavorite(Request $request, Response $response)
    {
        $body = $request->getBody();
        $houseId = (int) ($body['house_id'] ?? 0);

        if (!$houseId) {
            Flash::set('error', 'Invalid listing reference.');
            return Redirect::to('/tenant/favorites');
        }

        $userId = Auth::user('id');

        Database::query(
            "DELETE FROM favorites WHERE tenant_id = :tenant_id AND house_id = :house_id",
            ['tenant_id' => $userId, 'house_id' => $houseId]
        );

        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $userId,
                'action' => 'FAVORITE_REMOVED',
                'description' => "Removed house listing (ID: {$houseId}) from favorites.",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        Flash::set('success', 'Removed from favorites.');
        $referer = $_SERVER['HTTP_REFERER'] ?? '/tenant/favorites';
        return Redirect::to($referer);
    }

    public function recentlyViewed(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        $viewedHouses = Database::fetchAll(
            "SELECT h.*, a.name AS area_name, lp.verification_status AS landlord_verification,
                    (SELECT file_path FROM house_images WHERE house_id = h.id AND is_primary = 1 LIMIT 1) AS thumbnail
             FROM recently_viewed rv
             JOIN houses h ON rv.house_id = h.id
             JOIN areas a ON h.area_id = a.id
             JOIN landlord_profiles lp ON h.landlord_id = lp.id
             WHERE rv.tenant_id = :tenant_id
             ORDER BY rv.updated_at DESC",
            ['tenant_id' => $userId]
        );

        $listings = [];
        foreach ($viewedHouses as $house) {
            $listings[] = [
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

        return $this->render('tenant/recently_viewed', [
            'title' => 'Recently Viewed',
            'listings' => $listings
        ]);
    }

    public function notifications(Request $request, Response $response)
    {
        $userId = Auth::user('id');
        $body = $request->getBody();

        $totalRecords = Database::fetch(
            "SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id",
            ['user_id' => $userId]
        )['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $sqlQuery = "SELECT * FROM notifications 
                     WHERE user_id = :user_id 
                     ORDER BY id DESC LIMIT :offset, :limit";

        $stmt = Database::getConnection()->prepare($sqlQuery);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $notifs = $stmt->fetchAll();

        $notifications = [];
        foreach ($notifs as $n) {
            $notifications[] = [
                'id' => $n['id'],
                'title' => $n['title'],
                'message' => $n['message'],
                'date' => date('F j, Y', strtotime($n['created_at'])),
                'read' => $n['read_at'] !== null
            ];
        }

        return $this->render('tenant/notifications', [
            'title' => 'Notifications',
            'notifications' => $notifications,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function markNotificationRead(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);
        $userId = Auth::user('id');

        if ($id) {
            Database::query(
                "UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE id = :id AND user_id = :user_id",
                ['id' => $id, 'user_id' => $userId]
            );
            Flash::set('success', 'Notification marked as read.');
        }

        return Redirect::to('/tenant/notifications');
    }

    public function markAllNotificationsRead(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        Database::query(
            "UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE user_id = :user_id AND read_at IS NULL",
            ['user_id' => $userId]
        );

        Flash::set('success', 'All notifications marked as read.');
        return Redirect::to('/tenant/notifications');
    }

    public function deleteNotification(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);
        $userId = Auth::user('id');

        if ($id) {
            Database::query(
                "DELETE FROM notifications WHERE id = :id AND user_id = :user_id",
                ['id' => $id, 'user_id' => $userId]
            );
            Flash::set('success', 'Notification deleted.');
        }

        return Redirect::to('/tenant/notifications');
    }

    public function reviews(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        $revs = Database::fetchAll(
            "SELECT r.*, h.title AS house_title 
             FROM reviews r 
             JOIN houses h ON r.house_id = h.id 
             WHERE r.reviewer_id = :id 
             ORDER BY r.id DESC",
            ['id' => $userId]
        );

        $reviews = [];
        foreach ($revs as $rev) {
            $reviews[] = [
                'id' => $rev['id'],
                'house' => $rev['house_title'],
                'rating' => $rev['rating'],
                'comment' => $rev['comment'],
                'date' => date('M d, Y', strtotime($rev['created_at'])),
                'status' => $rev['status']
            ];
        }

        $repo = new HouseRepository();
        $houses = $repo->getApprovedListings();

        return $this->render('tenant/reviews', [
            'title' => 'My Reviews',
            'reviews' => $reviews,
            'houses' => $houses
        ]);
    }

    public function createReview(Request $request, Response $response)
    {
        $body = $request->getBody();
        $houseId = (int) ($body['house_id'] ?? 0);
        $rating = (int) ($body['rating'] ?? 0);
        $comment = trim($body['comment'] ?? '');

        if (!$houseId || $rating < 1 || $rating > 5 || empty($comment)) {
            Flash::set('error', 'All fields are required and rating must be between 1 and 5.');
            return Redirect::to('/tenant/reviews');
        }

        $userId = Auth::user('id');
        $repo = new HouseRepository();
        $house = $repo->findById($houseId);

        if (!$house || $house['status'] !== 'published') {
            Flash::set('error', 'Property listing not found or not published.');
            return Redirect::to('/tenant/reviews');
        }

        // Prevent duplicates
        $existing = Database::fetch(
            "SELECT id FROM reviews WHERE reviewer_id = :reviewer_id AND house_id = :house_id",
            ['reviewer_id' => $userId, 'house_id' => $houseId]
        );

        if ($existing) {
            Flash::set('error', 'You have already reviewed this property listing.');
            return Redirect::to('/tenant/reviews');
        }

        Database::beginTransaction();
        try {
            Database::query(
                "INSERT INTO reviews (reviewer_id, landlord_id, house_id, rating, comment, status) 
                 VALUES (:reviewer_id, :landlord_id, :house_id, :rating, :comment, 'published')",
                [
                    'reviewer_id' => $userId,
                    'landlord_id' => $house['landlord_id'],
                    'house_id' => $houseId,
                    'rating' => $rating,
                    'comment' => $comment
                ]
            );

            // Re-calculate averages for landlord
            $landlordId = $house['landlord_id'];
            $stats = Database::fetch(
                "SELECT COUNT(*) AS total, AVG(rating) AS avg_rating 
                 FROM reviews 
                 WHERE landlord_id = :landlord_id AND status = 'published'",
                ['landlord_id' => $landlordId]
            );

            Database::query(
                "UPDATE landlord_profiles 
                 SET rating_count = :count, rating_average = :avg 
                 WHERE id = :id",
                [
                    'count' => $stats['total'],
                    'avg' => $stats['avg_rating'] ?: 0.00,
                    'id' => $landlordId
                ]
            );

            // Notify Landlord about new review
            $landlordUser = Database::fetch(
                "SELECT user_id FROM landlord_profiles WHERE id = :id",
                ['id' => $landlordId]
            );
            if ($landlordUser) {
                Database::query(
                    "INSERT INTO notifications (user_id, type, title, message) 
                     VALUES (:user_id, 'new_review', 'New Review Received', :msg)",
                    [
                        'user_id' => $landlordUser['user_id'],
                        'msg' => "A tenant has submitted a " . $rating . "-star review on your listing: '" . $house['title'] . "'."
                    ]
                );
            }

            // Notify Tenant review confirmation
            Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'review_submitted', 'Review Submitted', :msg)",
                [
                    'user_id' => $userId,
                    'msg' => "Thank you for reviewing property '" . $house['title'] . "'."
                ]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'REVIEW_CREATED',
                    'description' => "Submitted review for house (ID: {$houseId}). Rating: {$rating}.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            Database::commit();
            Flash::set('success', 'Your review has been successfully published!');
        } catch (\Exception $e) {
            Database::rollBack();
            Flash::set('error', 'Failed to submit review: ' . $e->getMessage());
        }

        return Redirect::to('/tenant/reviews');
    }

    public function profile(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        $profileRow = Database::fetch("SELECT * FROM tenant_profiles WHERE user_id = :user_id", ['user_id' => $userId]);

        // Auto-create tenant profile if missing
        if (!$profileRow) {
            Database::query("INSERT INTO tenant_profiles (user_id) VALUES (:user_id)", ['user_id' => $userId]);
            $profileRow = Database::fetch("SELECT * FROM tenant_profiles WHERE user_id = :user_id", ['user_id' => $userId]);
        }

        $profile = [
            'name' => Auth::user('full_name'),
            'email' => Auth::user('email'),
            'phone' => Auth::user('phone'),
            'address' => $profileRow['bio'] ?? '',
            'occupation' => $profileRow['occupation'] ?? '',
            'profile_photo' => Auth::user('profile_photo') ?? ''
        ];

        return $this->render('tenant/profile', [
            'title' => 'My Profile',
            'profile' => $profile
        ]);
    }

    public function handleUpdateProfile(Request $request, Response $response)
    {
        $body = $request->getBody();
        $fullName = trim($body['full_name'] ?? '');
        $phone = trim($body['phone'] ?? '');
        $occupation = trim($body['occupation'] ?? '');
        $bio = trim($body['bio'] ?? '');

        if (empty($fullName) || empty($phone)) {
            Flash::set('error', 'Full Name and Phone Number are required.');
            return Redirect::to('/tenant/profile');
        }

        $userId = Auth::user('id');

        // Enforce phone uniqueness
        $existingPhone = Database::fetch(
            "SELECT id FROM users WHERE phone = :phone AND id != :id",
            ['phone' => $phone, 'id' => $userId]
        );
        if ($existingPhone) {
            Flash::set('error', 'This phone number is already registered.');
            return Redirect::to('/tenant/profile');
        }

        // Handle profile photo upload
        $profilePhoto = Auth::user('profile_photo');
        if (isset($_FILES['profile_photo']) && !empty($_FILES['profile_photo']['name'])) {
            $file = $_FILES['profile_photo'];
            
            // Limit size to 5MB
            if ($file['size'] > 5242880) {
                Flash::set('error', 'Profile photo size must be less than 5MB.');
                return Redirect::to('/tenant/profile');
            }

            // Validate MIME type using finfo
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
                Flash::set('error', 'Invalid profile photo type. Supported: JPG, PNG, WebP.');
                return Redirect::to('/tenant/profile');
            }

            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/profiles';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $newFileName)) {
                $profilePhoto = '/uploads/profiles/' . $newFileName;
            } else {
                Flash::set('error', 'Failed to upload profile photo.');
                return Redirect::to('/tenant/profile');
            }
        }

        Database::beginTransaction();
        try {
            // Update users table
            Database::query(
                "UPDATE users SET full_name = :name, phone = :phone, profile_photo = :photo WHERE id = :id",
                ['name' => $fullName, 'phone' => $phone, 'photo' => $profilePhoto, 'id' => $userId]
            );

            // Update/Insert tenant_profiles
            $profileExists = Database::fetch("SELECT id FROM tenant_profiles WHERE user_id = :user_id", ['user_id' => $userId]);
            if ($profileExists) {
                Database::query(
                    "UPDATE tenant_profiles SET occupation = :occ, bio = :bio WHERE user_id = :user_id",
                    ['occ' => $occupation, 'bio' => $bio, 'user_id' => $userId]
                );
            } else {
                Database::query(
                    "INSERT INTO tenant_profiles (user_id, occupation, bio) VALUES (:user_id, :occ, :bio)",
                    ['user_id' => $userId, 'occ' => $occupation, 'bio' => $bio]
                );
            }

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'PROFILE_UPDATED',
                    'description' => "Updated tenant profile details.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            Database::commit();
            Flash::set('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Database::rollBack();
            Flash::set('error', 'Failed to update profile: ' . $e->getMessage());
        }

        return Redirect::to('/tenant/profile');
    }

    public function settings(Request $request, Response $response)
    {
        return $this->render('tenant/settings', [
            'title' => 'Account Settings'
        ]);
    }

    public function handleUpdatePassword(Request $request, Response $response)
    {
        $body = $request->getBody();
        $currentPassword = $body['current_password'] ?? '';
        $newPassword = $body['new_password'] ?? '';
        $confirmPassword = $body['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            Flash::set('error', 'Please fill in all password fields.');
            return Redirect::to('/tenant/settings');
        }

        if ($newPassword !== $confirmPassword) {
            Flash::set('error', 'New passwords do not match.');
            return Redirect::to('/tenant/settings');
        }

        $userId = Auth::user('id');
        $user = Database::fetch("SELECT password_hash FROM users WHERE id = :id", ['id' => $userId]);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            Flash::set('error', 'Current password is incorrect.');
            return Redirect::to('/tenant/settings');
        }

        // Hash & Update password
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        Database::query("UPDATE users SET password_hash = :hash WHERE id = :id", ['hash' => $newHash, 'id' => $userId]);

        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $userId,
                'action' => 'PASSWORD_CHANGED',
                'description' => "Changed tenant login password securely.",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        Flash::set('success', 'Password updated successfully.');
        return Redirect::to('/tenant/settings');
    }

    public function inquiries(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        $inquiries = Database::fetchAll(
            "SELECT i.*, h.title AS house_title, u.full_name AS landlord_name
             FROM house_inquiries i
             JOIN houses h ON i.house_id = h.id
             JOIN landlord_profiles lp ON i.landlord_id = lp.id
             JOIN users u ON lp.user_id = u.id
             WHERE i.tenant_id = :tenant_id
             ORDER BY i.id DESC",
            ['tenant_id' => $userId]
        );

        return $this->render('tenant/inquiries', [
            'title' => 'My Rental Inquiries',
            'inquiries' => $inquiries
        ]);
    }
}
