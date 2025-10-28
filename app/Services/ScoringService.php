<?php

namespace App\Services;

use App\Models\ScoringSetting;

class ScoringService
{
	public function calculateFinalScore(float $bahasa_inggris, float $pu, float $twk, float $numerik): array
	{
		$setting = ScoringSetting::current();
		$w1 = $setting->weight_bahasa_inggris / 100;
		$w2 = $setting->weight_pu / 100;
		$w3 = $setting->weight_twk / 100;
		$w4 = $setting->weight_numerik / 100;

		$final = ($bahasa_inggris + $pu + $twk + $numerik) / 4;
		$passed = $final >= (float) $setting->passing_grade;

		return [
			'score' => round($final, 2),
			'passed' => $passed,
			'passing_grade' => (int) $setting->passing_grade,
			'weights' => [
				'bahasa_inggris' => (int) $setting->weight_bahasa_inggris,
				'pu' => (int) $setting->weight_pu,
				'twk' => (int) $setting->weight_twk,
				'numerik' => (int) $setting->weight_numerik,
			],
		];
	}
}


