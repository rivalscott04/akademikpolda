<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HasilTes;
use App\Models\UserTryoutSession;
use App\Models\UserTryoutSoal;
use Illuminate\Support\Facades\DB;

echo "=== CLEARING RIWAYAT TES ===\n";

// Get counts before clearing
$totalHasilTes = HasilTes::count();
$totalSessions = UserTryoutSession::count();
$totalUserAnswers = UserTryoutSoal::count();

echo "Before clearing:\n";
echo "Total HasilTes records: " . $totalHasilTes . "\n";
echo "Total UserTryoutSession records: " . $totalSessions . "\n";
echo "Total UserTryoutSoal records: " . $totalUserAnswers . "\n\n";

echo "Clearing test history data...\n";

// Disable foreign key checks temporarily
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

try {
    // Clear all test results
    echo "Clearing HasilTes table...\n";
    $deletedHasilTes = HasilTes::truncate();
    echo "✓ HasilTes cleared\n";

    // Clear all tryout sessions
    echo "Clearing UserTryoutSession table...\n";
    $deletedSessions = UserTryoutSession::truncate();
    echo "✓ UserTryoutSession cleared\n";

    // Clear all user answers
    echo "Clearing UserTryoutSoal table...\n";
    $deletedAnswers = UserTryoutSoal::truncate();
    echo "✓ UserTryoutSoal cleared\n";

    echo "\n✅ All test history data cleared successfully!\n";

} catch (Exception $e) {
    echo "❌ Error clearing data: " . $e->getMessage() . "\n";
} finally {
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

// Get counts after clearing
$totalHasilTesAfter = HasilTes::count();
$totalSessionsAfter = UserTryoutSession::count();
$totalUserAnswersAfter = UserTryoutSoal::count();

echo "\nAfter clearing:\n";
echo "Total HasilTes records: " . $totalHasilTesAfter . "\n";
echo "Total UserTryoutSession records: " . $totalSessionsAfter . "\n";
echo "Total UserTryoutSoal records: " . $totalUserAnswersAfter . "\n\n";

echo "=== RIWAYAT TES CLEARED COMPLETE ===\n";
echo "All test history has been removed from:\n";
echo "- Profile tab 'Riwayat Tes'\n";
echo "- Admin riwayat-tes page\n";
echo "- User tryout sessions\n";
echo "- User answers and scores\n";
