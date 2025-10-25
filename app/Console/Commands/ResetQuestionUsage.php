<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\TryoutBlueprint;
use Illuminate\Support\Facades\DB;

class ResetQuestionUsage extends Command
{
    protected $signature = 'questions:reset-usage {kategori?} {--force : Force reset without confirmation}';
    protected $description = 'Reset is_used flags for questions';

    public function handle()
    {
        $kategoriCode = $this->argument('kategori');
        $force = $this->option('force');
        
        if ($kategoriCode) {
            // Reset specific category
            $kategori = KategoriSoal::where('kode', strtoupper($kategoriCode))->first();
            if (!$kategori) {
                $this->error("Kategori dengan kode '{$kategoriCode}' tidak ditemukan.");
                return 1;
            }
            
            $this->resetCategory($kategori, $force);
        } else {
            // Reset all categories
            $categories = KategoriSoal::active()->get();
            
            if (!$force && !$this->confirm('Reset is_used flags for ALL categories? This will make all questions available again.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
            
            foreach ($categories as $kategori) {
                $this->resetCategory($kategori, true);
            }
        }
        
        return 0;
    }
    
    private function resetCategory($kategori, $force = false)
    {
        $this->info("Processing kategori: {$kategori->nama} ({$kategori->kode})");
        
        // Get stats before reset
        $totalSoals = $kategori->soals()->count();
        $usedSoals = $kategori->soals()->where('is_used', true)->count();
        $blueprintCount = TryoutBlueprint::where('kategori_id', $kategori->id)->count();
        
        $this->line("  Total soal: {$totalSoals}");
        $this->line("  Used soal: {$usedSoals}");
        $this->line("  Blueprint count: {$blueprintCount}");
        
        if ($usedSoals == 0) {
            $this->line("  ✅ No used questions to reset.");
            return;
        }
        
        if (!$force && !$this->confirm("  Reset {$usedSoals} used questions to available?")) {
            $this->line("  ⏭️  Skipped.");
            return;
        }
        
        // Reset questions
        $resetCount = $kategori->soals()
            ->where('is_used', true)
            ->update(['is_used' => false]);
        
        $this->line("  ✅ Reset {$resetCount} questions to available.");
        
        // Log the action
        \Log::info("Manual reset: {$resetCount} questions for kategori {$kategori->nama} ({$kategori->kode})");
    }
}



