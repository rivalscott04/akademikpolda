<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\TryoutBlueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorQuestionUsage extends Command
{
    protected $signature = 'questions:monitor';
    protected $description = 'Monitor question usage and detect anomalies';

    public function handle()
    {
        $this->info('Monitoring question usage...');
        
        $categories = KategoriSoal::active()->get();
        $alerts = [];
        
        foreach ($categories as $kategori) {
            $stats = $this->getCategoryStats($kategori);
            
            // Alert jika usage terlalu tinggi tanpa blueprint
            if ($stats['usage_percentage'] > 90 && $stats['blueprint_count'] == 0) {
                $alerts[] = [
                    'type' => 'high_usage_no_blueprints',
                    'kategori' => $kategori->nama,
                    'message' => "Kategori {$kategori->nama} memiliki {$stats['usage_percentage']}% soal terpakai tanpa blueprint"
                ];
            }
            
            // Alert jika ada bulk update
            if ($stats['bulk_updates'] > 0) {
                $alerts[] = [
                    'type' => 'bulk_updates_detected',
                    'kategori' => $kategori->nama,
                    'message' => "Detected {$stats['bulk_updates']} bulk updates in {$kategori->nama}"
                ];
            }
        }
        
        if (!empty($alerts)) {
            $this->warn('⚠️  Alerts detected:');
            foreach ($alerts as $alert) {
                $this->line("  - {$alert['message']}");
            }
            
            // Log alerts
            Log::warning('Question usage anomalies detected', $alerts);
        } else {
            $this->info('✅ No anomalies detected.');
        }
        
        return 0;
    }
    
    private function getCategoryStats($kategori)
    {
        $totalSoals = $kategori->soals()->count();
        $usedSoals = $kategori->soals()->where('is_used', true)->count();
        $blueprintCount = TryoutBlueprint::where('kategori_id', $kategori->id)->count();
        
        // Check for bulk updates (same timestamp with >10 questions)
        $bulkUpdates = $kategori->soals()
            ->where('is_used', true)
            ->selectRaw('updated_at, COUNT(*) as count')
            ->groupBy('updated_at')
            ->having('count', '>', 10)
            ->count();
        
        return [
            'total_soals' => $totalSoals,
            'used_soals' => $usedSoals,
            'blueprint_count' => $blueprintCount,
            'usage_percentage' => $totalSoals > 0 ? round(($usedSoals / $totalSoals) * 100, 2) : 0,
            'bulk_updates' => $bulkUpdates
        ];
    }
}



