<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\Tryout;
use App\Models\UserTryoutSession;
use App\Models\UserTryoutSoal;
use App\Models\HasilTes;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // === STATISTIK UTAMA (FOKUS PENDIDIKAN) ===
        // Cache statistik utama untuk 5 menit
        $totalSoal = Cache::remember('dashboard.total_soal', 300, function() {
            return Soal::where('is_active', true)->count();
        });
        
        $tryoutAktif = Cache::remember('dashboard.tryout_aktif', 300, function() {
            return Tryout::where('is_active', true)->count();
        });
        
        $pesertaAktif = Cache::remember('dashboard.peserta_aktif', 60, function() {
            return UserTryoutSession::where('status', 'active')->count();
        });
        
        $selesaiHariIni = Cache::remember('dashboard.selesai_hari_ini', 60, function() {
            return UserTryoutSession::where('status', 'completed')
                ->whereDate('finished_at', Carbon::today())
                ->count();
        });

        // === STATISTIK PERTUMBUHAN ===
        
        // Pertumbuhan soal (bulan ini vs bulan lalu)
        $soalBulanIni = Soal::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $soalBulanLalu = Soal::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $soalGrowth = $soalBulanLalu > 0 ? round((($soalBulanIni - $soalBulanLalu) / $soalBulanLalu) * 100) : 0;

        // Pertumbuhan peserta (bulan ini vs bulan lalu)
        $pesertaBulanIni = User::where('role', 'user')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $pesertaBulanLalu = User::where('role', 'user')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $pesertaGrowth = $pesertaBulanLalu > 0 ? round((($pesertaBulanIni - $pesertaBulanLalu) / $pesertaBulanLalu) * 100) : 0;

        // === PERFORMANSI KATEGORI DINAMIS ===
        
        // Optimasi: Gunakan single query dengan join untuk performa kategori
        $kategoriPerformansi = KategoriSoal::where('is_active', true)
            ->withCount(['soals as total_soal' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function($kategori) {
                // Cache hasil query untuk menghindari multiple database calls
                static $skorCache = [];
                static $pesertaCache = [];
                
                if (!isset($skorCache[$kategori->id])) {
                    $skorCache[$kategori->id] = UserTryoutSoal::whereHas('soal', function($query) use ($kategori) {
                        $query->where('kategori_id', $kategori->id);
                    })
                    ->whereNotNull('skor')
                    ->avg('skor');
                }
                
                if (!isset($pesertaCache[$kategori->id])) {
                    $pesertaCache[$kategori->id] = UserTryoutSoal::whereHas('soal', function($query) use ($kategori) {
                        $query->where('kategori_id', $kategori->id);
                    })
                    ->distinct('user_id')
                    ->count('user_id');
                }
                
                return [
                    'nama' => $kategori->nama,
                    'kode' => $kategori->kode,
                    'total_soal' => $kategori->total_soal,
                    'rata_skor' => round($skorCache[$kategori->id] ?? 0, 2),
                    'total_peserta' => $pesertaCache[$kategori->id] ?? 0,
                    'warna' => $this->getKategoriColor($kategori->kode)
                ];
            })
            ->sortByDesc('total_peserta');

        // === GRAFIK TREN PARTISIPASI ===
        
        // Data 30 hari terakhir untuk grafik partisipasi
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $trenPartisipasi = [];
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $timestamp = $date->timestamp * 1000;
            $selesaiHari = UserTryoutSession::where('status', 'completed')
                ->whereDate('finished_at', $date->toDateString())
                ->count();
            $trenPartisipasi[] = [$timestamp, $selesaiHari];
        }

        // === DISTRIBUSI SKOR ===
        
        // Ambil distribusi skor dari hasil tes
        $distribusiSkor = HasilTes::select('skor_akhir')
            ->whereNotNull('skor_akhir')
            ->get()
            ->groupBy(function($item) {
                $skor = $item->skor_akhir;
                if ($skor >= 80) return '80-100';
                if ($skor >= 60) return '60-79';
                if ($skor >= 40) return '40-59';
                return '0-39';
            })
            ->map->count();

        // === TOP PERFORMERS ===
        
        $topPerformers = User::where('role', 'user')
            ->whereHas('hasilTes')
            ->with(['hasilTes' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->map(function($user) {
                $hasilTerbaru = $user->hasilTes->first();
                return [
                    'nama' => $user->name,
                    'skor' => $hasilTerbaru ? $hasilTerbaru->skor_akhir : 0,
                    'tanggal' => $hasilTerbaru ? $hasilTerbaru->tanggal_tes : null
                ];
            })
            ->sortByDesc('skor')
            ->take(5);

        // === RECENT ACTIVITY ===
        
        // Tryout yang baru dibuat (7 hari terakhir)
        $tryoutTerbaru = Tryout::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Soal yang baru diupload (7 hari terakhir)
        $soalTerbaru = Soal::where('created_at', '>=', Carbon::now()->subDays(7))
            ->with('kategori')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Peserta yang baru menyelesaikan tryout (hari ini)
        $pesertaSelesaiHariIni = UserTryoutSession::where('status', 'completed')
            ->whereDate('finished_at', Carbon::today())
            ->with(['user', 'tryout'])
            ->orderBy('finished_at', 'desc')
            ->limit(5)
            ->get();

        // === PELANGGAN BARU DAN SUBSCRIPTION ANALYSIS ===
        
        // Pelanggan baru hari ini
        $pelangganBaruHariIni = User::where('role', 'user')
            ->whereDate('created_at', Carbon::today())
            ->with(['subscriptions' => function($query) {
                $query->latest();
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Pelanggan baru minggu ini
        $pelangganBaruMingguIni = User::where('role', 'user')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Pelanggan baru bulan ini
        $pelangganBaruBulanIni = User::where('role', 'user')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Analisis subscription yang dibeli pelanggan baru (7 hari terakhir)
        $subscriptionAnalysis = User::where('role', 'user')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->whereHas('subscriptions')
            ->with(['subscriptions' => function($query) {
                $query->latest();
            }])
            ->get()
            ->map(function($user) {
                $latestSubscription = $user->subscriptions->first();
                return [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'created_at' => $user->created_at,
                    'package_type' => $latestSubscription ? $latestSubscription->package_type : 'free',
                    'amount_paid' => $latestSubscription ? $latestSubscription->amount_paid : 0,
                    'payment_status' => $latestSubscription ? $latestSubscription->payment_status : 'free'
                ];
            })
            ->groupBy('package_type')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total_revenue' => $group->sum('amount_paid'),
                    'users' => $group->take(5)->values()
                ];
            });

        // Tren pelanggan baru 7 hari terakhir
        $trenPelangganBaru = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = User::where('role', 'user')
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $trenPelangganBaru[] = [
                'date' => $date->format('d/m'),
                'count' => $count
            ];
        }

        return view('admin.dashboard', compact(
            // Statistik utama
            'totalSoal',
            'tryoutAktif', 
            'pesertaAktif',
            'selesaiHariIni',
            
            // Pertumbuhan
            'soalGrowth',
            'pesertaGrowth',
            
            // Performansi kategori dinamis
            'kategoriPerformansi',
            
            // Grafik dan visualisasi
            'trenPartisipasi',
            'distribusiSkor',
            'topPerformers',
            
            // Recent activity
            'tryoutTerbaru',
            'soalTerbaru',
            'pesertaSelesaiHariIni',
            
            // Pelanggan baru dan subscription
            'pelangganBaruHariIni',
            'pelangganBaruMingguIni',
            'pelangganBaruBulanIni',
            'subscriptionAnalysis',
            'trenPelangganBaru'
        ));
    }

    /**
     * Get color for kategori based on kode
     */
    private function getKategoriColor($kode)
    {
        $colors = [
            'TWK' => '#1ab394',    // Green
            'TIU' => '#1c84c6',    // Blue  
            'TKP' => '#f8ac59',    // Orange
            'PSIKOTES' => '#ed5565', // Red
            'TKD' => '#23c6c8',    // Teal
        ];
        
        return $colors[$kode] ?? '#d1dade'; // Default gray
    }
}
