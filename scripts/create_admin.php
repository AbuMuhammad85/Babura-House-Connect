<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;
use App\Repositories\UserRepository;

echo "==================================================\n";
echo "Babura House Connect - Create Admin User Account\n";
echo "==================================================\n";

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line (CLI) interface.\n";
    exit(1);
}

// Prompt Helper
function prompt(string $message): string {
    echo $message . ": ";
    $input = trim(fgets(STDIN));
    if (empty($input)) {
        echo "Error: Field cannot be empty.\n";
        return prompt($message);
    }
    return $input;
}

$name = prompt("Enter Admin Full Name");
$email = prompt("Enter Admin Email");
$phone = prompt("Enter Admin Phone Number");
$password = prompt("Enter Admin Password");

$userRepo = new UserRepository();

// Check if email or phone is already registered
if ($userRepo->findByEmail($email)) {
    echo "Error: An account with this email is already registered.\n";
    exit(1);
}

if ($userRepo->findByPhone($phone)) {
    echo "Error: An account with this phone number is already registered.\n";
    exit(1);
}

try {
    $adminId = $userRepo->create([
        'role' => 'admin',
        'full_name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        'status' => 'active'
    ]);

    echo "\n[SUCCESS] Administrative account created successfully!\n";
    echo "  User ID: {$adminId}\n";
    echo "  Full Name: {$name}\n";
    echo "  Email: {$email}\n";
    echo "  Phone: {$phone}\n";
    echo "==================================================\n";

} catch (\Exception $e) {
    echo "Error: Failed to create administrative account: " . $e->getMessage() . "\n";
}
