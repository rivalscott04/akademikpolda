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
			'kecermatan' => 'required|numeric|min:0|max:100',
			'kecerdasan' => 'required|numeric|min:0|max:100',
			'kepribadian' => 'required|numeric|min:0|max:100',
		]);

		$result = $service->calculateFinalScore(
			(float) $request->kecermatan,
			(float) $request->kecerdasan,
			(float) $request->kepribadian
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
				'kecermatan' => $setting->weight_kecermatan,
				'kecerdasan' => $setting->weight_kecerdasan,
				'kepribadian' => $setting->weight_kepribadian,
			],
			'passing_grade' => $setting->passing_grade,
		]);
	}
}


