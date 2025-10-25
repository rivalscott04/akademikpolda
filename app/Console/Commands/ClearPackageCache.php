<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PackageCategoryMapping;

class ClearPackageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all package-related caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing package caches...');
        
        // Clear package mapping caches
        PackageCategoryMapping::clearCache();
        
        // Clear other package-related caches
        cache()->forget('kategori_soal_active_codes');
        cache()->forget('tryout_active_types');
        
        // Clear paket lengkap caches
        $service = app(\App\Services\PaketLengkapService::class);
        $service->clearAllCache();
        
        // Clear user statistics caches
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            cache()->forget("user_statistics_{$user->id}");
        }
        
        $this->info('Package caches cleared successfully!');
        
        return 0;
    }
}