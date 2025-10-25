<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HasilTes;
use Carbon\Carbon;

class CleanDuplicateTestResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:clean-duplicates {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate test results from hasil_tes table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No data will be deleted');
        }

        $this->info('🧹 Starting duplicate test results cleanup...');

        // Find duplicates based on user_id, jenis_tes, and same minute timestamp
        $duplicates = HasilTes::select('user_id', 'jenis_tes')
            ->selectRaw('DATE_FORMAT(tanggal_tes, "%Y-%m-%d %H:%i") as minute_timestamp')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('user_id', 'jenis_tes', 'minute_timestamp')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate test results found!');
            return 0;
        }

        $totalDuplicates = 0;
        $totalToDelete = 0;

        foreach ($duplicates as $duplicate) {
            $totalDuplicates += $duplicate->count;
            $totalToDelete += ($duplicate->count - 1); // Keep one, delete the rest

            $this->line("📊 User ID: {$duplicate->user_id}, Test: {$duplicate->jenis_tes}, Time: {$duplicate->minute_timestamp} - Found {$duplicate->count} duplicates");

            if (!$isDryRun) {
                // Keep the first record, delete the rest
                $recordsToDelete = HasilTes::where('user_id', $duplicate->user_id)
                    ->where('jenis_tes', $duplicate->jenis_tes)
                    ->whereRaw('DATE_FORMAT(tanggal_tes, "%Y-%m-%d %H:%i") = ?', [$duplicate->minute_timestamp])
                    ->orderBy('created_at', 'desc') // Keep the oldest, delete newer ones
                    ->skip(1) // Skip the first (oldest) record
                    ->limit(999) // Add limit to fix SQL syntax error
                    ->get();

                foreach ($recordsToDelete as $record) {
                    $record->delete();
                    $this->line("   🗑️  Deleted record ID: {$record->id}");
                }
            }
        }

        if ($isDryRun) {
            $this->info("📈 Summary (DRY RUN):");
            $this->info("   - Total duplicate groups found: {$duplicates->count()}");
            $this->info("   - Total duplicate records: {$totalDuplicates}");
            $this->info("   - Records that would be deleted: {$totalToDelete}");
            $this->info("   - Records that would be kept: " . ($totalDuplicates - $totalToDelete));
        } else {
            $this->info("✅ Cleanup completed!");
            $this->info("   - Duplicate groups processed: {$duplicates->count()}");
            $this->info("   - Records deleted: {$totalToDelete}");
            $this->info("   - Records kept: " . ($totalDuplicates - $totalToDelete));
        }

        return 0;
    }
}