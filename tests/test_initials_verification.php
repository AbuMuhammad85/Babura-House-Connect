<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;
use App\Helpers\Auth;
use App\Repositories\UserRepository;

echo "==================================================\n";
echo "Babura House Connect - User Name & Initials Verification\n";
echo "==================================================\n";

Database::beginTransaction();
try {
    $repo = new UserRepository();

    // 1. Create and authenticate User A
    $userAId = $repo->create([
        'role' => 'tenant',
        'full_name' => 'Mahmud Rabiu Zakariyya',
        'email' => 'mahmud@example.com',
        'phone' => '+234 901 222 3333',
        'password_hash' => password_hash('password', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    
    $userA = $repo->findById($userAId);
    Auth::login($userA);
    
    $nameA = Auth::user('full_name');
    $initialsA = Auth::initials();
    
    echo "Authenticated User A:\n";
    echo "  Expected Name: Mahmud Rabiu Zakariyya | Actual: {$nameA}\n";
    echo "  Expected Initials: MRZ               | Actual: {$initialsA}\n";
    
    if ($nameA !== 'Mahmud Rabiu Zakariyya') {
        throw new Exception("User A name verification failed.");
    }
    if ($initialsA !== 'MRZ') {
        throw new Exception("User A initials verification failed.");
    }
    echo "[PASS] User A verification successful.\n\n";

    // 2. Create and authenticate User B
    $userBId = $repo->create([
        'role' => 'landlord',
        'full_name' => 'Aisha Bello',
        'email' => 'aisha@example.com',
        'phone' => '+234 901 222 4444',
        'password_hash' => password_hash('password', PASSWORD_BCRYPT),
        'status' => 'active'
    ]);
    
    $userB = $repo->findById($userBId);
    Auth::login($userB);
    
    $nameB = Auth::user('full_name');
    $initialsB = Auth::initials();
    
    echo "Authenticated User B:\n";
    echo "  Expected Name: Aisha Bello | Actual: {$nameB}\n";
    echo "  Expected Initials: AB      | Actual: {$initialsB}\n";
    
    if ($nameB !== 'Aisha Bello') {
        throw new Exception("User B name verification failed.");
    }
    if ($initialsB !== 'AB') {
        throw new Exception("User B initials verification failed.");
    }
    echo "[PASS] User B verification successful.\n\n";

    Database::rollBack();
    echo "==================================================\n";
    echo "User Name & Initials Verification: ALL PASSED!\n";
    echo "==================================================\n";

} catch (Exception $e) {
    Database::rollBack();
    echo "[FAIL] Verification failed: " . $e->getMessage() . "\n";
    exit(1);
}
