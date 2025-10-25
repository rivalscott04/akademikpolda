<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScoringService;
use App\Models\ScoringSetting;

class SimulasiNilaiController extends Controller
{
	public function index()
	{
		$setting = ScoringSetting::current();
		// pull: get once then remove, so refresh resets to default 0
		$result = session()->pull('result');
		return view('simulasi.nilai', compact('setting', 'result'));
	}

	public function calculate(Request $request, ScoringService $service)
	{
		$request->validate([
			'bahasa_inggris' => 'required|numeric|min:0|max:100',
			'pengetahuan_umum' => 'required|numeric|min:0|max:100',
			'twk' => 'required|numeric|min:0|max:100',
			'numerik' => 'required|numeric|min:0|max:100',
		]);

		$result = $service->calculateFinalScore(
			(float) $request->bahasa_inggris,
			(float) $request->pengetahuan_umum,
			(float) $request->twk,
			(float) $request->numerik
		);

		return redirect()->route('simulasi.nilai')->with('result', $result);
	}

	public function reset()
	{
		session()->forget('result');
		return redirect()->route('simulasi.nilai');
	}

	/**
	 * Get scoring settings for AJAX requests
	 */
	public function getSettings()
	{
		$setting = ScoringSetting::current();
		return response()->json([
			'weights' => [
				'bahasa_inggris' => $setting->weight_bahasa_inggris,
				'pengetahuan_umum' => $setting->weight_pu,
				'twk' => $setting->weight_twk,
				'numerik' => $setting->weight_numerik,
			],
			'passing_grade' => $setting->passing_grade,
		]);
	}
}


