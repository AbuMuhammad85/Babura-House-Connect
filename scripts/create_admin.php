<?php

if (PHP_SAPI !== 'cli') {
    die("This script can only be run from the command line.\n");
}

require_once __DIR__ . '/../bootstrap/app.php';

use App\Repositories\UserRepository;
use App\Core\Database;

echo "==================================================\n";
echo "Babura House Connect - Create Admin User\n";
echo "==================================================\n";

$repo = new UserRepository();

echo "Enter Admin Full Name: ";
$name = trim(fgets(STDIN));

echo "Enter Admin Email: ";
$email = trim(fgets(STDIN));

echo "Enter Admin Phone: ";
$phone = trim(fgets(STDIN));

echo "Enter Admin Password: ";
$password = trim(fgets(STDIN));

if (empty($name) || empty($email) || empty($phone) || empty($password)) {
    die("[FAIL] All fields are required to create an admin account.\n");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("[FAIL] Invalid email address format.\n");
}

if ($repo->findByEmail($email)) {
    die("[FAIL] A user with this email address already exists.\n");
}

if ($repo->findByPhone($phone)) {
    die("[FAIL] A user with this phone number already exists.\n");
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

Database::beginTransaction();
try {
    $userId = $repo->create([
        'role' => 'admin',
        'full_name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password_hash' => $passwordHash,
        'status' => 'active'
    ]);

    Database::commit();

    Database::query(
        "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
         VALUES (:user_id, :action, :description, :ip_address, :user_agent)",
        [
            'user_id' => $userId,
            'action' => 'ADMIN_CREATED',
            'description' => "Admin user created via CLI: " . $email,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'CLI-Script'
        ]
    );

    echo "[PASS] Admin user successfully created!\n";
    echo "       ID: {$userId}\n";
    echo "       Email: {$email}\n";

} catch (Exception $e) {
    Database::rollBack();
    echo "[FAIL] Failed to create admin: " . $e->getMessage() . "\n";
}
