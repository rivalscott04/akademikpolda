<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScoringSetting;

class ScoringSettingSeeder extends Seeder
{
	public function run(): void
	{
		if (!ScoringSetting::query()->exists()) {
			ScoringSetting::create([
				'weight_bahasa_inggris' => 25,
				'weight_pu' => 25,
				'weight_twk' => 25,
				'weight_numerik' => 25,
				'passing_grade' => 61,
			]);
		}
	}
}


