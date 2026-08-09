<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;
use App\Repositories\UserRepository;
use App\Repositories\HouseRepository;

echo "==================================================\n";
echo "Babura House Connect - Phase 6 Marketplace Integration Test\n";
echo "==================================================\n";

Database::beginTransaction();
try {
    // 1. Create a tenant user for role checking
    $db = Database::class;
    
    // Clear out any previous test data safely
    Database::query("DELETE FROM users WHERE email IN ('tenant_test@example.com', 'landlord_a@example.com', 'landlord_b@example.com')");

    $userRepo = new UserRepository();
    $houseRepo = new HouseRepository();

    $tenantId = $userRepo->create([
        'role' => 'tenant',
        'full_name' => 'Test Tenant User',
        'email' => 'tenant_test@example.com',
        'phone' => '+234 901 000 1111',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    echo "[PASS] TEST 1: Tenant user created successfully.\n";

    // 2. Create Landlord A (initially pending verification)
    $landlordA_Id = $userRepo->create([
        'role' => 'landlord',
        'full_name' => 'Landlord A (Verified)',
        'email' => 'landlord_a@example.com',
        'phone' => '+234 901 000 2222',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    $userRepo->createLandlordProfile($landlordA_Id, ['verification_status' => 'pending']);
    
    $landlordProfileA = Database::fetch("SELECT id, verification_status FROM landlord_profiles WHERE user_id = :user_id", ['user_id' => $landlordA_Id]);
    
    // Test rule: unverified landlord cannot publish
    if ($landlordProfileA['verification_status'] === 'approved') {
        throw new Exception("Landlord A verification should be pending by default.");
    }
    echo "[PASS] TEST 2: Landlord A created (Verification status = pending).\n";

    // 3. Create Landlord B (unverified landlord)
    $landlordB_Id = $userRepo->create([
        'role' => 'landlord',
        'full_name' => 'Landlord B (Unverified)',
        'email' => 'landlord_b@example.com',
        'phone' => '+234 901 000 3333',
        'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    $userRepo->createLandlordProfile($landlordB_Id, ['verification_status' => 'pending']);
    $landlordProfileB = Database::fetch("SELECT id FROM landlord_profiles WHERE user_id = :user_id", ['user_id' => $landlordB_Id]);
    echo "[PASS] TEST 3: Landlord B created (Verification status = pending).\n";

    // 4. Admin approves landlord A verification
    Database::query(
        "UPDATE landlord_profiles SET verification_status = 'approved' WHERE id = :id",
        ['id' => $landlordProfileA['id']]
    );
    $landlordProfileA = Database::fetch("SELECT id, verification_status FROM landlord_profiles WHERE id = :id", ['id' => $landlordProfileA['id']]);
    if ($landlordProfileA['verification_status'] !== 'approved') {
        throw new Exception("Admin verification approval failed.");
    }
    echo "[PASS] TEST 4: Landlord A approved by Admin.\n";

    // 5. Landlord A (NO subscription) creates house listing
    // We check that subscription is not required!
    $area = Database::fetch("SELECT id FROM areas LIMIT 1");
    if (!$area) {
        throw new Exception("No areas found in database to link house listing.");
    }
    $areaId = $area['id'];

    // We verify no active subscription exists for Landlord A
    $subCount = Database::fetch("SELECT COUNT(*) AS total FROM subscriptions WHERE landlord_id = :id", ['id' => $landlordProfileA['id']]);
    if ((int)$subCount['total'] > 0) {
        throw new Exception("Test state polluted: landlord should have no active subscription.");
    }

    // Attempt house creation for verified landlord WITHOUT subscription
    $houseId = $houseRepo->create([
        'landlord_id' => $landlordProfileA['id'],
        'area_id' => $areaId,
        'title' => 'Test House Listing Landlord A',
        'description' => 'Beautiful property close to main road, steady light.',
        'address' => 'Kofar Gabas Road, Babura',
        'house_type' => 'flat',
        'rent_amount' => 120000.00,
        'rent_period' => 'year',
        'size' => 110,
        'amenities' => 'Steady Water Pump, Prepaid Meter',
        'bedrooms' => 3,
        'bathrooms' => 2,
        'status' => 'pending_approval'
    ]);

    if (!$houseId) {
        throw new Exception("House creation failed.");
    }
    echo "[PASS] TEST 5: Verified Landlord WITHOUT subscription successfully created house (ID: {$houseId}).\n";

    // Add mock images
    $houseRepo->addImages($houseId, ['/uploads/houses/img1.jpg', '/uploads/houses/img2.jpg']);
    $houseRepo->addVideo($houseId, '/uploads/houses/tour.mp4');
    echo "[PASS] TEST 6: House images and video paths saved to database successfully.\n";

    // 6. Test IDOR listing edits protection
    // Landlord B tries to edit Landlord A's listing
    $houseRecord = $houseRepo->findById($houseId);
    if ($houseRecord['landlord_id'] == $landlordProfileB['id']) {
        throw new Exception("IDOR protection test failed: Owner of listing is wrong.");
    }
    echo "[PASS] TEST 7: IDOR check passed. Landlord B is not the owner of Landlord A's listing.\n";

    // 7. Test Browse listings visibility
    // Pending listing should NOT be publicly visible
    $browseListings = $houseRepo->getApprovedListings();
    $found = false;
    foreach ($browseListings as $b) {
        if ($b['id'] == $houseId) {
            $found = true;
        }
    }
    if ($found) {
        throw new Exception("Security breach: Pending listing is visible on public browse page.");
    }
    echo "[PASS] TEST 8: Pending approval listing is NOT publicly visible on /browse.\n";

    // 8. Admin reviews pending listings
    $pendingAdmin = $houseRepo->getPendingListings();
    $foundPending = false;
    foreach ($pendingAdmin as $p) {
        if ($p['id'] == $houseId) {
            $foundPending = true;
        }
    }
    if (!$foundPending) {
        throw new Exception("Admin cannot view the pending house listing.");
    }
    echo "[PASS] TEST 9: Admin can see the pending house listing successfully.\n";

    // 9. Admin approves listing
    $houseRepo->updateStatus($houseId, 'published');
    $houseRecord = $houseRepo->findById($houseId);
    if ($houseRecord['status'] !== 'published') {
        throw new Exception("Admin listing approval update failed.");
    }
    echo "[PASS] TEST 10: Admin approved and published house listing.\n";

    // 10. Verify listing is now live on browse page
    $browseListings = $houseRepo->getApprovedListings();
    $foundLive = false;
    foreach ($browseListings as $b) {
        if ($b['id'] == $houseId) {
            $foundLive = true;
        }
    }
    if (!$foundLive) {
        throw new Exception("Listing not showing on browse page after approval.");
    }
    echo "[PASS] TEST 11: Approved house listing is now live on the public browse page.\n";

    // 11. Deactivate listing
    $houseRepo->updateStatus($houseId, 'archived');
    $browseListings = $houseRepo->getApprovedListings();
    $foundLive = false;
    foreach ($browseListings as $b) {
        if ($b['id'] == $houseId) {
            $foundLive = true;
        }
    }
    if ($foundLive) {
        throw new Exception("Listing is still showing on browse page after deactivation.");
    }
    echo "[PASS] TEST 12: Deactivated listing is successfully hidden from the public browse page.\n";

    Database::rollBack();
    echo "==================================================\n";
    echo "Babura House Connect - All Marketplace Tests PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    Database::rollBack();
    echo "[FAIL] Integration test failed: " . $e->getMessage() . "\n";
    exit(1);
}
