<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserTryoutSession;
use App\Models\HasilTes;
use App\Models\UserTryoutSoal;

echo "=== CLEARING TEST HISTORY DATA ===\n";
echo "Before clearing:\n";
echo "UserTryoutSession count: " . UserTryoutSession::count() . "\n";
echo "HasilTes count: " . HasilTes::count() . "\n";
echo "UserTryoutSoal count: " . UserTryoutSoal::count() . "\n\n";

echo "Clearing data...\n";

// Clear all test history data in correct order (child tables first)
// Disable foreign key checks temporarily
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

UserTryoutSoal::truncate();
UserTryoutSession::truncate();
HasilTes::truncate();

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "After clearing:\n";
echo "UserTryoutSession count: " . UserTryoutSession::count() . "\n";
echo "HasilTes count: " . HasilTes::count() . "\n";
echo "UserTryoutSoal count: " . UserTryoutSoal::count() . "\n\n";

echo "=== ALL TEST HISTORY DATA CLEARED ===\n";
