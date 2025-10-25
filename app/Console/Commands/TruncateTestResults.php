<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateTestResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:truncate-results {--confirm : Confirm that you want to delete ALL test results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all test results from hasil_tes table (DANGEROUS - deletes all data)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('confirm')) {
            $this->error('❌ This command will delete ALL test results!');
            $this->error('Use --confirm flag to proceed');
            return 1;
        }

        $this->warn('⚠️  WARNING: This will delete ALL test results!');
        
        if (!$this->confirm('Are you absolutely sure you want to delete ALL test results?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('🗑️  Truncating hasil_tes table...');
        
        try {
            DB::table('hasil_tes')->truncate();
            $this->info('✅ All test results have been deleted!');
        } catch (\Exception $e) {
            $this->error('❌ Error truncating table: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
