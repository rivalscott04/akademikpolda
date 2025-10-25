<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the old unique constraint exists and drop it safely
        try {
            DB::statement('ALTER TABLE hasil_tes DROP INDEX unique_user_test_date');
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }
        
        // Check if column already exists before adding it
        $columns = DB::select("SHOW COLUMNS FROM hasil_tes LIKE 'session_id_extracted'");
        if (empty($columns)) {
            Schema::table('hasil_tes', function (Blueprint $table) {
                // Add a generated column to extract session_id from JSON
                $table->string('session_id_extracted')->nullable()->after('detail_jawaban');
            });
        }
        
        // Create the generated column using raw SQL (this will modify existing column if it exists)
        try {
            DB::statement("ALTER TABLE hasil_tes MODIFY COLUMN session_id_extracted VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(detail_jawaban, '$.session_id'))) STORED");
        } catch (\Exception $e) {
            // Column might already be a generated column, continue
        }
        
        // Clean up duplicates before adding unique constraint
        $this->cleanDuplicates();
        
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Add unique constraint using the generated column
            $table->unique(['user_id', 'jenis_tes', 'session_id_extracted'], 'unique_user_test_session');
        });
    }
    
    private function cleanDuplicates()
    {
        // Find and remove duplicates based on user_id, jenis_tes, and session_id_extracted
        $duplicates = DB::table('hasil_tes')
            ->select('user_id', 'jenis_tes', 'session_id_extracted', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id ORDER BY id) as ids'))
            ->whereNotNull('session_id_extracted')
            ->groupBy('user_id', 'jenis_tes', 'session_id_extracted')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $keepId = $ids[0]; // Keep the first (oldest) record
            $deleteIds = array_slice($ids, 1); // Delete the rest
            
            // Delete duplicate records
            DB::table('hasil_tes')->whereIn('id', $deleteIds)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new unique constraint safely
        try {
            DB::statement('ALTER TABLE hasil_tes DROP INDEX unique_user_test_session');
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }
        
        // Drop the generated column using raw SQL
        try {
            DB::statement('ALTER TABLE hasil_tes DROP COLUMN session_id_extracted');
        } catch (\Exception $e) {
            // Column doesn't exist, continue
        }
        
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Restore the old unique constraint
            $table->unique(['user_id', 'jenis_tes', 'tanggal_tes'], 'unique_user_test_date');
        });
    }
};