<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoringSetting extends Model
{
	protected $fillable = [
		'weight_bahasa_inggris',
		'weight_pu',
		'weight_twk',
		'weight_numerik',
		'passing_grade',
	];

	/**
	 * Get the single settings row, creating default in memory if none exists.
	 */
	public static function current(): self
	{
		return static::query()->latest('id')->first() ?? new static([
			'weight_bahasa_inggris' => 25,
			'weight_pu' => 25,
			'weight_twk' => 25,
			'weight_numerik' => 25,
			'passing_grade' => 61,
		]);
	}
}


