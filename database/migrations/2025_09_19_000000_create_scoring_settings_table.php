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
		Schema::create('scoring_settings', function (Blueprint $table) {
			$table->id();
			// weights are stored as integer percentage (0-100)
			$table->unsignedTinyInteger('weight_bahasa_inggris');
			$table->unsignedTinyInteger('weight_pu');
			$table->unsignedTinyInteger('weight_twk');
			$table->unsignedTinyInteger('weight_numerik');
			$table->unsignedTinyInteger('passing_grade')->default(61);
			$table->timestamps();
		});

		// Ensure at most one row (enforced at app level; add optional unique check sum to 100 later if needed)
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('scoring_settings');
	}
};


