<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\TryoutBlueprint;
use Illuminate\Support\Facades\DB;

class CheckQuestionUsageAnomalies extends Command
{
    protected $signature = 'questions:check-anomalies';
    protected $description = 'Check for anomalies in question usage flags';

    public function handle()
    {
        $this->info('Checking for question usage anomalies...');
        
        $anomalies = [];
        
        // Check for categories with high usage but no blueprints
        $categories = KategoriSoal::active()->get();
        
        foreach ($categories as $kategori) {
            $totalSoals = $kategori->soals()->count();
            $usedSoals = $kategori->soals()->where('is_used', true)->count();
            $blueprintCount = TryoutBlueprint::where('kategori_id', $kategori->id)->count();
            
            // If more than 80% questions are used but no blueprints exist
            if ($usedSoals > 0 && $blueprintCount == 0 && ($usedSoals / $totalSoals) > 0.8) {
                $anomalies[] = [
                    'type' => 'high_usage_no_blueprints',
                    'kategori' => $kategori->nama,
                    'kode' => $kategori->kode,
                    'total_soals' => $totalSoals,
                    'used_soals' => $usedSoals,
                    'blueprint_count' => $blueprintCount,
                    'usage_percentage' => round(($usedSoals / $totalSoals) * 100, 2)
                ];
            }
            
            // Check for questions marked as used on the same timestamp
            $sameTimestampCount = $kategori->soals()
                ->where('is_used', true)
                ->selectRaw('updated_at, COUNT(*) as count')
                ->groupBy('updated_at')
                ->having('count', '>', 10)
                ->count();
                
            if ($sameTimestampCount > 0) {
                $anomalies[] = [
                    'type' => 'bulk_update_same_timestamp',
                    'kategori' => $kategori->nama,
                    'kode' => $kategori->kode,
                    'same_timestamp_groups' => $sameTimestampCount
                ];
            }
        }
        
        if (empty($anomalies)) {
            $this->info('✅ No anomalies detected.');
            return;
        }
        
        $this->warn('⚠️  Anomalies detected:');
        foreach ($anomalies as $anomaly) {
            $this->line("Category: {$anomaly['kategori']} ({$anomaly['kode']})");
            
            if ($anomaly['type'] === 'high_usage_no_blueprints') {
                $this->line("  - High usage ({$anomaly['usage_percentage']}%) but no blueprints");
                $this->line("  - Used: {$anomaly['used_soals']}/{$anomaly['total_soals']}");
            } elseif ($anomaly['type'] === 'bulk_update_same_timestamp') {
                $this->line("  - Bulk updates detected ({$anomaly['same_timestamp_groups']} groups)");
            }
        }
        
        // Ask if user wants to fix anomalies
        if ($this->confirm('Do you want to fix these anomalies by resetting is_used flags?')) {
            $this->fixAnomalies($anomalies);
        }
    }
    
    private function fixAnomalies($anomalies)
    {
        $this->info('Fixing anomalies...');
        
        foreach ($anomalies as $anomaly) {
            if ($anomaly['type'] === 'high_usage_no_blueprints') {
                $kategori = KategoriSoal::where('kode', $anomaly['kode'])->first();
                if ($kategori) {
                    $updated = $kategori->soals()
                        ->where('is_used', true)
                        ->update(['is_used' => false]);
                    
                    $this->info("✅ Reset {$updated} questions for {$kategori->nama}");
                }
            }
        }
        
        $this->info('Anomalies fixed successfully!');
    }
}
