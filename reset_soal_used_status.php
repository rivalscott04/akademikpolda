<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Soal;
use App\Models\UserTryoutSession;
use App\Models\HasilTes;
use App\Models\UserTryoutSoal;
use Illuminate\Support\Facades\DB;

echo "=== RESETTING SOAL USED STATUS ===\n";
echo "Before reset:\n";
echo "Total soal: " . Soal::count() . "\n";
echo "Soal yang sudah dipakai (is_used = true): " . Soal::where('is_used', true)->count() . "\n";
echo "Soal yang belum dipakai (is_used = false): " . Soal::where('is_used', false)->count() . "\n\n";

echo "Clearing test history data...\n";

// Clear all test history data in correct order (child tables first)
// Disable foreign key checks temporarily
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

UserTryoutSoal::truncate();
UserTryoutSession::truncate();
HasilTes::truncate();

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Resetting soal is_used status to false...\n";

// Reset all soal is_used status to false
$updated = Soal::where('is_used', true)->update(['is_used' => false]);

echo "Updated {$updated} soal records.\n\n";

echo "After reset:\n";
echo "Total soal: " . Soal::count() . "\n";
echo "Soal yang sudah dipakai (is_used = true): " . Soal::where('is_used', true)->count() . "\n";
echo "Soal yang belum dipakai (is_used = false): " . Soal::where('is_used', false)->count() . "\n\n";

echo "=== SOAL USED STATUS RESET COMPLETE ===\n";
