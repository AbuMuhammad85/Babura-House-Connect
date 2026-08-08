<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;
use App\Core\Config;

echo "==================================================\n";
echo "Babura House Connect - Database Connection Test\n";
echo "==================================================\n";

try {
    $dbName = Config::get('database.database');
    $dbHost = Config::get('database.host');
    
    if (!$dbName || !$dbHost) {
        throw new Exception("Environment variables fail: Database config keys not found.");
    }
    echo "[PASS] Environment configuration loaded successfully.\n";
    echo "       Host: {$dbHost}\n";
    echo "       Database: {$dbName}\n";

    $pdo = Database::getConnection();
    echo "[PASS] Database connection established successfully via PDO.\n";

    $tables = [
        'users', 'tenant_profiles', 'landlord_profiles', 'landlord_verifications',
        'areas', 'houses', 'house_images', 'house_videos', 'favorites', 'reviews',
        'notifications', 'subscription_plans', 'subscriptions', 'payments', 'reports', 'activity_logs'
    ];

    $missing = [];
    foreach ($tables as $table) {
        try {
            $pdo->query("SELECT 1 FROM `{$table}` LIMIT 1");
            echo "       Table [{$table}]: EXISTS\n";
        } catch (PDOException $e) {
            echo "       Table [{$table}]: MISSING\n";
            $missing[] = $table;
        }
    }

    if (!empty($missing)) {
        echo "[WARN] Schema incomplete. The following tables are missing: " . implode(', ', $missing) . "\n";
        echo "       Please run 'database/schema.sql' to initialize the database tables.\n";
    } else {
        echo "[PASS] All 16 database tables are verified in schema.\n";
        
        $areaCount = $pdo->query("SELECT COUNT(*) FROM `areas`")->fetchColumn();
        echo "[PASS] Seed verification: {$areaCount} areas found in database.\n";
        
        $areas = $pdo->query("SELECT name FROM `areas`")->fetchAll(PDO::FETCH_COLUMN);
        echo "       Areas: " . implode(', ', $areas) . "\n";
    }

} catch (Exception $e) {
    echo "[FAIL] Connection failed or error occurred: " . $e->getMessage() . "\n";
    exit(1);
}

echo "==================================================\n";
echo "Test Finished.\n";
echo "==================================================\n";
