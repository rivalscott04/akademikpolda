<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HasilTes;
use Illuminate\Support\Facades\DB;

class AggressiveCleanDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:aggressive-clean-duplicates {--dry-run : Show what would be deleted without actually deleting} {--user-id= : Clean duplicates for specific user ID only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggressively clean duplicate test results from hasil_tes table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $userId = $this->option('user-id');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No data will be deleted');
        }

        $this->info('🧹 Starting aggressive duplicate test results cleanup...');

        // Build query with optional user filter
        $query = HasilTes::query();
        if ($userId) {
            $query->where('user_id', $userId);
            $this->info("🎯 Filtering for User ID: {$userId}");
        }

        // Get all records ordered by user, test type, and date
        $allRecords = $query->orderBy('user_id')
            ->orderBy('jenis_tes')
            ->orderBy('tanggal_tes')
            ->get();

        $duplicates = [];
        $processed = 0;
        $totalDuplicates = 0;

        // Group by user_id and jenis_tes
        $grouped = $allRecords->groupBy(function ($record) {
            return $record->user_id . '_' . $record->jenis_tes;
        });

        foreach ($grouped as $groupKey => $records) {
            $userTestRecords = $records->sortBy('tanggal_tes');
            $lastRecord = null;
            $duplicateGroup = [];

            foreach ($userTestRecords as $record) {
                if ($lastRecord && $this->isDuplicate($lastRecord, $record)) {
                    if (empty($duplicateGroup)) {
                        $duplicateGroup[] = $lastRecord; // Add the first record of the duplicate group
                    }
                    $duplicateGroup[] = $record;
                } else {
                    // If we have a duplicate group, process it
                    if (count($duplicateGroup) > 1) {
                        $duplicates[] = $duplicateGroup;
                        $totalDuplicates += count($duplicateGroup) - 1; // -1 because we keep one
                    }
                    $duplicateGroup = [];
                }
                $lastRecord = $record;
            }

            // Process the last duplicate group if exists
            if (count($duplicateGroup) > 1) {
                $duplicates[] = $duplicateGroup;
                $totalDuplicates += count($duplicateGroup) - 1;
            }

            $processed++;
        }

        if (empty($duplicates)) {
            $this->info('✅ No duplicate test results found!');
            return 0;
        }

        $this->info("📊 Found " . count($duplicates) . " duplicate groups with {$totalDuplicates} records to delete");

        $deletedCount = 0;
        foreach ($duplicates as $duplicateGroup) {
            $user = $duplicateGroup[0]->user_id;
            $testType = $duplicateGroup[0]->jenis_tes;
            $date = $duplicateGroup[0]->tanggal_tes;
            
            $this->line("📋 User {$user}, Test: {$testType}, Date: {$date} - Found " . count($duplicateGroup) . " duplicates");

            if (!$isDryRun) {
                // Keep the first record (oldest), delete the rest
                for ($i = 1; $i < count($duplicateGroup); $i++) {
                    $record = $duplicateGroup[$i];
                    $record->delete();
                    $deletedCount++;
                    $this->line("   🗑️  Deleted record ID: {$record->id}");
                }
            } else {
                $this->line("   🔄 Would delete " . (count($duplicateGroup) - 1) . " records");
            }
        }

        if ($isDryRun) {
            $this->info("📈 Summary (DRY RUN):");
            $this->info("   - Duplicate groups found: " . count($duplicates));
            $this->info("   - Records that would be deleted: {$totalDuplicates}");
        } else {
            $this->info("✅ Cleanup completed!");
            $this->info("   - Duplicate groups processed: " . count($duplicates));
            $this->info("   - Records deleted: {$deletedCount}");
        }

        return 0;
    }

    /**
     * Check if two records are duplicates
     */
    private function isDuplicate($record1, $record2)
    {
        // Same user and test type
        if ($record1->user_id !== $record2->user_id || $record1->jenis_tes !== $record2->jenis_tes) {
            return false;
        }

        // Same date (within 1 minute)
        $timeDiff = abs(strtotime($record1->tanggal_tes) - strtotime($record2->tanggal_tes));
        if ($timeDiff > 60) { // More than 1 minute apart
            return false;
        }

        // Same score (or very close)
        $score1 = floatval($record1->skor_akhir ?? 0);
        $score2 = floatval($record2->skor_akhir ?? 0);
        if (abs($score1 - $score2) > 0.01) { // More than 0.01 difference
            return false;
        }

        // Same category
        if ($record1->kategori_skor !== $record2->kategori_skor) {
            return false;
        }

        return true;
    }
}
