<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HasilTes;

class FixIncorrectScoreCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:fix-score-categories {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix incorrect score categories in hasil_tes table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No data will be updated');
        }

        $this->info('🔧 Starting score category fix...');

        // Get all test results that have skor_akhir
        $testResults = HasilTes::whereNotNull('skor_akhir')
            ->whereNotNull('jenis_tes')
            ->get();

        $fixedCount = 0;
        $totalChecked = $testResults->count();

        foreach ($testResults as $result) {
            $currentCategory = $result->kategori_skor;
            $correctCategory = $this->getCorrectCategory($result->jenis_tes, $result->skor_akhir);

            if ($currentCategory !== $correctCategory) {
                $this->line("📊 ID: {$result->id}, User: {$result->user_id}, Test: {$result->jenis_tes}");
                $this->line("   Score: {$result->skor_akhir}");
                $this->line("   Current: '{$currentCategory}' → Correct: '{$correctCategory}'");

                if (!$isDryRun) {
                    $result->update(['kategori_skor' => $correctCategory]);
                    $this->line("   ✅ Updated!");
                } else {
                    $this->line("   🔄 Would be updated");
                }

                $fixedCount++;
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
     * Get the correct category based on test type and score
     */
    private function getCorrectCategory($jenisTes, $score)
    {
        switch ($jenisTes) {
            case 'kecerdasan':
                return $this->getIntelligenceCategory($score);
            case 'kepribadian':
                return $this->getTkpCategory($score);
            case 'kecermatan':
                return $this->getKecermatanCategory($score);
            default:
                return 'Unknown';
        }
    }

    /**
     * Get category for intelligence test (kecerdasan)
     */
    private function getIntelligenceCategory($score)
    {
        if ($score >= 90) return 'Sangat Tinggi';
        if ($score >= 81) return 'Tinggi';
        if ($score >= 71) return 'Baik';
        if ($score >= 61) return 'Cukup';
        if ($score >= 51) return 'Rendah';
        return 'Sangat Rendah';
    }

    /**
     * Get category for TKP test (kepribadian)
     */
    private function getTkpCategory($score)
    {
        if ($score >= 91) return 'Sangat Tinggi';
        if ($score >= 76) return 'Tinggi';
        if ($score >= 61) return 'Cukup Tinggi';
        if ($score >= 41) return 'Sedang';
        return 'Rendah';
    }

    /**
     * Get category for kecermatan test
     */
    private function getKecermatanCategory($score)
    {
        // For kecermatan, score is usually the number of correct answers out of 9
        $percentage = ($score / 9) * 100;
        
        if ($percentage >= 90) return 'Sangat Tinggi';
        if ($percentage >= 81) return 'Tinggi';
        if ($percentage >= 71) return 'Baik';
        if ($percentage >= 61) return 'Cukup';
        if ($percentage >= 51) return 'Rendah';
        return 'Sangat Rendah';
    }
}