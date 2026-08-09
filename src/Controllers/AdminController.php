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
        $body = $request->getBody();
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        $repo = new \App\Repositories\UserRepository();
        $user = $repo->findByEmail($email);

        if (!$user || $user['role'] !== 'admin' || !password_verify($password, $user['password_hash'])) {
            // Log failure
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $user ? $user['id'] : null,
                    'action' => 'LOGIN_FAILED',
                    'description' => 'Failed admin login attempt for ' . $email,
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Helpers\Flash::set('error', 'Invalid credentials.');
            return \App\Helpers\Redirect::to('/admin/login');
        }

        if ($user['status'] !== 'active') {
            \App\Helpers\Flash::set('error', 'Your administrative account is ' . $user['status'] . '.');
            return \App\Helpers\Redirect::to('/admin/login');
        }

        // Establish session
        \App\Helpers\Auth::login($user);
        $repo->updateLastLogin($user['id']);

        // Log success
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        \App\Core\Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $user['id'],
                'action' => 'LOGIN_SUCCESS',
                'description' => 'Successful admin login for ' . $email,
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        return \App\Helpers\Redirect::to('/admin/dashboard');
    }

    public function dashboard(Request $request, Response $response)
    {
        $adminId = \App\Helpers\Auth::user('id');

        // General Stats
        $totalUsers = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM users")['total'];
        $totalTenants = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM users WHERE role = 'tenant'")['total'];
        $totalLandlords = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM users WHERE role = 'landlord'")['total'];
        
        $verifiedLandlords = \App\Core\Database::fetch(
            "SELECT COUNT(*) AS total FROM landlord_profiles WHERE verification_status = 'approved'"
        )['total'];
        $pendingVerifs = \App\Core\Database::fetch(
            "SELECT COUNT(*) AS total FROM landlord_verifications WHERE status = 'pending'"
        )['total'];

        // Property Stats
        $totalHouses = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses")['total'];
        $pendingListings = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE status = 'pending_approval'")['total'];
        $publishedHouses = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE status = 'published'")['total'];
        $inactiveHouses = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM houses WHERE status = 'archived'")['total'];

        // Interaction Stats
        $pendingInquiries = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM house_inquiries WHERE status = 'pending'")['total'];
        $totalReviews = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM reviews")['total'];
        $pendingReports = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM reports WHERE status = 'pending'")['total'];
        
        $unreadAlerts = \App\Core\Database::fetch(
            "SELECT COUNT(*) AS total FROM notifications WHERE user_id = :admin_id AND read_at IS NULL",
            ['admin_id' => $adminId]
        )['total'];

        // Urgent Verifications (Pending)
        $pendingLandlordsQuery = \App\Core\Database::fetchAll(
            "SELECT lv.id, u.full_name AS name, u.phone, lv.created_at AS date
             FROM landlord_verifications lv
             JOIN landlord_profiles lp ON lv.landlord_id = lp.id
             JOIN users u ON lp.user_id = u.id
             WHERE lv.status = 'pending'
             ORDER BY lv.id DESC
             LIMIT 5"
        );

        $pendingLandlords = [];
        foreach ($pendingLandlordsQuery as $p) {
            $pendingLandlords[] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'phone' => $p['phone'],
                'date' => date('M d, Y', strtotime($p['date']))
            ];
        }

        // Recent Audit Activity Logs
        $recentActivities = \App\Core\Database::fetchAll(
            "SELECT al.*, u.full_name AS user_name 
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.id DESC
             LIMIT 5"
        );

        return $this->render('admin/dashboard', [
            'title' => 'Admin Control Panel',
            'stats' => [
                'total_users' => (int)$totalUsers,
                'total_tenants' => (int)$totalTenants,
                'total_landlords' => (int)$totalLandlords,
                'verified_landlords' => (int)$verifiedLandlords,
                'pending_verifications' => (int)$pendingVerifs,
                'total_houses' => (int)$totalHouses,
                'pending_listings' => (int)$pendingListings,
                'published_houses' => (int)$publishedHouses,
                'inactive_houses' => (int)$inactiveHouses,
                'pending_inquiries' => (int)$pendingInquiries,
                'total_reviews' => (int)$totalReviews,
                'pending_reports' => (int)$pendingReports,
                'unread_admin_notifications' => (int)$unreadAlerts
            ],
            'pendingLandlords' => $pendingLandlords,
            'recentActivities' => $recentActivities
        ]);
    }

    public function verifyLandlords(Request $request, Response $response)
    {
        $body = $request->getBody();
        $status = trim($body['verification_status'] ?? '');

        $sql = "SELECT COUNT(*) AS total 
                FROM landlord_profiles lp
                JOIN users u ON lp.user_id = u.id";
        $params = [];

        if (!empty($status)) {
            $sql .= " WHERE lp.verification_status = :status";
            $params['status'] = $status;
        }

        $totalRecords = \App\Core\Database::fetch($sql, $params)['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $querySql = "SELECT 
                        lv.id AS verification_id,
                        lv.id_type,
                        lv.nin_last4,
                        lv.created_at AS submitted_at,
                        lp.verification_status,
                        u.full_name AS landlord_name,
                        u.email AS landlord_email,
                        u.phone AS landlord_phone,
                        u.created_at AS registered_at,
                        u.status AS account_status,
                        (SELECT COUNT(*) FROM houses WHERE landlord_id = lp.id AND status = 'published') AS active_listings_count
                     FROM landlord_profiles lp
                     JOIN users u ON lp.user_id = u.id
                     LEFT JOIN landlord_verifications lv ON lv.landlord_id = lp.id AND lv.status = lp.verification_status";

        if (!empty($status)) {
            $querySql .= " WHERE lp.verification_status = :status";
        }

        $querySql .= " ORDER BY lp.id DESC LIMIT :offset, :limit";
        
        // Prepare parameter values with bind types to prevent SQL injection in limits
        $stmt = \App\Core\Database::getConnection()->prepare($querySql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $landlords = $stmt->fetchAll();

        return $this->render('admin/verify_landlords', [
            'title' => 'Verify Landlords',
            'landlords' => $landlords,
            'filters' => [
                'verification_status' => $status
            ],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function approveVerification(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid verification record.');
            return \App\Helpers\Redirect::to('/admin/verify-landlords');
        }

        $verification = \App\Core\Database::fetch(
            "SELECT landlord_id FROM landlord_verifications WHERE id = :id AND status = 'pending'",
            ['id' => $id]
        );

        if (!$verification) {
            \App\Helpers\Flash::set('error', 'Pending verification record not found.');
            return \App\Helpers\Redirect::to('/admin/verify-landlords');
        }

        $landlordId = $verification['landlord_id'];
        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            // Update profile
            \App\Core\Database::query(
                "UPDATE landlord_profiles SET verification_status = 'approved' WHERE id = :id",
                ['id' => $landlordId]
            );

            // Update verification status
            \App\Core\Database::query(
                "UPDATE landlord_verifications SET 
                    status = 'approved', 
                    reviewed_by = :reviewed_by, 
                    reviewed_at = CURRENT_TIMESTAMP 
                 WHERE id = :id",
                [
                    'id' => $id,
                    'reviewed_by' => $adminId
                ]
            );

            $landlordUser = \App\Core\Database::fetch(
                "SELECT user_id FROM landlord_profiles WHERE id = :id",
                ['id' => $landlordId]
            );

            // Insert system notification
            \App\Core\Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'verification_approved', 'Verification Approved', 'Congratulations! Your identity and ownership documents have been approved by the administrators.')",
                ['user_id' => $landlordUser['user_id']]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'LANDLORD_VERIFIED',
                    'description' => "Verified landlord profile (ID: {$landlordId}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Landlord profile approved successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Transaction failed: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/verify-landlords');
    }

    public function rejectVerification(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);
        $notes = trim($body['notes'] ?? '');

        if (!$id || empty($notes)) {
            \App\Helpers\Flash::set('error', 'Rejection reason and ID are required.');
            return \App\Helpers\Redirect::to('/admin/verify-landlords');
        }

        $verification = \App\Core\Database::fetch(
            "SELECT landlord_id FROM landlord_verifications WHERE id = :id AND status = 'pending'",
            ['id' => $id]
        );

        if (!$verification) {
            \App\Helpers\Flash::set('error', 'Pending verification record not found.');
            return \App\Helpers\Redirect::to('/admin/verify-landlords');
        }

        $landlordId = $verification['landlord_id'];
        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            // Update profile
            \App\Core\Database::query(
                "UPDATE landlord_profiles SET verification_status = 'rejected' WHERE id = :id",
                ['id' => $landlordId]
            );

            // Update verification status
            \App\Core\Database::query(
                "UPDATE landlord_verifications SET 
                    status = 'rejected', 
                    admin_notes = :admin_notes,
                    reviewed_by = :reviewed_by, 
                    reviewed_at = CURRENT_TIMESTAMP 
                 WHERE id = :id",
                [
                    'id' => $id,
                    'admin_notes' => $notes,
                    'reviewed_by' => $adminId
                ]
            );

            $landlordUser = \App\Core\Database::fetch(
                "SELECT user_id FROM landlord_profiles WHERE id = :id",
                ['id' => $landlordId]
            );

            // Insert system notification
            \App\Core\Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'verification_rejected', 'Verification Rejected', :message)",
                [
                    'user_id' => $landlordUser['user_id'],
                    'message' => 'Your verification request was rejected. Reason: ' . $notes
                ]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'LANDLORD_VERIFICATION_REJECTED',
                    'description' => "Rejected landlord profile (ID: {$landlordId}). Reason: {$notes}",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Landlord profile rejected successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Transaction failed: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/verify-landlords');
    }

    public function serveFile(Request $request, Response $response, $id, $type)
    {
        $id = (int)$id;
        $verification = \App\Core\Database::fetch(
            "SELECT id_document_path, verification_photo FROM landlord_verifications WHERE id = :id",
            ['id' => $id]
        );

        if (!$verification) {
            $response->setStatusCode(404);
            return "File Not Found";
        }

        $filePath = '';
        if ($type === 'photo') {
            $filePath = $verification['verification_photo'];
        } elseif ($type === 'id' || $type === 'ownership') {
            $paths = json_decode($verification['id_document_path'], true);
            $filePath = ($type === 'id') ? ($paths['id_card'] ?? '') : ($paths['ownership'] ?? '');
        }

        $realUploadDir = realpath(__DIR__ . '/../../storage/uploads/verifications');
        $realFilePath = realpath($filePath);

        // Path traversal validation check
        if (!$realFilePath || strpos($realFilePath, $realUploadDir) !== 0 || !file_exists($realFilePath)) {
            $response->setStatusCode(404);
            return "File Not Found";
        }

        $mimeType = mime_content_type($realFilePath);
        header("Content-Type: " . $mimeType);
        header("Content-Length: " . filesize($realFilePath));
        header('Content-Disposition: inline; filename="' . basename($realFilePath) . '"');
        readfile($realFilePath);
        exit;
    }

    public function manageHouses(Request $request, Response $response)
    {
        $body = $request->getBody();
        $status = trim($body['status'] ?? '');
        $areaId = (int)($body['area_id'] ?? 0);

        $sqlCount = "SELECT COUNT(*) AS total FROM houses h JOIN areas a ON h.area_id = a.id";
        $params = [];
        $conditions = [];

        if (!empty($status)) {
            $conditions[] = "h.status = :status";
            $params['status'] = $status;
        }
        if ($areaId > 0) {
            $conditions[] = "h.area_id = :area_id";
            $params['area_id'] = $areaId;
        }

        if (!empty($conditions)) {
            $sqlCount .= " WHERE " . implode(" AND ", $conditions);
        }

        $totalRecords = \App\Core\Database::fetch($sqlCount, $params)['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $sqlQuery = "SELECT h.*, a.name AS area_name, u.full_name AS landlord_name
                     FROM houses h
                     JOIN areas a ON h.area_id = a.id
                     JOIN landlord_profiles lp ON h.landlord_id = lp.id
                     JOIN users u ON lp.user_id = u.id";

        if (!empty($conditions)) {
            $sqlQuery .= " WHERE " . implode(" AND ", $conditions);
        }

        $sqlQuery .= " ORDER BY h.id DESC LIMIT :offset, :limit";

        $stmt = \App\Core\Database::getConnection()->prepare($sqlQuery);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $houses = $stmt->fetchAll();

        $areas = \App\Core\Database::fetchAll("SELECT * FROM areas ORDER BY name ASC");

        return $this->render('admin/manage_houses', [
            'title' => 'Manage Listings',
            'houses' => $houses,
            'areas' => $areas,
            'filters' => [
                'status' => $status,
                'area_id' => $areaId
            ],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function approveListing(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid listing ID.');
            return \App\Helpers\Redirect::to('/admin/manage-houses');
        }

        $repo = new \App\Repositories\HouseRepository();
        $house = $repo->findById($id);

        if (!$house) {
            \App\Helpers\Flash::set('error', 'Listing not found.');
            return \App\Helpers\Redirect::to('/admin/manage-houses');
        }

        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            $repo->updateStatus($id, 'published');

            $landlord = \App\Core\Database::fetch(
                "SELECT user_id FROM landlord_profiles WHERE id = :id",
                ['id' => $house['landlord_id']]
            );

            \App\Core\Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'house_approved', 'Listing Approved', :message)",
                [
                    'user_id' => $landlord['user_id'],
                    'message' => "Your house listing '{$house['title']}' has been approved and is now live on the browse portal!"
                ]
            );

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'HOUSE_APPROVED',
                    'description' => "Approved house listing: '{$house['title']}' (ID: {$id}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'House listing approved and published successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to approve house: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/manage-houses');
    }

    public function rejectListing(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);
        $notes = trim($body['notes'] ?? '');

        if (!$id || empty($notes)) {
            \App\Helpers\Flash::set('error', 'Listing ID and rejection notes are required.');
            return \App\Helpers\Redirect::to('/admin/manage-houses');
        }

        $repo = new \App\Repositories\HouseRepository();
        $house = $repo->findById($id);

        if (!$house) {
            \App\Helpers\Flash::set('error', 'Listing not found.');
            return \App\Helpers\Redirect::to('/admin/manage-houses');
        }

        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            $repo->updateStatus($id, 'rejected');

            $landlord = \App\Core\Database::fetch(
                "SELECT user_id FROM landlord_profiles WHERE id = :id",
                ['id' => $house['landlord_id']]
            );

            \App\Core\Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'house_rejected', 'Listing Rejected', :message)",
                [
                    'user_id' => $landlord['user_id'],
                    'message' => "Your house listing '{$house['title']}' was rejected. Reason: {$notes}."
                ]
            );

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'HOUSE_REJECTED',
                    'description' => "Rejected house listing: '{$house['title']}' (ID: {$id}). Reason: {$notes}.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'House listing rejected successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to reject listing: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/manage-houses');
    }

    public function deactivateListing(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid listing ID.');
            return \App\Helpers\Redirect::to('/admin/manage-houses');
        }

        $repo = new \App\Repositories\HouseRepository();
        $house = $repo->findById($id);

        if (!$house) {
            \App\Helpers\Flash::set('error', 'Listing not found.');
            return \App\Helpers\Redirect::to('/admin/manage-houses');
        }

        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            $repo->updateStatus($id, 'archived');

            $landlord = \App\Core\Database::fetch(
                "SELECT user_id FROM landlord_profiles WHERE id = :id",
                ['id' => $house['landlord_id']]
            );

            \App\Core\Database::query(
                "INSERT INTO notifications (user_id, type, title, message) 
                 VALUES (:user_id, 'house_deactivated', 'Listing Deactivated', :message)",
                [
                    'user_id' => $landlord['user_id'],
                    'message' => "Your listing '{$house['title']}' was deactivated by the administration."
                ]
            );

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

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'HOUSE_DEACTIVATED',
                    'description' => "Deactivated house listing: '{$house['title']}' (ID: {$id}) by admin.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'House listing deactivated successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to deactivate listing: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/manage-houses');
    }

    public function manageUsers(Request $request, Response $response)
    {
        $body = $request->getBody();
        $role = trim($body['role'] ?? '');
        $status = trim($body['status'] ?? '');

        $sqlCount = "SELECT COUNT(*) AS total FROM users WHERE 1=1";
        $params = [];

        if (!empty($role)) {
            $sqlCount .= " AND role = :role";
            $params['role'] = $role;
        }

        if (!empty($status)) {
            $sqlCount .= " AND status = :status";
            $params['status'] = $status;
        }

        $totalRecords = \App\Core\Database::fetch($sqlCount, $params)['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $sqlQuery = "SELECT * FROM users WHERE 1=1";
        if (!empty($role)) {
            $sqlQuery .= " AND role = :role";
        }
        if (!empty($status)) {
            $sqlQuery .= " AND status = :status";
        }
        $sqlQuery .= " ORDER BY id DESC LIMIT :offset, :limit";

        $stmt = \App\Core\Database::getConnection()->prepare($sqlQuery);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();

        return $this->render('admin/manage_users', [
            'title' => 'Manage Users',
            'users' => $users,
            'filters' => [
                'role' => $role,
                'status' => $status
            ],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function suspendUser(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid user ID.');
            return \App\Helpers\Redirect::to('/admin/manage-users');
        }

        $adminId = \App\Helpers\Auth::user('id');
        if ($id === (int) $adminId) {
            \App\Helpers\Flash::set('error', 'You cannot suspend your own administrative account.');
            return \App\Helpers\Redirect::to('/admin/manage-users');
        }

        $user = \App\Core\Database::fetch("SELECT full_name FROM users WHERE id = :id", ['id' => $id]);
        if (!$user) {
            \App\Helpers\Flash::set('error', 'User not found.');
            return \App\Helpers\Redirect::to('/admin/manage-users');
        }

        \App\Core\Database::query("UPDATE users SET status = 'suspended' WHERE id = :id", ['id' => $id]);

        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        \App\Core\Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $adminId,
                'action' => 'USER_SUSPENDED',
                'description' => "Suspended user account: '{$user['full_name']}' (ID: {$id}).",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        \App\Helpers\Flash::set('success', 'User account has been suspended.');
        return \App\Helpers\Redirect::to('/admin/manage-users');
    }

    public function activateUser(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid user ID.');
            return \App\Helpers\Redirect::to('/admin/manage-users');
        }

        $adminId = \App\Helpers\Auth::user('id');

        $user = \App\Core\Database::fetch("SELECT full_name FROM users WHERE id = :id", ['id' => $id]);
        if (!$user) {
            \App\Helpers\Flash::set('error', 'User not found.');
            return \App\Helpers\Redirect::to('/admin/manage-users');
        }

        \App\Core\Database::query("UPDATE users SET status = 'active' WHERE id = :id", ['id' => $id]);

        // Log activity
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        \App\Core\Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $adminId,
                'action' => 'USER_ACTIVATED',
                'description' => "Activated user account: '{$user['full_name']}' (ID: {$id}).",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        \App\Helpers\Flash::set('success', 'User account has been activated.');
        return \App\Helpers\Redirect::to('/admin/manage-users');
    }

    public function manageReviews(Request $request, Response $response)
    {
        $body = $request->getBody();
        $rating = trim($body['rating'] ?? '');
        $status = trim($body['status'] ?? '');

        $sqlCount = "SELECT COUNT(*) AS total FROM reviews r JOIN users u ON r.reviewer_id = u.id";
        $params = [];
        $conditions = [];

        if (!empty($rating)) {
            $conditions[] = "r.rating = :rating";
            $params['rating'] = (int)$rating;
        }
        if (!empty($status)) {
            $conditions[] = "r.status = :status";
            $params['status'] = $status;
        }

        if (!empty($conditions)) {
            $sqlCount .= " WHERE " . implode(" AND ", $conditions);
        }

        $totalRecords = \App\Core\Database::fetch($sqlCount, $params)['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $sqlQuery = "SELECT r.*, u.full_name AS user, h.title AS house
                     FROM reviews r
                     JOIN users u ON r.reviewer_id = u.id
                     JOIN houses h ON r.house_id = h.id";

        if (!empty($conditions)) {
            $sqlQuery .= " WHERE " . implode(" AND ", $conditions);
        }

        $sqlQuery .= " ORDER BY r.id DESC LIMIT :offset, :limit";

        $stmt = \App\Core\Database::getConnection()->prepare($sqlQuery);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $reviews = $stmt->fetchAll();

        return $this->render('admin/manage_reviews', [
            'title' => 'Manage Reviews',
            'reviews' => $reviews,
            'filters' => [
                'rating' => $rating,
                'status' => $status
            ],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function publishReview(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid review ID.');
            return \App\Helpers\Redirect::to('/admin/manage-reviews');
        }

        $review = \App\Core\Database::fetch("SELECT landlord_id FROM reviews WHERE id = :id", ['id' => $id]);
        if (!$review) {
            \App\Helpers\Flash::set('error', 'Review not found.');
            return \App\Helpers\Redirect::to('/admin/manage-reviews');
        }

        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            \App\Core\Database::query("UPDATE reviews SET status = 'published' WHERE id = :id", ['id' => $id]);

            // Re-calculate averages for landlord
            $landlordId = $review['landlord_id'];
            $stats = \App\Core\Database::fetch(
                "SELECT COUNT(*) AS total, AVG(rating) AS avg_rating 
                 FROM reviews 
                 WHERE landlord_id = :landlord_id AND status = 'published'",
                ['landlord_id' => $landlordId]
            );

            \App\Core\Database::query(
                "UPDATE landlord_profiles 
                 SET rating_count = :count, rating_average = :avg 
                 WHERE id = :id",
                [
                    'count' => $stats['total'],
                    'avg' => $stats['avg_rating'] ?: 0.00,
                    'id' => $landlordId
                ]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'REVIEW_PUBLISHED',
                    'description' => "Published moderated review (ID: {$id}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Review has been approved and published.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to publish review: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/manage-reviews');
    }

    public function hideReview(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid review ID.');
            return \App\Helpers\Redirect::to('/admin/manage-reviews');
        }

        $review = \App\Core\Database::fetch("SELECT landlord_id FROM reviews WHERE id = :id", ['id' => $id]);
        if (!$review) {
            \App\Helpers\Flash::set('error', 'Review not found.');
            return \App\Helpers\Redirect::to('/admin/manage-reviews');
        }

        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            \App\Core\Database::query("UPDATE reviews SET status = 'hidden' WHERE id = :id", ['id' => $id]);

            // Re-calculate averages for landlord
            $landlordId = $review['landlord_id'];
            $stats = \App\Core\Database::fetch(
                "SELECT COUNT(*) AS total, AVG(rating) AS avg_rating 
                 FROM reviews 
                 WHERE landlord_id = :landlord_id AND status = 'published'",
                ['landlord_id' => $landlordId]
            );

            \App\Core\Database::query(
                "UPDATE landlord_profiles 
                 SET rating_count = :count, rating_average = :avg 
                 WHERE id = :id",
                [
                    'count' => $stats['total'],
                    'avg' => $stats['avg_rating'] ?: 0.00,
                    'id' => $landlordId
                ]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'REVIEW_HIDDEN',
                    'description' => "Hid moderated review (ID: {$id}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Review has been hidden successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to hide review: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/manage-reviews');
    }

    public function reports(Request $request, Response $response)
    {
        $body = $request->getBody();
        $status = trim($body['status'] ?? '');

        $sqlCount = "SELECT COUNT(*) AS total FROM reports";
        $params = [];

        if (!empty($status)) {
            $sqlCount .= " WHERE status = :status";
            $params['status'] = $status;
        }

        $totalRecords = \App\Core\Database::fetch($sqlCount, $params)['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $sqlQuery = "SELECT r.*, u.full_name AS reporter_name, h.title AS house_title
                     FROM reports r
                     LEFT JOIN users u ON r.reporter_id = u.id
                     LEFT JOIN houses h ON r.house_id = h.id";

        if (!empty($status)) {
            $sqlQuery .= " WHERE r.status = :status";
        }

        $sqlQuery .= " ORDER BY r.id DESC LIMIT :offset, :limit";

        $stmt = \App\Core\Database::getConnection()->prepare($sqlQuery);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $reports = $stmt->fetchAll();

        return $this->render('admin/reports', [
            'title' => 'System Reports',
            'reports' => $reports,
            'filters' => [
                'status' => $status
            ],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function updateReportStatus(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);
        $status = trim($body['status'] ?? '');

        if (!$id || !in_array($status, ['pending', 'investigating', 'resolved', 'dismissed'])) {
            \App\Helpers\Flash::set('error', 'Invalid report ID or status.');
            return \App\Helpers\Redirect::to('/admin/reports');
        }

        $report = \App\Core\Database::fetch("SELECT reporter_id FROM reports WHERE id = :id", ['id' => $id]);
        if (!$report) {
            \App\Helpers\Flash::set('error', 'Report record not found.');
            return \App\Helpers\Redirect::to('/admin/reports');
        }

        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            \App\Core\Database::query(
                "UPDATE reports SET 
                    status = :status, 
                    reviewed_by = :reviewed_by, 
                    reviewed_at = CURRENT_TIMESTAMP 
                 WHERE id = :id",
                [
                    'id' => $id,
                    'status' => $status,
                    'reviewed_by' => $adminId
                ]
            );

            // Notify reporter if exists
            if ($report['reporter_id']) {
                \App\Core\Database::query(
                    "INSERT INTO notifications (user_id, type, title, message) 
                     VALUES (:user_id, 'report_updated', 'Complaint Status Update', :msg)",
                    [
                        'user_id' => $report['reporter_id'],
                        'msg' => "The status of your submitted complaint (Report ID: {$id}) has been updated to: " . ucfirst($status) . "."
                    ]
                );
            }

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'REPORT_STATUS_UPDATED',
                    'description' => "Updated complaint report (ID: {$id}) status to '{$status}'.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Complaint report status updated successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to update report status: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/reports');
    }

    public function activityLogs(Request $request, Response $response)
    {
        $body = $request->getBody();
        $totalRecords = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM activity_logs")['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $sqlQuery = "SELECT al.*, u.full_name AS user_name, u.role
                     FROM activity_logs al
                     LEFT JOIN users u ON al.user_id = u.id
                     ORDER BY al.id DESC LIMIT :offset, :limit";

        $stmt = \App\Core\Database::getConnection()->prepare($sqlQuery);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $logs = $stmt->fetchAll();

        return $this->render('admin/activity_logs', [
            'title' => 'System Audit Logs',
            'logs' => $logs,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function areas(Request $request, Response $response)
    {
        $areas = \App\Core\Database::fetchAll("SELECT * FROM areas ORDER BY name ASC");

        return $this->render('admin/areas', [
            'title' => 'Area Management',
            'areas' => $areas
        ]);
    }

    public function createArea(Request $request, Response $response)
    {
        $body = $request->getBody();
        $name = trim($body['name'] ?? '');

        if (empty($name)) {
            \App\Helpers\Flash::set('error', 'Area name cannot be empty.');
            return \App\Helpers\Redirect::to('/admin/areas');
        }

        // Strict validation: Must remain restricted to Babura Town/local areas
        $lowerName = strtolower($name);
        $blacklist = ['kano', 'abuja', 'lagos', 'nigeria', 'london', 'america', 'dutse', 'hadejia'];
        foreach ($blacklist as $term) {
            if (strpos($lowerName, $term) !== false) {
                \App\Helpers\Flash::set('error', 'Area registration is strictly restricted to valid locations within Babura Town.');
                return \App\Helpers\Redirect::to('/admin/areas');
            }
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        // Check if area already exists
        $existing = \App\Core\Database::fetch(
            "SELECT id FROM areas WHERE slug = :slug OR name = :name",
            ['slug' => $slug, 'name' => $name]
        );

        if ($existing) {
            \App\Helpers\Flash::set('error', 'An area with this name or slug already exists.');
            return \App\Helpers\Redirect::to('/admin/areas');
        }

        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            \App\Core\Database::query(
                "INSERT INTO areas (name, slug, is_active) VALUES (:name, :slug, 1)",
                ['name' => $name, 'slug' => $slug]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'AREA_CREATED',
                    'description' => "Registered new Babura area: '{$name}' (Slug: {$slug}).",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'New local Babura area registered successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to register area: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/areas');
    }

    public function toggleArea(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int) ($body['id'] ?? 0);

        if (!$id) {
            \App\Helpers\Flash::set('error', 'Invalid area ID.');
            return \App\Helpers\Redirect::to('/admin/areas');
        }

        $area = \App\Core\Database::fetch("SELECT name, is_active FROM areas WHERE id = :id", ['id' => $id]);
        if (!$area) {
            \App\Helpers\Flash::set('error', 'Area not found.');
            return \App\Helpers\Redirect::to('/admin/areas');
        }

        $newActive = $area['is_active'] ? 0 : 1;
        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::beginTransaction();
        try {
            \App\Core\Database::query(
                "UPDATE areas SET is_active = :is_active WHERE id = :id",
                ['id' => $id, 'is_active' => $newActive]
            );

            // Log activity
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $statusStr = $newActive ? 'activated' : 'deactivated';
            \App\Core\Database::query(
                "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
                [
                    'user_id' => $adminId,
                    'action' => 'AREA_STATUS_TOGGLED',
                    'description' => "Toggled area '{$area['name']}' status to {$statusStr}.",
                    'ip_address' => $ip,
                    'user_agent' => $ua
                ]
            );

            \App\Core\Database::commit();
            \App\Helpers\Flash::set('success', 'Area status updated successfully.');
        } catch (\Exception $e) {
            \App\Core\Database::rollBack();
            \App\Helpers\Flash::set('error', 'Failed to update area status: ' . $e->getMessage());
        }

        return \App\Helpers\Redirect::to('/admin/areas');
    }

    public function subscriptions(Request $request, Response $response)
    {
        return $this->render('admin/subscriptions', [
            'title' => 'Subscription Packages'
        ]);
    }

    public function analytics(Request $request, Response $response)
    {
        $tenantCount = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM users WHERE role = 'tenant'")['total'];
        $landlordCount = \App\Core\Database::fetch("SELECT COUNT(*) AS total FROM users WHERE role = 'landlord'")['total'];
        
        // Premium landlords have active subscription
        $premiumCount = \App\Core\Database::fetch(
            "SELECT COUNT(DISTINCT landlord_id) AS total FROM subscriptions WHERE status = 'active'"
        )['total'];
        $standardCount = max(0, $landlordCount - $premiumCount);

        return $this->render('admin/analytics', [
            'title' => 'Platform Analytics',
            'tenantCount' => $tenantCount,
            'standardLandlordCount' => $standardCount,
            'premiumLandlordCount' => $premiumCount
        ]);
    }

    public function settings(Request $request, Response $response)
    {
        return $this->render('admin/settings', [
            'title' => 'System Settings'
        ]);
    }

    public function updateSettings(Request $request, Response $response)
    {
        $body = $request->getBody();
        $siteName = trim($body['site_name'] ?? '');
        $supportEmail = trim($body['support_email'] ?? '');
        $maintenanceMode = trim($body['maintenance_mode'] ?? '');
        $smtpHost = trim($body['smtp_host'] ?? '');
        $smtpPort = trim($body['smtp_port'] ?? '');
        $smtpEncryption = trim($body['smtp_encryption'] ?? '');

        if (empty($siteName) || empty($supportEmail)) {
            \App\Helpers\Flash::set('error', 'Platform Title and Support Contact Email are required.');
            return \App\Helpers\Redirect::to('/admin/settings');
        }

        $payload = [
            'site_name' => $siteName,
            'support_email' => $supportEmail,
            'maintenance_mode' => ($maintenanceMode === '1'),
            'smtp_host' => $smtpHost,
            'smtp_port' => $smtpPort,
            'smtp_encryption' => $smtpEncryption
        ];

        $settingsFile = dirname(__DIR__, 2) . '/storage/settings.json';
        file_put_contents($settingsFile, json_encode($payload, JSON_PRETTY_PRINT));

        $adminId = \App\Helpers\Auth::user('id');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        \App\Core\Database::query(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
             VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
            [
                'user_id' => $adminId,
                'action' => 'SETTINGS_UPDATED',
                'description' => "Updated global settings parameters.",
                'ip_address' => $ip,
                'user_agent' => $ua
            ]
        );

        \App\Helpers\Flash::set('success', 'System configurations updated successfully.');
        return \App\Helpers\Redirect::to('/admin/settings');
    }

    public function viewTenant(Request $request, Response $response, $id)
    {
        $id = (int)$id;
        
        $tenant = \App\Core\Database::fetch(
            "SELECT * FROM users WHERE id = :id",
            ['id' => $id]
        );

        if (!$tenant) {
            \App\Helpers\Flash::set('error', 'User account not found.');
            return \App\Helpers\Redirect::to('/admin/manage-users');
        }

        // Fetch Tenant Favorites
        $favorites = \App\Core\Database::fetchAll(
            "SELECT f.*, h.title, a.name AS area_name, h.rent_amount, h.rent_period
             FROM favorites f
             JOIN houses h ON f.house_id = h.id
             JOIN areas a ON h.area_id = a.id
             WHERE f.tenant_id = :id",
            ['id' => $id]
        );

        // Fetch Tenant Inquiries
        $inquiries = \App\Core\Database::fetchAll(
            "SELECT hi.*, h.title AS house_title, u.full_name AS landlord_name
             FROM house_inquiries hi
             JOIN houses h ON hi.house_id = h.id
             JOIN landlord_profiles lp ON hi.landlord_id = lp.id
             JOIN users u ON lp.user_id = u.id
             WHERE hi.tenant_id = :id
             ORDER BY hi.id DESC",
            ['id' => $id]
        );

        return $this->render('admin/view_tenant', [
            'title' => 'Tenant Details',
            'tenant' => $tenant,
            'favorites' => $favorites,
            'inquiries' => $inquiries
        ]);
    }

    public function notifications(Request $request, Response $response)
    {
        $adminId = \App\Helpers\Auth::user('id');
        $body = $request->getBody();

        $totalRecords = \App\Core\Database::fetch(
            "SELECT COUNT(*) AS total FROM notifications WHERE user_id = :admin_id",
            ['admin_id' => $adminId]
        )['total'];

        $currentPage = (int)($body['page'] ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $limit = 10;
        $totalPages = ceil($totalRecords / $limit) ?: 1;
        $offset = ($currentPage - 1) * $limit;

        $sqlQuery = "SELECT * FROM notifications 
                     WHERE user_id = :admin_id 
                     ORDER BY id DESC LIMIT :offset, :limit";

        $stmt = \App\Core\Database::getConnection()->prepare($sqlQuery);
        $stmt->bindValue(':admin_id', $adminId, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll();

        return $this->render('admin/notifications', [
            'title' => 'Admin Notifications',
            'notifications' => $notifications,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function markNotificationRead(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int)($body['id'] ?? 0);
        $adminId = \App\Helpers\Auth::user('id');

        if ($id) {
            \App\Core\Database::query(
                "UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE id = :id AND user_id = :admin_id",
                ['id' => $id, 'admin_id' => $adminId]
            );
            \App\Helpers\Flash::set('success', 'Notification marked as read.');
        }

        return \App\Helpers\Redirect::to('/admin/notifications');
    }

    public function markAllNotificationsRead(Request $request, Response $response)
    {
        $adminId = \App\Helpers\Auth::user('id');

        \App\Core\Database::query(
            "UPDATE notifications SET read_at = CURRENT_TIMESTAMP WHERE user_id = :admin_id AND read_at IS NULL",
            ['admin_id' => $adminId]
        );

        \App\Helpers\Flash::set('success', 'All notifications marked as read.');
        return \App\Helpers\Redirect::to('/admin/notifications');
    }

    public function deleteNotification(Request $request, Response $response)
    {
        $body = $request->getBody();
        $id = (int)($body['id'] ?? 0);
        $adminId = \App\Helpers\Auth::user('id');

        if ($id) {
            \App\Core\Database::query(
                "DELETE FROM notifications WHERE id = :id AND user_id = :admin_id",
                ['id' => $id, 'admin_id' => $adminId]
            );
            \App\Helpers\Flash::set('success', 'Notification deleted.');
        }

        return \App\Helpers\Redirect::to('/admin/notifications');
    }
}
