<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HasilTes;

class FixPersonalityScoreCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:personality-score-categories {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix incorrect score categories for personality tests (kepribadian)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🔧 Fixing personality test score categories...');

        // Get all personality test results
        $personalityResults = HasilTes::where('jenis_tes', 'kepribadian')
            ->whereNotNull('skor_akhir')
            ->get();

        $totalChecked = $personalityResults->count();
        $fixedCount = 0;

        $this->info("📊 Found {$totalChecked} personality test results to check");

        foreach ($personalityResults as $result) {
            $currentCategory = $result->kategori_skor;
            $correctCategory = $this->getCorrectPersonalityCategory($result->skor_akhir);
            
            if ($currentCategory !== $correctCategory) {
                $this->line("❌ ID {$result->id}: Score {$result->skor_akhir} - Current: '{$currentCategory}' → Should be: '{$correctCategory}'");
                
                if (!$isDryRun) {
                    $result->update(['kategori_skor' => $correctCategory]);
                }
                
                $fixedCount++;
            } else {
                $this->line("✅ ID {$result->id}: Score {$result->skor_akhir} - Category '{$currentCategory}' is correct");
            }
        }

        if ($isDryRun) {
            $this->info("📈 Summary (DRY RUN):");
            $this->info("   - Total records checked: {$totalChecked}");
            $this->info("   - Records that need fixing: {$fixedCount}");
        } else {
            $this->info("✅ Fix completed!");
            $this->info("   - Total records checked: {$totalChecked}");
            $this->info("   - Records fixed: {$fixedCount}");
        }

        return 0;
    }

    /**
     * Get the correct category for personality test based on score
     */
    private function getCorrectPersonalityCategory($score)
    {
        if ($score >= 91) return 'Sangat Tinggi';
        if ($score >= 76) return 'Tinggi';
        if ($score >= 61) return 'Cukup Tinggi';
        if ($score >= 41) return 'Sedang';
        return 'Rendah';
    }
}