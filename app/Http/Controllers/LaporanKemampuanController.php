<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HasilTes;
use App\Models\Tryout;
use App\Models\KategoriSoal;
use App\Models\UserTryoutSession;
use App\Models\PackageCategoryMapping;
use Illuminate\Support\Facades\DB;

class LaporanKemampuanController extends Controller
{
    /**
     * Halaman utama laporan kemampuan
     */
    public function index()
    {
        return view('laporan-kemampuan.index');
    }

    /**
     * Halaman laporan paket lengkap
     */
    public function paketLengkap()
    {
        $users = User::whereHas('hasilTes')
            ->with(['hasilTes' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();

        return view('laporan-kemampuan.paket-lengkap', compact('users'));
    }

    /**
     * Halaman laporan per paket
     */
    public function perPaket()
    {
        $packages = PackageCategoryMapping::with('kategori')
            ->select('package_type')
            ->distinct()
            ->get();

        return view('laporan-kemampuan.per-paket', compact('packages'));
    }

    /**
     * Halaman detail laporan per paket
     */
    public function perPaketDetail($package)
    {
        $packageName = $package;
        return view('laporan-kemampuan.per-paket-detail', compact('packageName'));
    }

    /**
     * Get kategori soal berdasarkan paket
     */
    public function getKategoriByPaket(Request $request)
    {
        $packageType = $request->package_type;
        
        $kategori = PackageCategoryMapping::where('package_type', $packageType)
            ->with('kategori')
            ->get()
            ->pluck('kategori');

        return response()->json($kategori);
    }

    /**
     * Get siswa berdasarkan paket dan kategori
     */
    public function getSiswaByPaket(Request $request)
    {
        try {
            $packageType = $request->package_type;
            $kategoriId = $request->kategori_id;

            // Validasi input
            if (!$packageType) {
                return response()->json(['error' => 'Package type is required'], 400);
            }

            // Get kategori soal yang termasuk dalam paket
            $kategoriIds = PackageCategoryMapping::where('package_type', $packageType)
                ->pluck('kategori_id');

            if ($kategoriIds->isEmpty()) {
                return response()->json(['error' => 'No categories found for this package'], 404);
            }

            // Get siswa yang pernah tes dengan jenis tes yang sesuai paket AKADEMIK
            $users = User::whereHas('hasilTes', function($query) use ($packageType) {
                // Filter berdasarkan jenis tes yang sesuai dengan paket AKADEMIK
                if ($packageType === 'bahasa_inggris') {
                    $query->where('jenis_tes', 'bahasa_inggris');
                } elseif ($packageType === 'pu') {
                    $query->where('jenis_tes', 'pu');
                } elseif ($packageType === 'twk') {
                    $query->where('jenis_tes', 'twk');
                } elseif ($packageType === 'numerik') {
                    $query->where('jenis_tes', 'numerik');
                } elseif ($packageType === 'lengkap') {
                    $query->whereIn('jenis_tes', ['bahasa_inggris', 'pu', 'twk', 'numerik']);
                }
            })->select('id', 'name', 'email')->get();

            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Error in getSiswaByPaket: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Generate laporan paket lengkap untuk siswa
     */
    public function generateLaporanPaketLengkap(Request $request)
    {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);

        // Get semua hasil tes siswa
        $hasilTes = HasilTes::where('user_id', $userId)
            ->with(['kategoriSoal', 'tryout'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($hasilTes->isEmpty()) {
            return redirect()->back()->with('error', 'Siswa belum memiliki data tes.');
        }

        // Analisis data (wrap as collection for easy querying in views)
        $analisis = collect($this->analisisPaketLengkap($hasilTes));

        return view('laporan-kemampuan.detail-paket-lengkap', compact('user', 'analisis'));
    }

    /**
     * Generate laporan per paket untuk siswa
     */
    public function generateLaporanPerPaket(Request $request)
    {
        $userId = $request->user_id;
        $packageType = $request->package_type;
        $kategoriId = $request->kategori_id;

        $user = User::findOrFail($userId);

        // Get kategori soal dalam paket
        $kategoriIds = PackageCategoryMapping::where('package_type', $packageType)
            ->pluck('kategori_id');

        // Get hasil tes untuk jenis tes yang sesuai paket AKADEMIK
        $hasilTes = HasilTes::where('user_id', $userId)
            ->where(function($query) use ($packageType) {
                if ($packageType === 'bahasa_inggris') {
                    $query->where('jenis_tes', 'bahasa_inggris');
                } elseif ($packageType === 'pu') {
                    $query->where('jenis_tes', 'pu');
                } elseif ($packageType === 'twk') {
                    $query->where('jenis_tes', 'twk');
                } elseif ($packageType === 'numerik') {
                    $query->where('jenis_tes', 'numerik');
                } elseif ($packageType === 'lengkap') {
                    $query->whereIn('jenis_tes', ['bahasa_inggris', 'pu', 'twk', 'numerik']);
                }
            })
            ->orderBy('created_at', 'asc')
            ->get();

        if ($hasilTes->isEmpty()) {
            return redirect()->back()->with('error', 'Siswa belum memiliki data tes untuk paket ini.');
        }

        // Analisis data (wrap as collection for easy querying in views)
        $analisis = collect($this->analisisPerPaket($hasilTes, $packageType));

        return view('laporan-kemampuan.detail-per-paket', compact('user', 'analisis', 'packageType'));
    }

    /**
     * Analisis data untuk paket lengkap
     */
    private function analisisPaketLengkap($hasilTes)
    {
        $groupedByJenisTes = $hasilTes->groupBy('jenis_tes');
        
        $analisis = [];
        
        foreach ($groupedByJenisTes as $jenisTes => $tes) {
            $tesPertama = $tes->first();
            $tesTerakhir = $tes->last();
            
            $analisis[] = [
                'jenis_tes' => $jenisTes,
                'tes_pertama' => $tesPertama,
                'tes_terakhir' => $tesTerakhir,
                'selisih_skor' => $tesTerakhir->skor_akhir - $tesPertama->skor_akhir,
                'persentase_naik' => $tesPertama->skor_akhir > 0 ? (($tesTerakhir->skor_akhir - $tesPertama->skor_akhir) / $tesPertama->skor_akhir) * 100 : 0,
                'total_tes' => $tes->count(),
                'skor_tertinggi' => $tes->max('skor_akhir'),
                'skor_terendah' => $tes->min('skor_akhir'),
                'rata_rata' => $tes->avg('skor_akhir')
            ];
        }

        // Sort berdasarkan selisih skor (terbesar dulu)
        usort($analisis, function($a, $b) {
            return $b['selisih_skor'] <=> $a['selisih_skor'];
        });

        return $analisis;
    }

    /**
     * Analisis data untuk per paket
     */
    private function analisisPerPaket($hasilTes, $packageType)
    {
        $groupedByJenisTes = $hasilTes->groupBy('jenis_tes');
        
        $analisis = [];
        
        foreach ($groupedByJenisTes as $jenisTes => $tes) {
            $tesPertama = $tes->first();
            $tesTerakhir = $tes->last();
            
            $analisis[] = [
                'jenis_tes' => $jenisTes,
                'tes_pertama' => $tesPertama,
                'tes_terakhir' => $tesTerakhir,
                'selisih_skor' => $tesTerakhir->skor_akhir - $tesPertama->skor_akhir,
                'persentase_naik' => $tesPertama->skor_akhir > 0 ? (($tesTerakhir->skor_akhir - $tesPertama->skor_akhir) / $tesPertama->skor_akhir) * 100 : 0,
                'total_tes' => $tes->count(),
                'skor_tertinggi' => $tes->max('skor_akhir'),
                'skor_terendah' => $tes->min('skor_akhir'),
                'rata_rata' => $tes->avg('skor_akhir')
            ];
        }

        // Sort berdasarkan selisih skor (terbesar dulu)
        usort($analisis, function($a, $b) {
            return $b['selisih_skor'] <=> $a['selisih_skor'];
        });

        return $analisis;
    }
}
