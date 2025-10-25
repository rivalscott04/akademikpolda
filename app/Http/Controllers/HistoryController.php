<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserTryoutSession;
use App\Models\HasilTes;
use App\Models\UserTryoutSoal;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get tryout history
        $tryoutHistory = UserTryoutSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['tryout'])
            ->orderBy('finished_at', 'desc')
            ->get()
            ->map(function ($session) {
                // Calculate total score for this tryout
                $answersQuery = UserTryoutSoal::where('user_id', $session->user_id)
                    ->where('tryout_id', $session->tryout_id)
                    ->where('user_tryout_session_id', $session->id);
                $totalScore = (clone $answersQuery)->sum('skor');

                $totalQuestions = (clone $answersQuery)->count();

                $correctAnswers = (clone $answersQuery)->where('skor', '>', 0)->count();

                // Determine if this tryout session is TKP by checking kategori codes
                $kepribadianKategoriCodes = \App\Models\PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                $tkpQuestions = (clone $answersQuery)->with('soal.kategori')->get()->filter(function ($ans) use ($kepribadianKategoriCodes) {
                    $kategori = $ans->soal->kategori ?? null;
                    return $kategori && in_array($kategori->kode, $kepribadianKategoriCodes);
                });
                $isTkp = $tkpQuestions->count() > 0;

                $tkpN = null;
                $tkpT = null;
                $tkpFinal = null;
                if ($isTkp) {
                    $tkpN = $tkpQuestions->count();
                    $tkpT = (int) round($tkpQuestions->sum('skor'));
                    try {
                        $svc = app(\App\Services\TkpScoringService::class);
                        $tkpFinal = $svc->calculateFinalScore($tkpN, $tkpT);
                    } catch (\Throwable $e) {
                        $tkpFinal = null;
                    }
                }

                // Determine status based on tryout type
                $status = $isTkp && $tkpFinal !== null
                    ? $this->getTkpStatus($tkpFinal)
                    : $this->getTryoutStatus($correctAnswers, $totalQuestions);

                return [
                    'id' => $session->id,
                    'tryout_id' => $session->tryout_id,
                    'type' => 'tryout',
                    'title' => $session->tryout->judul ?? 'Tryout',
                    'date' => $session->finished_at,
                    'score' => $totalScore,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'wrong_answers' => $totalQuestions - $correctAnswers,
                    'percentage' => $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0,
                    'duration' => $session->elapsed_minutes,
                    'status' => $status,
                    'jenis_paket' => $session->tryout->jenis_paket ?? null,
                    // TKP extras for view
                    'is_tkp' => $isTkp,
                    'tkp_n' => $tkpN,
                    'tkp_t' => $tkpT,
                    'tkp_final' => $tkpFinal,
                ];
            });

        // Get kecermatan history
        $kecermatanHistory = HasilTes::where('user_id', $user->id)
            ->where('jenis_tes', 'kecermatan')
            ->orderBy('tanggal_tes', 'desc')
            ->get()
            ->map(function ($hasil) {
                $totalQuestions = $hasil->skor_benar + $hasil->skor_salah;
                
                // Use skor_akhir if available (calculated with complex algorithm), 
                // otherwise fallback to simple percentage calculation
                $percentage = $hasil->skor_akhir !== null 
                    ? round($hasil->skor_akhir, 2) 
                    : ($totalQuestions > 0 ? round(($hasil->skor_benar / $totalQuestions) * 100, 2) : 0);

                return [
                    'id' => $hasil->id,
                    'type' => 'kecermatan',
                    'title' => 'Tes Kecermatan',
                    'date' => $hasil->tanggal_tes,
                    'score' => $hasil->skor_benar,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $hasil->skor_benar,
                    'wrong_answers' => $hasil->skor_salah,
                    'percentage' => $percentage,
                    'duration' => $hasil->waktu_total ?? 0,
                    'status' => $this->getKecermatanStatus($percentage, 100) // Use percentage directly for status
                ];
            });

        // TODO: If/when kecerdasan & kepribadian standalone results are stored in hasil_tes,
        // add queries similar to $kecermatanHistory for 'kecerdasan' and 'kepribadian'

        // Combine and sort all history
        $allHistory = $tryoutHistory->concat($kecermatanHistory)
            ->sortByDesc('date')
            ->take(10); // Limit to 10 most recent

        return view('user.history.index', compact('allHistory'));
    }

    private function getTryoutStatus($correct, $total)
    {
        if ($total == 0) return 'unknown';

        $percentage = ($correct / $total) * 100;

        if ($percentage >= 80) return 'excellent';
        if ($percentage >= 70) return 'good';
        if ($percentage >= 60) return 'fair';
        return 'poor';
    }

    /**
     * Get TKP status based on final score according to TKP scoring system
     * 91-100: Sangat Tinggi (excellent)
     * 76-90:  Tinggi (good)
     * 61-75:  Cukup Tinggi (fair)
     * 41-60:  Sedang (poor)
     * ≤40:    Rendah (poor)
     */
    private function getTkpStatus($finalScore)
    {
        if ($finalScore === null || $finalScore === 0) return 'unknown';

        if ($finalScore >= 91) return 'excellent';      // Sangat Tinggi
        if ($finalScore >= 76) return 'good';           // Tinggi
        if ($finalScore >= 61) return 'fair';           // Cukup Tinggi
        if ($finalScore >= 41) return 'poor';           // Sedang
        return 'poor';                                  // Rendah
    }

    private function getKecermatanStatus($percentage, $maxScore = 100)
    {
        if ($maxScore == 0) return 'unknown';

        if ($percentage >= 90) return 'excellent';
        if ($percentage >= 80) return 'good';
        if ($percentage >= 70) return 'fair';
        return 'poor';
    }
}
