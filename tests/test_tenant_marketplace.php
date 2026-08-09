<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;
use App\Repositories\UserRepository;
use App\Repositories\HouseRepository;

echo "==================================================\n";
echo "Babura House Connect - Phase 7 Tenant & Connection Integration Test\n";
echo "==================================================\n";

Database::beginTransaction();
try {
    // 0. Setup Clean Test Accounts
    Database::query("DELETE FROM users WHERE email IN ('tenant_test_p7@example.com', 'landlord_test_p7@example.com', 'admin_test_p7@example.com')");

    $userRepo = new UserRepository();
    $houseRepo = new HouseRepository();

    // 1. Create Tenant
    $tenantUserId = $userRepo->create([
        'role' => 'tenant',
        'full_name' => 'Garba Danladi Tenant',
        'email' => 'tenant_test_p7@example.com',
        'phone' => '+234 907 777 8888',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    $userRepo->createTenantProfile($tenantUserId, ['occupation' => 'Civil Servant', 'bio' => 'Babura resident']);
    echo "[PASS] TEST 1: Tenant user and profile created successfully.\n";

    // 2. Create Landlord
    $landlordUserId = $userRepo->create([
        'role' => 'landlord',
        'full_name' => 'Alhaji Ibrahim Landlord',
        'email' => 'landlord_test_p7@example.com',
        'phone' => '+234 907 777 9999',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    $userRepo->createLandlordProfile($landlordUserId, ['verification_status' => 'approved']);
    $landlordProfile = Database::fetch("SELECT id FROM landlord_profiles WHERE user_id = :user_id", ['user_id' => $landlordUserId]);
    echo "[PASS] TEST 2: Landlord user and approved profile created successfully.\n";

    // 3. Create Admin
    $adminUserId = $userRepo->create([
        'role' => 'admin',
        'full_name' => 'Marketplace Moderator',
        'email' => 'admin_test_p7@example.com',
        'phone' => '+234 907 777 0000',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    echo "[PASS] TEST 3: Admin user created successfully.\n";

    // Get an area
    $area = Database::fetch("SELECT id FROM areas LIMIT 1");
    if (!$area) {
        throw new Exception("No active areas found in DB.");
    }
    $areaId = $area['id'];

    // 4. Create an approved property listing
    $houseId = $houseRepo->create([
        'landlord_id' => $landlordProfile['id'],
        'area_id' => $areaId,
        'title' => 'Approved Cozy House 2BDR',
        'description' => 'Beautiful property close to university, steady light.',
        'address' => 'Federal University Road, Babura',
        'house_type' => 'two_bedroom',
        'rent_amount' => 150000.00,
        'rent_period' => 'year',
        'size' => 105,
        'amenities' => 'Steady Water Pump, Prepaid Meter',
        'bedrooms' => 2,
        'bathrooms' => 2,
        'status' => 'published'
    ]);
    echo "[PASS] TEST 4: Approved public listing created (ID: {$houseId}).\n";

    // 5. Create a draft (hidden) property listing
    $draftHouseId = $houseRepo->create([
        'landlord_id' => $landlordProfile['id'],
        'area_id' => $areaId,
        'title' => 'Draft House Not Public',
        'description' => 'Not verified yet.',
        'address' => 'Sabo Gari, Babura',
        'house_type' => 'flat',
        'rent_amount' => 80000.00,
        'rent_period' => 'year',
        'size' => 90,
        'amenities' => 'Prepaid Meter',
        'bedrooms' => 1,
        'bathrooms' => 1,
        'status' => 'draft'
    ]);
    echo "[PASS] TEST 5: Draft house listing created (ID: {$draftHouseId}).\n";

    // 6. Test public listings filter/visibility
    $approvedListings = $houseRepo->getApprovedListings();
    $foundApproved = false;
    $foundDraft = false;
    foreach ($approvedListings as $h) {
        if ($h['id'] == $houseId) $foundApproved = true;
        if ($h['id'] == $draftHouseId) $foundDraft = true;
    }
    if (!$foundApproved) throw new Exception("Approved listing not found in marketplace query.");
    if ($foundDraft) throw new Exception("Security breach: draft listing is showing on public marketplace query.");
    echo "[PASS] TEST 6: Marketplace visibility rules verified successfully (Draft hidden, Approved visible).\n";

    // 7. Test Favorite Creation & Retrieval
    // Tenant favorites the house
    Database::query(
        "INSERT INTO favorites (tenant_id, house_id) VALUES (:tenant_id, :house_id)",
        ['tenant_id' => $tenantUserId, 'house_id' => $houseId]
    );

    // Try duplicate favorite (should fail due to database UNIQUE constraint)
    $dupFavPassed = false;
    try {
        Database::query(
            "INSERT INTO favorites (tenant_id, house_id) VALUES (:tenant_id, :house_id)",
            ['tenant_id' => $tenantUserId, 'house_id' => $houseId]
        );
    } catch (\PDOException $e) {
        $dupFavPassed = true;
    }
    if (!$dupFavPassed) {
        throw new Exception("Security breach: duplicate favorite was allowed.");
    }
    echo "[PASS] TEST 7: Duplicate favorite blocked by database constraint successfully.\n";

    // Verify favorite count
    $favsCount = Database::fetch("SELECT COUNT(*) AS total FROM favorites WHERE tenant_id = :id", ['id' => $tenantUserId]);
    if ((int)$favsCount['total'] !== 1) {
        throw new Exception("Favorite count is wrong.");
    }
    echo "[PASS] TEST 8: Tenant favorites count loaded correctly.\n";

    // 8. Test Recently Viewed tracking
    Database::query(
        "INSERT INTO recently_viewed (tenant_id, house_id) VALUES (:tenant_id, :house_id)
         ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP",
        ['tenant_id' => $tenantUserId, 'house_id' => $houseId]
    );
    // Track again (duplicate check - should update timestamp instead of duplicating row)
    Database::query(
        "INSERT INTO recently_viewed (tenant_id, house_id) VALUES (:tenant_id, :house_id)
         ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP",
        ['tenant_id' => $tenantUserId, 'house_id' => $houseId]
    );
    $viewCount = Database::fetch("SELECT COUNT(*) AS total FROM recently_viewed WHERE tenant_id = :id", ['id' => $tenantUserId]);
    if ((int)$viewCount['total'] !== 1) {
        throw new Exception("Recently viewed tracking duplicated row instead of updating timestamp.");
    }
    echo "[PASS] TEST 9: Recently viewed update duplicate suppression verified successfully.\n";

    // 9. Tenant Inquiry Creation
    Database::query(
        "INSERT INTO house_inquiries (house_id, tenant_id, landlord_id, message, status) 
         VALUES (:house_id, :tenant_id, :landlord_id, 'Hello, I want to rent this property!', 'pending')",
        [
            'house_id' => $houseId,
            'tenant_id' => $tenantUserId,
            'landlord_id' => $landlordProfile['id']
        ]
    );
    $inquiry = Database::fetch(
        "SELECT * FROM house_inquiries WHERE tenant_id = :tenant_id AND house_id = :house_id",
        ['tenant_id' => $tenantUserId, 'house_id' => $houseId]
    );
    if (!$inquiry || $inquiry['status'] !== 'pending') {
        throw new Exception("Tenant inquiry creation failed.");
    }
    echo "[PASS] TEST 10: Tenant inquiry created and saved with status = pending.\n";

    // 10. Landlord updates inquiry status
    Database::query(
        "UPDATE house_inquiries SET status = 'contacted' WHERE id = :id",
        ['id' => $inquiry['id']]
    );
    $inquiry = Database::fetch("SELECT status FROM house_inquiries WHERE id = :id", ['id' => $inquiry['id']]);
    if ($inquiry['status'] !== 'contacted') {
        throw new Exception("Landlord inquiry status update failed.");
    }
    echo "[PASS] TEST 11: Landlord inquiry status transition to 'contacted' successful.\n";

    // 11. Review Creation
    Database::query(
        "INSERT INTO reviews (reviewer_id, landlord_id, house_id, rating, comment, status) 
         VALUES (:reviewer_id, :landlord_id, :house_id, 5, 'Great place to live!', 'published')",
        [
            'reviewer_id' => $tenantUserId,
            'landlord_id' => $landlordProfile['id'],
            'house_id' => $houseId
        ]
    );
    // Re-calculate landlord averages
    $landlordId = $landlordProfile['id'];
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

    $landlordCheck = Database::fetch("SELECT rating_count, rating_average FROM landlord_profiles WHERE id = :id", ['id' => $landlordId]);
    if ((int)$landlordCheck['rating_count'] !== 1 || (float)$landlordCheck['rating_average'] !== 5.0) {
        throw new Exception("Landlord rating updates failed.");
    }
    echo "[PASS] TEST 12: Tenant review submitted and landlord averages updated successfully.\n";

    Database::rollBack();
    echo "==================================================\n";
    echo "Babura House Connect - All Tenant Marketplace Tests PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    Database::rollBack();
    echo "[FAIL] Integration test failed: " . $e->getMessage() . "\n";
    exit(1);
}
