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
        $userId = Auth::user('id');
        $profile = \App\Core\Database::fetch("SELECT * FROM landlord_profiles WHERE user_id = :user_id", ['user_id' => $userId]);
        
        if (!$profile) {
            \App\Helpers\Flash::set('error', 'Landlord profile record not found.');
            return Redirect::to('/');
        }
        
        $landlordId = $profile['id'];

        // Listing counts
        $totalListings = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE landlord_id = :id", ['id' => $landlordId])['total'];
        $publishedListings = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE landlord_id = :id AND status = 'published'", ['id' => $landlordId])['total'];
        $pendingListings = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE landlord_id = :id AND status = 'pending_approval'", ['id' => $landlordId])['total'];
        $rejectedListings = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE landlord_id = :id AND status = 'rejected'", ['id' => $landlordId])['total'];
        $inactiveListings = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE landlord_id = :id AND status = 'archived'", ['id' => $landlordId])['total'];

        // Inquiries counts
        $totalInquiries = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM house_inquiries WHERE landlord_id = :id", ['id' => $landlordId])['total'];
        $pendingInquiries = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM house_inquiries WHERE landlord_id = :id AND status = 'pending'", ['id' => $landlordId])['total'];
        $acceptedInquiries = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM house_inquiries WHERE landlord_id = :id AND status = 'accepted'", ['id' => $landlordId])['total'];

        // Profile metrics
        $ratingAverage = $profile['rating_average'];
        $reviewCount = $profile['rating_count'];
        $unreadNotifications = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND read_at IS NULL", ['user_id' => $userId])['total'];

        // Recent listings (last 3)
        $recentListings = \App\Core\Database::fetchAll(
            "SELECT h.*, a.name AS area_name 
             FROM houses h 
             JOIN areas a ON h.area_id = a.id 
             WHERE h.landlord_id = :id 
             ORDER BY h.id DESC LIMIT 3",
            ['id' => $landlordId]
        );

        // Recent inquiries (last 3)
        $recentInquiries = \App\Core\Database::fetchAll(
            "SELECT i.*, h.title AS house_title, u.full_name AS tenant_name 
             FROM house_inquiries i 
             JOIN houses h ON i.house_id = h.id 
             JOIN users u ON i.tenant_id = u.id 
             WHERE i.landlord_id = :id 
             ORDER BY i.id DESC LIMIT 3",
            ['id' => $landlordId]
        );

        // Recent notifications (last 3)
        $recentNotifications = \App\Core\Database::fetchAll(
            "SELECT * FROM notifications 
             WHERE user_id = :user_id 
             ORDER BY id DESC LIMIT 3",
            ['user_id' => $userId]
        );

        return $this->render('landlord/dashboard', [
            'title' => 'Landlord Dashboard',
            'stats' => [
                'total_listings' => (int)$totalListings,
                'published_listings' => (int)$publishedListings,
                'pending_listings' => (int)$pendingListings,
                'rejected_listings' => (int)$rejectedListings,
                'inactive_listings' => (int)$inactiveListings,
                'total_inquiries' => (int)$totalInquiries,
                'pending_inquiries' => (int)$pendingInquiries,
                'accepted_inquiries' => (int)$acceptedInquiries,
                'rating_average' => $ratingAverage,
                'review_count' => $reviewCount,
                'unread_notifications' => (int)$unreadNotifications
            ],
            'recentListings' => $recentListings,
            'recentInquiries' => $recentInquiries,
            'recentNotifications' => $recentNotifications
        ]);
    }

    public function verification(Request $request, Response $response)
    {
        return $this->render('landlord/verification', [
            'title' => 'Identity Verification'
        ]);
    }

    public function submitVerification(Request $request, Response $response)
    {
        $body = $request->getBody();
        $idType = $body['id_type'] ?? 'NIN';
        $ownershipType = $body['ownership_type'] ?? 'C of O';
        $nin = trim($body['nin'] ?? '');

        // 1. Server-side validation checks
        if (empty($nin) || strlen($nin) < 10) {
            Flash::set('error', 'Please enter a valid National Identification Number (NIN).');
            return Redirect::to('/landlord/verification');
        }

        $files = $_FILES;
        if (empty($files['id_document']['name']) || empty($files['verification_photo']['name']) || empty($files['ownership_document']['name'])) {
            Flash::set('error', 'Please select and upload all required documents.');
            return Redirect::to('/landlord/verification');
        }

        // 2. Hash NIN (never store raw values)
        $ninHash = password_hash($nin, PASSWORD_BCRYPT);
        $ninLast4 = substr($nin, -4);

        // 3. Upload to private storage outside the public web root
        $uploadDir = __DIR__ . '/../../storage/uploads/verifications';
        
        $uploader = new \App\Helpers\Upload();
        
        $idDocPath = $uploader->file($files['id_document'], $uploadDir);
        $selfiePath = $uploader->file($files['verification_photo'], $uploadDir);
        $ownershipPath = $uploader->file($files['ownership_document'], $uploadDir);

        if (!$idDocPath || !$selfiePath || !$ownershipPath) {
            Flash::set('error', 'File upload failed. Ensure all files are under 5MB and are JPG/PNG/PDF formats.');
            return Redirect::to('/landlord/verification');
        }

        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile) {
            Flash::set('error', 'Landlord profile record not found.');
            return Redirect::to('/landlord/verification');
        }

        $landlordId = $landlordProfile['id'];

        // 4. Run database transactions
        \App\Core\Database::beginTransaction();
        try {
            // Update Landlord verification status to pending
            \App\Core\Database::query(
                "UPDATE landlord_profiles SET verification_status = 'pending' WHERE id = :id",
                ['id' => $landlordId]
            );

            // Save documents path as serialized payload
            $docs = json_encode([
                'id_card' => $uploadDir . '/' . $idDocPath,
                'ownership' => $uploadDir . '/' . $ownershipPath,
                'ownership_type' => $ownershipType
            ]);

            \App\Core\Database::query(
                "INSERT INTO landlord_verifications (landlord_id, nin_hash, nin_last4, id_type, id_document_path, verification_photo, status) 
                 VALUES (:landlord_id, :nin_hash, :nin_last4, :id_type, :id_document_path, :verification_photo, 'pending')",
                [
                    'landlord_id' => $landlordId,
                    'nin_hash' => $ninHash,
                    'nin_last4' => $ninLast4,
                    'id_type' => $idType,
                    'id_document_path' => $docs,
                    'verification_photo' => $uploadDir . '/' . $selfiePath
                ]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'LANDLORD_VERIFICATION_SUBMITTED',
                    'description' => 'Landlord verification documents submitted.',
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            // Save system notification for the user
            \App\Core\Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'verification_submitted', 'Verification Submitted', 'Your landlord verification documents have been received and are pending review.')",
                ['user_id' => $userId]
            );

            // Notify admins
            $admins = \App\Core\Database::fetchAll("SELECT id FROM users WHERE role = 'admin'");
            foreach ($admins as $admin) {
                \App\Core\Database::query(
                    "INSERT INTO notifications (user_id, type, title, message) 
                     VALUES (:user_id, 'new_landlord_verification', 'New Landlord Verification', :message)",
                    [
                        'user_id' => $admin['id'],
                        'message' => "A new landlord verification has been submitted by " . Auth::user('full_name') . " and is awaiting review."
                    ]
                );
            }

            \App\Core\Database::commit();
            Flash::set('success', 'Documents uploaded successfully! Awaiting administrator approval.');
            return Redirect::to('/landlord/verification');

        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            Flash::set('error', 'Verification submission failed. Please try again.');
            return Redirect::to('/landlord/verification');
        }
    }

    public function addHouse(Request $request, Response $response)
    {
        $areas = \App\Core\Database::fetchAll("SELECT * FROM areas WHERE is_active = 1 ORDER BY name ASC");
        
        return $this->render('landlord/add_house', [
            'title' => 'Add New House Listing',
            'areas' => $areas
        ]);
    }

    public function handleAddHouse(Request $request, Response $response)
    {
        $body = $request->getBody();
        $title = trim($body['title'] ?? '');
        $areaId = (int) ($body['area_id'] ?? 0);
        $address = trim($body['address'] ?? '');
        $type = $body['type'] ?? 'flat';
        $price = (float) ($body['price'] ?? 0);
        $period = $body['period'] ?? 'year';
        $size = (int) ($body['size'] ?? 0);
        $beds = (int) ($body['beds'] ?? 1);
        $baths = (int) ($body['baths'] ?? 1);
        $description = trim($body['description'] ?? '');
        $amenities = isset($body['amenities']) ? implode(', ', (array) $body['amenities']) : '';

        // 1. Validations
        if (empty($title) || empty($address) || empty($description)) {
            Flash::set('error', 'Please fill in all required text fields.');
            return Redirect::to('/landlord/add-house');
        }

        $area = \App\Core\Database::fetch("SELECT id FROM areas WHERE id = :id AND is_active = 1", ['id' => $areaId]);
        if (!$area) {
            Flash::set('error', 'Selected area is invalid or not in Babura.');
            return Redirect::to('/landlord/add-house');
        }

        if ($price <= 0) {
            Flash::set('error', 'Rent price must be greater than zero.');
            return Redirect::to('/landlord/add-house');
        }

        $validTypes = ['single_room', 'room_and_parlor', 'two_bedroom', 'three_bedroom', 'four_bedroom', 'self_contain', 'flat', 'duplex', 'compound_house', 'shop', 'other'];
        if (!in_array($type, $validTypes)) {
            Flash::set('error', 'Selected property type is invalid.');
            return Redirect::to('/landlord/add-house');
        }

        // 2. Upload images (limit to 5)
        $files = $_FILES;
        $imagePaths = [];
        $uploadDir = __DIR__ . '/../../public/uploads/houses';
        
        $uploader = new \App\Helpers\Upload();
        $uploader->setAllowedTypes(
            ['jpg', 'jpeg', 'png', 'webp'],
            ['image/jpeg', 'image/png', 'image/webp']
        );
        
        if (empty($files['images']['name'][0])) {
            Flash::set('error', 'At least one property image is required.');
            return Redirect::to('/landlord/add-house');
        }

        $count = count($files['images']['name']);
        if ($count > 5) {
            Flash::set('error', 'You can upload a maximum of 5 images.');
            return Redirect::to('/landlord/add-house');
        }
        
        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name' => $files['images']['name'][$i],
                'type' => $files['images']['type'][$i],
                'tmp_name' => $files['images']['tmp_name'][$i],
                'error' => $files['images']['error'][$i],
                'size' => $files['images']['size'][$i]
            ];
            
            $path = $uploader->file($file, $uploadDir);
            if (!$path) {
                Flash::set('error', 'Image upload failed. Supported: JPG, PNG, PDF. Max: 5MB.');
                return Redirect::to('/landlord/add-house');
            }
            $imagePaths[] = '/uploads/houses/' . $path;
        }

        // 3. Upload optional video
        $videoPath = null;
        if (!empty($files['video']['name'])) {
            if ($files['video']['size'] > 20971520) { // 20MB
                Flash::set('error', 'Video file size must be less than 20MB.');
                return Redirect::to('/landlord/add-house');
            }
            
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($files['video']['tmp_name']);
            if (!in_array($mimeType, ['video/mp4', 'video/webm', 'video/ogg'])) {
                Flash::set('error', 'Invalid video format. Only MP4, WebM, and OGG are allowed.');
                return Redirect::to('/landlord/add-house');
            }
            
            $ext = strtolower(pathinfo($files['video']['name'], PATHINFO_EXTENSION));
            $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            if (move_uploaded_file($files['video']['tmp_name'], $uploadDir . '/' . $newFileName)) {
                $videoPath = '/uploads/houses/' . $newFileName;
            }
        }

        // 4. Resolve landlord profile ID from authenticated session
        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile) {
            Flash::set('error', 'Landlord profile record not found.');
            return Redirect::to('/landlord/listings');
        }

        $landlordId = $landlordProfile['id'];
        $repo = new \App\Repositories\HouseRepository();

        \App\Core\Database::beginTransaction();
        try {
            $houseId = $repo->create([
                'landlord_id' => $landlordId,
                'area_id' => $areaId,
                'title' => $title,
                'description' => $description,
                'address' => $address,
                'house_type' => $type,
                'rent_amount' => $price,
                'rent_period' => $period,
                'size' => $size,
                'amenities' => $amenities,
                'bedrooms' => $beds,
                'bathrooms' => $baths,
                'status' => 'pending_approval',
                'availability' => 'available',
                'featured' => 0
            ]);

            $repo->addImages($houseId, $imagePaths);

            if ($videoPath) {
                $repo->addVideo($houseId, $videoPath);
            }

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'HOUSE_CREATED',
                    'description' => "Created house listing: '{$title}' (ID: {$houseId}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            Flash::set('success', 'Listing successfully created and submitted for admin approval!');
            return Redirect::to('/landlord/listings');

        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            Flash::set('error', 'Failed to save property: ' . $e->getMessage());
            return Redirect::to('/landlord/add-house');
        }
    }

    public function manageListings(Request $request, Response $response)
    {
        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile) {
            Flash::set('error', 'Landlord profile record not found.');
            return Redirect::to('/');
        }

        $repo = new \App\Repositories\HouseRepository();
        $listings = $repo->getLandlordListings($landlordProfile['id']);

        return $this->render('landlord/manage_listings', [
            'title' => 'Manage Listings',
            'listings' => $listings
        ]);
    }

    public function editListing(Request $request, Response $response, $id)
    {
        $id = (int)$id;
        $repo = new \App\Repositories\HouseRepository();
        $house = $repo->findById($id);

        if (!$house) {
            $response->setStatusCode(404);
            return $this->render('public/404', ['title' => 'Listing Not Found']);
        }

        // IDOR Protection: Verify owner matching
        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile || $house['landlord_id'] != $landlordProfile['id']) {
            $response->setStatusCode(403);
            return $this->render('public/403', ['title' => 'Access Denied']);
        }

        $areas = \App\Core\Database::fetchAll("SELECT * FROM areas WHERE is_active = 1 ORDER BY name ASC");

        return $this->render('landlord/edit_listing', [
            'title' => 'Edit Listing - ' . $house['title'],
            'house' => $house,
            'areas' => $areas
        ]);
    }

    public function handleEditListing(Request $request, Response $response, $id)
    {
        $id = (int)$id;
        $repo = new \App\Repositories\HouseRepository();
        $house = $repo->findById($id);

        if (!$house) {
            $response->setStatusCode(404);
            return $this->render('public/404', ['title' => 'Listing Not Found']);
        }

        // IDOR Protection
        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile || $house['landlord_id'] != $landlordProfile['id']) {
            $response->setStatusCode(403);
            return $this->render('public/403', ['title' => 'Access Denied']);
        }

        $body = $request->getBody();
        $title = trim($body['title'] ?? '');
        $areaId = (int) ($body['area_id'] ?? 0);
        $address = trim($body['address'] ?? '');
        $type = $body['type'] ?? 'flat';
        $price = (float) ($body['price'] ?? 0);
        $period = $body['period'] ?? 'year';
        $size = (int) ($body['size'] ?? 0);
        $beds = (int) ($body['beds'] ?? 1);
        $baths = (int) ($body['baths'] ?? 1);
        $description = trim($body['description'] ?? '');
        $amenities = isset($body['amenities']) ? implode(', ', (array) $body['amenities']) : '';

        // Validation
        if (empty($title) || empty($address) || empty($description)) {
            Flash::set('error', 'Please fill in all required text fields.');
            return Redirect::to('/landlord/listings/edit/' . $id);
        }

        $area = \App\Core\Database::fetch("SELECT id FROM areas WHERE id = :id AND is_active = 1", ['id' => $areaId]);
        if (!$area) {
            Flash::set('error', 'Selected location area is invalid.');
            return Redirect::to('/landlord/listings/edit/' . $id);
        }

        if ($price <= 0) {
            Flash::set('error', 'Price must be greater than zero.');
            return Redirect::to('/landlord/listings/edit/' . $id);
        }

        $validTypes = ['single_room', 'room_and_parlor', 'two_bedroom', 'three_bedroom', 'four_bedroom', 'self_contain', 'flat', 'duplex', 'compound_house', 'shop', 'other'];
        if (!in_array($type, $validTypes)) {
            Flash::set('error', 'Selected property type is invalid.');
            return Redirect::to('/landlord/listings/edit/' . $id);
        }

        // Substantial modification updates: return to pending approval status!
        $repo->update($id, [
            'area_id' => $areaId,
            'title' => $title,
            'description' => $description,
            'address' => $address,
            'house_type' => $type,
            'rent_amount' => $price,
            'rent_period' => $period,
            'size' => $size,
            'amenities' => $amenities,
            'bedrooms' => $beds,
            'bathrooms' => $baths,
            'status' => 'pending_approval',
            'availability' => $house['availability']
        ]);

        // Log action
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        \App\Core\Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $userId,
                'action' => 'HOUSE_UPDATED',
                'description' => "Modified house listing: '{$title}' (ID: {$id}). Re-submitted for approval.",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        Flash::set('success', 'Property listing updated and re-submitted for approval successfully!');
        return Redirect::to('/landlord/listings');
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
        $userId = Auth::user('id');
        $profileRow = \App\Core\Database::fetch("SELECT * FROM landlord_profiles WHERE user_id = :user_id", ['user_id' => $userId]);

        if (!$profileRow) {
            \App\Core\Database::query("INSERT INTO landlord_profiles (user_id) VALUES (:user_id)", ['user_id' => $userId]);
            $profileRow = \App\Core\Database::fetch("SELECT * FROM landlord_profiles WHERE user_id = :user_id", ['user_id' => $userId]);
        }

        $profile = [
            'name' => Auth::user('full_name'),
            'email' => Auth::user('email'),
            'phone' => Auth::user('phone'),
            'address' => $profileRow['address'] ?? '',
            'bio' => $profileRow['bio'] ?? '',
            'profile_photo' => Auth::user('profile_photo') ?? ''
        ];

        return $this->render('landlord/profile', [
            'title' => 'Landlord Profile',
            'profile' => $profile
        ]);
    }

    public function handleUpdateProfile(Request $request, Response $response)
    {
        $body = $request->getBody();
        $fullName = trim($body['full_name'] ?? '');
        $phone = trim($body['phone'] ?? '');
        $address = trim($body['address'] ?? '');
        $bio = trim($body['bio'] ?? '');

        if (empty($fullName) || empty($phone)) {
            \App\Helpers\Flash::set('error', 'Full Representative Name and Support Call Line are required.');
            return Redirect::to('/landlord/profile');
        }

        $userId = Auth::user('id');

        // Enforce uniqueness of phone number
        $existingPhone = \App\Core\Database::fetch(
            "SELECT id FROM users WHERE phone = :phone AND id != :id",
            ['phone' => $phone, 'id' => $userId]
        );
        if ($existingPhone) {
            \App\Helpers\Flash::set('error', 'This phone number is already registered.');
            return Redirect::to('/landlord/profile');
        }

        // Handle profile photo upload
        $profilePhoto = Auth::user('profile_photo');
        if (isset($_FILES['profile_photo']) && !empty($_FILES['profile_photo']['name'])) {
            $file = $_FILES['profile_photo'];
            
            // Limit size to 5MB
            if ($file['size'] > 5242880) {
                \App\Helpers\Flash::set('error', 'Profile photo size must be less than 5MB.');
                return Redirect::to('/landlord/profile');
            }

            // Validate MIME type using finfo
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
                \App\Helpers\Flash::set('error', 'Invalid profile photo type. Supported: JPG, PNG, WebP.');
                return Redirect::to('/landlord/profile');
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
                \App\Helpers\Flash::set('error', 'Failed to upload profile photo.');
                return Redirect::to('/landlord/profile');
            }
        }

        \App\Core\Database::beginTransaction();
        try {
            // Update users
            \App\Core\Database::query(
                "UPDATE users SET full_name = :name, phone = :phone, profile_photo = :photo WHERE id = :id",
                ['name' => $fullName, 'phone' => $phone, 'photo' => $profilePhoto, 'id' => $userId]
            );

            // Update landlord_profiles
            \App\Core\Database::query(
                "UPDATE landlord_profiles SET address = :address, bio = :bio WHERE user_id = :user_id",
                ['address' => $address, 'bio' => $bio, 'user_id' => $userId]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'PROFILE_UPDATED',
                    'description' => "Updated landlord profile details.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to update profile: ' . $e->getMessage());
        }

        return Redirect::to('/landlord/profile');
    }

    public function settings(Request $request, Response $response)
    {
        return $this->render('landlord/settings', [
            'title' => 'Settings'
        ]);
    }

    public function handleUpdatePassword(Request $request, Response $response)
    {
        $body = $request->getBody();
        $currentPassword = $body['current_password'] ?? '';
        $newPassword = $body['new_password'] ?? '';
        $confirmPassword = $body['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            \App\Helpers\Flash::set('error', 'Please fill in all password fields.');
            return Redirect::to('/landlord/settings');
        }

        if ($newPassword !== $confirmPassword) {
            \App\Helpers\Flash::set('error', 'New passwords do not match.');
            return Redirect::to('/landlord/settings');
        }

        $userId = Auth::user('id');
        $user = \App\Core\Database::fetch("SELECT password_hash FROM users WHERE id = :id", ['id' => $userId]);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            \App\Helpers\Flash::set('error', 'Current password is incorrect.');
            return Redirect::to('/landlord/settings');
        }

        // Hash & Update password
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        \App\Core\Database::query("UPDATE users SET password_hash = :hash WHERE id = :id", ['hash' => $newHash, 'id' => $userId]);

        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        \App\Core\Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $userId,
                'action' => 'PASSWORD_CHANGED',
                'description' => "Changed landlord login password securely.",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        \App\Helpers\Flash::set('success', 'Password updated successfully.');
        return Redirect::to('/landlord/settings');
    }

    public function deactivateListing(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid listing ID.');
            return Redirect::to('/landlord/listings');
        }

        $repo = new \App\Repositories\HouseRepository();
        $house = $repo->findById($id);

        if (!$house) {
            \App\Helpers\Flash::set('error', 'Listing not found.');
            return Redirect::to('/landlord/listings');
        }

        // IDOR Protection
        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile || $house['landlord_id'] != $landlordProfile['id']) {
            \App\Helpers\Flash::set('error', 'You are not authorized to deactivate this listing.');
            return Redirect::to('/landlord/listings');
        }

        \App\Core\Database::beginTransaction();
        try {
            $repo->updateStatus($id, 'archived');

            // Notify favorited users
            $favorites = \App\Core\Database::fetchAll(
                "SELECT tenant_id FROM favorites WHERE house_id = :house_id",
                ['house_id' => $id]
            );
            foreach ($favorites as $fav) {
                \App\Core\Database::query(
                    "INSERT INTO notifications (user_id, type, title, message) 
                     VALUES (:user_id, 'house_unavailable', 'Favorite House Unavailable', :message)",
                    [
                        'user_id' => $fav['tenant_id'],
                        'message' => "The house '{$house['title']}' which you favorited is no longer available."
                    ]
                );
            }

            // Log action
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $userId,
                    'action' => 'HOUSE_DEACTIVATED',
                    'description' => "Deactivated house listing: '{$house['title']}' (ID: {$id}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Property listing deactivated successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to deactivate listing: ' . $e->getMessage());
        }

        return Redirect::to('/landlord/listings');
    }

    public function inquiries(Request $request, Response $response)
    {
        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile) {
            \App\Helpers\Flash::set('error', 'Landlord profile record not found.');
            return Redirect::to('/');
        }

        $inquiries = \App\Core\Database::fetchAll(
            "SELECT i.*, h.title AS house_title, u.full_name AS tenant_name, u.phone AS tenant_phone, u.email AS tenant_email
             FROM house_inquiries i
             JOIN houses h ON i.house_id = h.id
             JOIN users u ON i.tenant_id = u.id
             WHERE i.landlord_id = :landlord_id
             ORDER BY i.id DESC",
            ['landlord_id' => $landlordProfile['id']]
        );

        return $this->render('landlord/inquiries', [
            'title' => 'Rental Inquiries',
            'inquiries' => $inquiries
        ]);
    }

    public function updateInquiryStatus(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);
        $status = trim($body['status'] ?? '');

        if (!$id || !in_array($status, ['pending', 'contacted', 'accepted', 'rejected', 'closed'])) {
            \App\Helpers\Flash::set('error', 'Invalid inquiry ID or status.');
            return Redirect::to('/landlord/inquiries');
        }

        $userId = Auth::user('id');
        $landlordProfile = \App\Core\Database::fetch(
            "SELECT id FROM landlord_profiles WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        if (!$landlordProfile) {
            \App\Helpers\Flash::set('error', 'Landlord profile not found.');
            return Redirect::to('/landlord/inquiries');
        }

        $inquiry = \App\Core\Database::fetch(
            "SELECT landlord_id, house_id, tenant_id FROM house_inquiries WHERE id = :id",
            ['id' => $id]
        );

        // IDOR Protection: Make sure landlord owns the property
        if (!$inquiry || $inquiry['landlord_id'] != $landlordProfile['id']) {
            \App\Helpers\Flash::set('error', 'You are not authorized to update this inquiry.');
            return Redirect::to('/landlord/inquiries');
        }

        \App\Core\Database::query(
            "UPDATE house_inquiries SET status = :status WHERE id = :id",
            ['id' => $id, 'status' => $status]
        );

        // Notify tenant about status change
        $house = \App\Core\Database::fetch("SELECT title FROM houses WHERE id = :id", ['id' => $inquiry['house_id']]);
        
        $msg = "Your inquiry for property '" . $house['title'] . "' has been " . $status . ".";
        if ($status === 'contacted') {
            $msg = "The landlord has contacted you regarding your inquiry for property '" . $house['title'] . "'.";
        } elseif ($status === 'closed') {
            $msg = "Your inquiry for property '" . $house['title'] . "' has been closed.";
        }

        \App\Core\Database::query(
            "INSERT INTO notifications (user_id, type, title, message) 
             VALUES (:user_id, 'inquiry_updated', 'Inquiry Status Updated', :msg)",
            [
                'user_id' => $inquiry['tenant_id'],
                'msg' => $msg
            ]
        );

        // Log action
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        \App\Core\Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $userId,
                'action' => 'INQUIRY_STATUS_UPDATED',
                'description' => "Updated inquiry (ID: {$id}) status to '{$status}'.",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        \App\Helpers\Flash::set('success', 'Inquiry status updated successfully.');
        return Redirect::to('/landlord/inquiries');
    }

    public function notifications(Request $request, Response $response)
    {
        $userId = Auth::user('id');
        $body = $request->getBody();

        $totalRecords = \App\Core\Database::fetch(
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

        $stmt = \App\Core\Database::getConnection()->prepare($sqlQuery);
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

        return $this->render('landlord/notifications', [
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
            \App\Core\Database::query(
                "UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE id = :id AND user_id = :user_id",
                ['id' => $id, 'user_id' => $userId]
            );
            \App\Helpers\Flash::set('success', 'Notification marked as read.');
        }

        return Redirect::to('/landlord/notifications');
    }

    public function markAllNotificationsRead(Request $request, Response $response)
    {
        $userId = Auth::user('id');

        \App\Core\Database::query(
            "UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE user_id = :user_id AND read_at IS NULL",
            ['user_id' => $userId]
        );

        \App\Helpers\Flash::set('success', 'All notifications marked as read.');
        return Redirect::to('/landlord/notifications');
    }

    public function deleteNotification(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);
        $userId = Auth::user('id');

        if ($id) {
            \App\Core\Database::query(
                "DELETE FROM notifications WHERE id = :id AND user_id = :user_id",
                ['id' => $id, 'user_id' => $userId]
            );
            \App\Helpers\Flash::set('success', 'Notification deleted.');
        }

        return Redirect::to('/landlord/notifications');
    }
}
