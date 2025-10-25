<?php

namespace App\Http\Controllers;

use App\Models\ScoringSetting;
use Illuminate\Http\Request;

class ScoringSettingController extends Controller
{
	public function index()
	{
		$setting = ScoringSetting::current();
		return view('admin.scoring-settings', compact('setting'));
	}

	public function update(Request $request)
	{
		$request->validate([
			'weight_bahasa_inggris' => 'required|integer|min:0|max:100',
			'weight_pu' => 'required|integer|min:0|max:100',
			'weight_twk' => 'required|integer|min:0|max:100',
			'weight_numerik' => 'required|integer|min:0|max:100',
			'passing_grade' => 'required|integer|min:0|max:100',
		]);

		$total = (int) $request->weight_bahasa_inggris + (int) $request->weight_pu + (int) $request->weight_twk + (int) $request->weight_numerik;
		if ($total !== 100) {
			return back()->withInput()->withErrors(['weights' => 'Jumlah bobot harus tepat 100%. Saat ini: ' . $total . '%']);
		}

		$setting = ScoringSetting::query()->latest('id')->first();
		if (!$setting) {
			$setting = new ScoringSetting();
		}

		$setting->fill($request->only([
			'weight_bahasa_inggris',
			'weight_pu',
			'weight_twk',
			'weight_numerik',
			'passing_grade',
		]));
		$setting->save();

		return redirect()->route('admin.scoring-settings.index')->with('success', 'Pengaturan simulasi nilai berhasil disimpan.');
	}
}


