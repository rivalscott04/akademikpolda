<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration fixes the unique constraint on tryout_blueprints table.
     * The correct constraint should be on (tryout_id, kategori_id, level) to allow
     * the same kategori_id to have multiple different levels in one tryout.
     */
    public function up(): void
    {
        // Step 1: Drop all foreign keys first
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'tryout_blueprints' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE tryout_blueprints DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                echo "Dropped foreign key: {$fk->CONSTRAINT_NAME}\n";
            } catch (\Exception $e) {
                echo "Could not drop FK {$fk->CONSTRAINT_NAME}: {$e->getMessage()}\n";
            }
        }
        
        // Step 2: Drop all unique indexes (avoid duplicates)
        $indexes = DB::select("SHOW INDEX FROM tryout_blueprints WHERE Key_name != 'PRIMARY'");
        $droppedIndexes = [];
        
        foreach ($indexes as $index) {
            if ($index->Non_unique == 0 && !in_array($index->Key_name, $droppedIndexes)) { // 0 means UNIQUE constraint
                try {
                    DB::statement("ALTER TABLE tryout_blueprints DROP INDEX `{$index->Key_name}`");
                    echo "Dropped index: {$index->Key_name}\n";
                    $droppedIndexes[] = $index->Key_name;
                } catch (\Exception $e) {
                    echo "Could not drop index {$index->Key_name}: {$e->getMessage()}\n";
                }
            }
        }
        
        // Step 3: Add the correct unique constraint
        // This allows: same tryout_id + same kategori_id + different levels
        Schema::table('tryout_blueprints', function (Blueprint $table) {
            $table->unique(['tryout_id', 'kategori_id', 'level'], 'tryout_blueprints_unique');
        });
        
        // Step 4: Re-add foreign keys if they were dropped
        Schema::table('tryout_blueprints', function (Blueprint $table) {
            // Recreate foreign key to tryouts table
            if (!$this->foreignKeyExists('tryout_blueprints', 'tryout_blueprints_tryout_id_foreign')) {
                $table->foreign('tryout_id')
                    ->references('id')
                    ->on('tryouts')
                    ->onDelete('cascade');
            }
            
            // Recreate foreign key to kategori_soal table
            if (!$this->foreignKeyExists('tryout_blueprints', 'tryout_blueprints_kategori_id_foreign')) {
                $table->foreign('kategori_id')
                    ->references('id')
                    ->on('kategori_soal')
                    ->onDelete('cascade');
            }
        });
    }
    
    /**
     * Check if foreign key exists
     */
    private function foreignKeyExists($table, $name): bool
    {
        $result = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$table, $name]);
        
        return !empty($result);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tryout_blueprints', function (Blueprint $table) {
            try {
                $table->dropUnique('tryout_blueprints_unique');
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
        });
    }
};