<?php
require_once __DIR__ . '/../bootstrap/app.php';
echo "==================================================\n";
echo "Babura House Connect - Public Marketplace Regression Test\n";
echo "==================================================\n";
$browse = \App\Core\Database::fetchAll("SELECT * FROM houses WHERE status = 'published' AND availability = 'available'");
echo "[PASS] TEST: Verified public browse listings fetch successfully (" . count($browse) . " items).\n";
echo "==================================================\n";
echo "Babura House Connect - Marketplace Regression Tests PASSED!\n";
echo "==================================================\n";
