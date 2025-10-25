<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HasilTes;
use Illuminate\Support\Facades\DB;

class CleanDuplicateTestHistory extends Command
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
    protected $description = 'Clean duplicate test history records to prevent multiple entries for the same test session';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Starting duplicate test history cleanup...');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No records will be deleted');
        }

        // Find duplicates based on user_id, jenis_tes, and detail_jawaban
        $duplicates = DB::table('hasil_tes')
            ->select('user_id', 'jenis_tes', 'detail_jawaban', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('user_id', 'jenis_tes', 'detail_jawaban')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate records found!');
            return 0;
        }

        $this->info("Found {$duplicates->count()} groups of duplicate records");

        $totalDeleted = 0;
        $totalKept = 0;

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $keepId = $ids[0]; // Keep the first (oldest) record
            $deleteIds = array_slice($ids, 1); // Delete the rest

            $this->line("User {$duplicate->user_id} - {$duplicate->jenis_tes}: Keeping ID {$keepId}, deleting IDs: " . implode(', ', $deleteIds));

            if (!$isDryRun) {
                $deleted = HasilTes::whereIn('id', $deleteIds)->delete();
                $totalDeleted += $deleted;
            } else {
                $totalDeleted += count($deleteIds);
            }
            
            $totalKept++;
        }

        if ($isDryRun) {
            $this->info("DRY RUN: Would delete {$totalDeleted} duplicate records, keeping {$totalKept} original records");
        } else {
            $this->info("Successfully deleted {$totalDeleted} duplicate records, kept {$totalKept} original records");
        }

        return 0;
    }
}