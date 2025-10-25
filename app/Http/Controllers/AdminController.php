<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Mengambil statistik untuk dashboard
        $totalUsers = User::where('role', 'user')->count();
        $activeSubscriptions = Subscription::where('end_date', '>', now())
            ->where('payment_status', 'paid')
            ->count();
        $totalRevenue = Subscription::where('payment_status', 'paid')
            ->sum('amount_paid');

        return view('admin.dashboard', compact('totalUsers', 'activeSubscriptions', 'totalRevenue'));
    }

    public function userList(Request $request)
    {
        $query = User::where('role', 'user')
            ->with(['subscriptions' => function ($query) {
                $query->latest();
            }]);

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function userDetail($id)
    {
        $user = User::with(['subscriptions' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        return view('admin.users.detail', compact('user'));
    }

    public function subscriptionList()
    {
        $subscriptions = Subscription::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function revenueReport()
    {
        $monthlyRevenue = Subscription::where('payment_status', 'paid')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount_paid) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.revenue.report', compact('monthlyRevenue'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cek apakah request hanya untuk update status
        if ($request->has('is_active') && !$request->has('name')) {
            $request->validate([
                'is_active' => 'required|in:0,1'
            ]);

            $user->update([
                'is_active' => (bool) $request->is_active
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Status pengguna berhasil diperbarui.');
        }

        // Untuk update data lengkap
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,admin',
            'is_active' => 'sometimes|in:0,1' // sometimes untuk optional
        ]);

        $updateData = $request->only(['name', 'email', 'role']);

        // Tambahkan is_active jika ada di request
        if ($request->has('is_active')) {
            $updateData['is_active'] = (bool) $request->is_active;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function updatePackage(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'package' => 'nullable|in:,free,kecermatan,kecerdasan,kepribadian,lengkap'
        ]);

        $package = $request->input('package');
        
        // Debug log
        \Log::info('Package update request', [
            'user_id' => $userId,
            'package' => $package,
            'request_data' => $request->all()
        ]);

        try {
            // Update package field directly in users table
            $user->update([
                'package' => $package === '' ? null : $package
            ]);

            \Log::info('Package updated successfully', [
                'user_id' => $userId,
                'new_package' => $package
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Package pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Package update failed', [
                'user_id' => $userId,
                'package' => $package,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.users.index')
                ->with('error', 'Gagal memperbarui package pengguna: ' . $e->getMessage());
        }
    }

    public function riwayatTes(Request $request)
    {
        // Get list of users with test statistics
        $query = User::where('role', 'user')
            ->withCount(['hasilTes as total_tests'])
            ->withCount(['hasilTes as today_tests' => function ($q) {
                $q->whereDate('tanggal_tes', today());
            }])
            ->withCount(['hasilTes as this_week_tests' => function ($q) {
                $q->whereBetween('tanggal_tes', [now()->startOfWeek(), now()->endOfWeek()]);
            }])
            ->withCount(['hasilTes as this_month_tests' => function ($q) {
                $q->whereMonth('tanggal_tes', now()->month)->whereYear('tanggal_tes', now()->year);
            }]);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by package
        if ($request->has('package') && !empty($request->package)) {
            $query->where('package', $request->package);
        }

        $users = $query->orderBy('total_tests', 'desc')
                      ->orderBy('name', 'asc')
                      ->paginate(20);

        // Get statistics for overview cards
        $totalUsers = User::where('role', 'user')->count();
        $totalTests = DB::table('hasil_tes')->count();
        $todayTests = DB::table('hasil_tes')->whereDate('tanggal_tes', today())->count();
        $thisWeekTests = DB::table('hasil_tes')->whereBetween('tanggal_tes', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthTests = DB::table('hasil_tes')->whereMonth('tanggal_tes', now()->month)->whereYear('tanggal_tes', now()->year)->count();

        // Get package distribution
        $packageStats = User::where('role', 'user')
            ->select('package', DB::raw('count(*) as total'))
            ->groupBy('package')
            ->get();

        return view('admin.riwayat-tes', compact(
            'users', 
            'totalUsers',
            'totalTests', 
            'todayTests', 
            'thisWeekTests', 
            'thisMonthTests',
            'packageStats'
        ));
    }

    public function riwayatTesUser($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        
        $query = DB::table('hasil_tes')
            ->where('user_id', $userId)
            ->select('hasil_tes.*');

        // Filter by jenis tes
        if ($request->has('jenis_tes') && !empty($request->jenis_tes)) {
            $query->where('hasil_tes.jenis_tes', $request->jenis_tes);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('hasil_tes.tanggal_tes', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('hasil_tes.tanggal_tes', '<=', $request->date_to);
        }

        $hasilTes = $query->orderBy('hasil_tes.tanggal_tes', 'desc')->paginate(20);

        // Get user statistics
        $userStats = [
            'total_tests' => DB::table('hasil_tes')->where('user_id', $userId)->count(),
            'today_tests' => DB::table('hasil_tes')->where('user_id', $userId)->whereDate('tanggal_tes', today())->count(),
            'this_week_tests' => DB::table('hasil_tes')->where('user_id', $userId)->whereBetween('tanggal_tes', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_tests' => DB::table('hasil_tes')->where('user_id', $userId)->whereMonth('tanggal_tes', now()->month)->whereYear('tanggal_tes', now()->year)->count(),
        ];

        // Get test type distribution for this user
        $testTypeStats = DB::table('hasil_tes')
            ->where('user_id', $userId)
            ->select('jenis_tes', DB::raw('count(*) as total'))
            ->whereNotNull('jenis_tes')
            ->groupBy('jenis_tes')
            ->get();

        return view('admin.riwayat-tes-user', compact(
            'user',
            'hasilTes', 
            'userStats',
            'testTypeStats'
        ));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting admin users
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus akun admin.');
        }

        // Delete related data first
        $user->subscriptions()->delete();
        DB::table('hasil_tes')->where('user_id', $id)->delete();

        // Delete the user
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }

    public function getTestDetail($testId)
    {
        try {
            $test = DB::table('hasil_tes')->where('id', $testId)->first();
            
            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tes tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'test' => $test
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tes'
            ], 500);
        }
    }

    public function downloadTestPDF($testId)
    {
        try {
            $test = DB::table('hasil_tes')->where('id', $testId)->first();
            
            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tes tidak ditemukan'
                ], 404);
            }

            // Get user data
            $user = DB::table('users')->where('id', $test->user_id)->first();
            
            // Calculate additional data
            $totalQuestions = $test->skor_benar + $test->skor_salah;
            $unansweredQuestions = ($test->total_soal ?? $totalQuestions) - $totalQuestions;
            $scorePercentage = $totalQuestions > 0 ? round(($test->skor_benar / $totalQuestions) * 100) : 0;
            
            // Calculate percentile (mock calculation)
            $percentile = $this->calculatePercentile($scorePercentage);
            
            // Generate recommendations
            $recommendations = $this->generateRecommendations($scorePercentage, $test->kategori_skor ?? 'fair', $test->jenis_tes ?? 'tes');
            
            // Prepare data for PDF
            $pdfData = [
                'test' => $test,
                'user' => $user,
                'totalQuestions' => $totalQuestions,
                'unansweredQuestions' => $unansweredQuestions,
                'scorePercentage' => $scorePercentage,
                'percentile' => $percentile,
                'recommendations' => $recommendations,
                'testDate' => \Carbon\Carbon::parse($test->tanggal_tes)->format('d F Y'),
                'testTime' => \Carbon\Carbon::parse($test->tanggal_tes)->format('H:i'),
                'duration' => $test->waktu_total ? round($test->waktu_total / 60) : 0,
            ];

            // Generate PDF using DomPDF
            $html = $this->generatePDFHTML($pdfData);
            
            // Create PDF with proper settings for color printing
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'isPhpEnabled' => true,
                'isJavascriptEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'debugLayoutLines' => false,
                'debugLayoutBlocks' => false,
                'debugLayoutInline' => false,
                'debugLayoutPaddingBox' => false,
                'dpi' => 96,
                'defaultMediaType' => 'print',
                'isFontSubsettingEnabled' => true,
            ]);
            
            return $pdf->stream('hasil_tes_' . $testId . '.pdf');
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculatePercentile($score)
    {
        if ($score >= 90) return 95;
        if ($score >= 80) return 85;
        if ($score >= 70) return 70;
        if ($score >= 60) return 50;
        if ($score >= 50) return 30;
        return 15;
    }

    private function generateRecommendations($score, $category, $testType)
    {
        $recommendations = [];
        
        if ($score >= 80) {
            $recommendations[] = 'Performa sangat baik! Pertahankan konsistensi dan terus berlatih.';
        } else if ($score >= 60) {
            $recommendations[] = 'Hasil cukup baik. Fokus pada area yang masih lemah untuk peningkatan.';
        } else {
            $recommendations[] = 'Perlu peningkatan. Disarankan untuk latihan lebih intensif dan konsisten.';
        }
        
        // Test type specific recommendations
        if ($testType === 'kecermatan') {
            $recommendations[] = 'Latih kecepatan dan ketelitian dalam mengamati detail untuk meningkatkan performa.';
        } else if ($testType === 'kecerdasan') {
            $recommendations[] = 'Tingkatkan kemampuan analisis dan pemecahan masalah melalui latihan rutin.';
        } else if ($testType === 'kepribadian') {
            $recommendations[] = 'Evaluasi jawaban untuk memahami karakteristik diri dan area pengembangan.';
        }
        
        $recommendations[] = 'Manajemen waktu yang lebih baik dapat meningkatkan hasil tes.';
        $recommendations[] = 'Lakukan latihan rutin untuk meningkatkan kemampuan dan kepercayaan diri.';
        
        return $recommendations;
    }

    private function generatePDFHTML($data)
    {
        $test = $data['test'];
        $user = $data['user'];
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Hasil Tes - ' . $user->name . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1ab394; padding-bottom: 20px; }
                .header h1 { color: #1ab394; margin: 0; font-size: 28px; }
                .header p { color: #666; margin: 5px 0; }
                .section { margin-bottom: 25px; }
                .section h2 { color: #333; border-left: 4px solid #1ab394; padding-left: 15px; margin-bottom: 15px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
                .info-item { background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #1ab394; }
                .info-label { font-weight: bold; color: #333; margin-bottom: 5px; }
                .info-value { color: #666; }
                .score-display { text-align: center; margin: 20px 0; }
                .score-circle { width: 120px; height: 120px; border-radius: 50%; background: #1ab394 !important; color: white !important; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto 20px; }
                .score-number { font-size: 36px; font-weight: bold; line-height: 1; }
                .score-label { font-size: 14px; opacity: 0.9; }
                .breakdown { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0; }
                .breakdown-item { text-align: center; padding: 15px; border-radius: 6px; }
                .breakdown-correct { background: #d4edda !important; color: #155724 !important; }
                .breakdown-wrong { background: #f8d7da !important; color: #721c24 !important; }
                .breakdown-unanswered { background: #fff3cd !important; color: #856404 !important; }
                .breakdown-total { background: #1ab394 !important; color: white !important; }
                .breakdown-number { font-size: 24px; font-weight: bold; display: block; }
                .breakdown-label { font-size: 12px; margin-top: 5px; }
                .recommendations { background: #f8f9fa; padding: 20px; border-radius: 6px; border-left: 4px solid #1ab394; }
                .recommendations ul { margin: 0; padding-left: 20px; }
                .recommendations li { margin-bottom: 8px; color: #333; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
                .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
                .badge-primary { background: #1ab394 !important; color: white !important; }
                .badge-success { background: #1ab394 !important; color: white !important; }
                .badge-warning { background: #f8ac59 !important; color: white !important; }
                .badge-danger { background: #ed5565 !important; color: white !important; }
                @media print { 
                    body { background: white !important; } 
                    .container { box-shadow: none !important; } 
                    * { 
                        -webkit-print-color-adjust: exact !important; 
                        color-adjust: exact !important; 
                        print-color-adjust: exact !important;
                    }
                }
                @page { 
                    margin: 0.5in; 
                    size: A4; 
                }
                /* Force colors for all elements */
                .score-circle, .breakdown-correct, .breakdown-wrong, .breakdown-unanswered, .breakdown-total, .badge-primary, .badge-success, .badge-warning, .badge-danger {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <!-- Header -->
                <div class="header">
                    <h1>HASIL TES</h1>
                    <p><strong>' . $user->name . '</strong></p>
                    <p>' . $user->email . '</p>
                    <p>Test ID: ' . $test->id . ' | ' . $data['testDate'] . '</p>
                </div>

                <!-- Test Information -->
                <div class="section">
                    <h2>Informasi Tes</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Jenis Tes</div>
                            <div class="info-value">
                                <span class="badge badge-primary" style="background-color: #1ab394 !important; color: white !important;">' . ucfirst($test->jenis_tes ?? 'Tes') . '</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal & Waktu</div>
                            <div class="info-value">' . $data['testDate'] . ' pukul ' . $data['testTime'] . '</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Durasi</div>
                            <div class="info-value">' . $data['duration'] . ' menit</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Percobaan</div>
                            <div class="info-value">Ke-' . ($test->attempt_number ?? 1) . '</div>
                        </div>
                    </div>
                </div>

                <!-- Score Display -->
                <div class="section">
                    <h2>Hasil Tes</h2>
                        <div class="score-display">
                            <div class="score-circle" style="background-color: #1ab394 !important; color: white !important;">
                                <span class="score-number">' . $data['scorePercentage'] . '%</span>
                                <span class="score-label">Skor</span>
                            </div>
                            ' . ($test->skor_akhir ? '<p><strong>Skor Akhir: ' . $test->skor_akhir . '</strong></p>' : '') . '
                            ' . ($test->kategori_skor ? '<p><span class="badge badge-success" style="background-color: #1ab394 !important; color: white !important;">' . ucfirst($test->kategori_skor) . '</span></p>' : '') . '
                        </div>
                </div>

                <!-- Breakdown -->
                <div class="section">
                    <h2>Breakdown Soal</h2>
                    <div class="breakdown">
                        <div class="breakdown-item breakdown-correct" style="background-color: #d4edda !important; color: #155724 !important;">
                            <span class="breakdown-number">' . $test->skor_benar . '</span>
                            <span class="breakdown-label">Benar</span>
                        </div>
                        <div class="breakdown-item breakdown-wrong" style="background-color: #f8d7da !important; color: #721c24 !important;">
                            <span class="breakdown-number">' . $test->skor_salah . '</span>
                            <span class="breakdown-label">Salah</span>
                        </div>
                        <div class="breakdown-item breakdown-unanswered" style="background-color: #fff3cd !important; color: #856404 !important;">
                            <span class="breakdown-number">' . $data['unansweredQuestions'] . '</span>
                            <span class="breakdown-label">Tidak Dijawab</span>
                        </div>
                        <div class="breakdown-item breakdown-total" style="background-color: #1ab394 !important; color: white !important;">
                            <span class="breakdown-number">' . ($test->total_soal ?? $data['totalQuestions']) . '</span>
                            <span class="breakdown-label">Total Soal</span>
                        </div>
                    </div>
                </div>

                <!-- Performance Analysis -->
                <div class="section">
                    <h2>Analisis Performa</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Percentile</div>
                            <div class="info-value"><strong>' . $data['percentile'] . '%</strong><br>Lebih baik dari ' . $data['percentile'] . '% siswa</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Perbandingan</div>
                            <div class="info-value">' . ($data['scorePercentage'] >= 75 ? 'Di atas rata-rata' : 'Di bawah rata-rata') . '</div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="section">
                    <h2>Rekomendasi</h2>
                    <div class="recommendations">
                        <ul>
                            ' . implode('', array_map(function($rec) { return '<li>' . $rec . '</li>'; }, $data['recommendations'])) . '
                        </ul>
                    </div>
                </div>

                <!-- Footer -->
                <div class="footer">
                    <p>Dokumen ini dibuat secara otomatis pada ' . now()->format('d F Y H:i') . '</p>
                    <p>Untuk pertanyaan lebih lanjut, silakan hubungi administrator sistem.</p>
                </div>
            </div>
        </body>
        </html>';
    }
}
