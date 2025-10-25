<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTryoutSoal;

class MigrateExistingAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tryout:migrate-answers';

    /**
     * The console description of the command.
     *
     * @var string
     */
    protected $description = 'Migrate existing answers to support shuffled options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of existing answers...');

        // Get all user tryout soals that have answers but no original answers
        $userSoals = UserTryoutSoal::where('sudah_dijawab', true)
            ->whereNull('jawaban_original')
            ->with('soal')
            ->get();

        $this->info("Found {$userSoals->count()} records to migrate.");

        $bar = $this->output->createProgressBar($userSoals->count());
        $bar->start();

        $migrated = 0;
        $skipped = 0;

        foreach ($userSoals as $userSoal) {
            try {
                // For existing data, jawaban_user is actually the original answer
                // So we copy it to jawaban_original
                $userSoal->jawaban_original = $userSoal->jawaban_user;
                $userSoal->save();

                $migrated++;
            } catch (\Exception $e) {
                $this->error("Failed to migrate record ID {$userSoal->id}: " . $e->getMessage());
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Migration completed!");
        $this->info("Migrated: {$migrated} records");

        if ($skipped > 0) {
            $this->warn("Skipped: {$skipped} records due to errors");
        }

        return Command::SUCCESS;
    }
}
