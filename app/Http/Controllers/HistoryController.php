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

                // Sistem akademik tidak menggunakan TKP (Tes Karakteristik Pribadi)
                $isTkp = false;

                // TKP scoring tidak digunakan dalam sistem akademik
                $tkpN = null;
                $tkpT = null;
                $tkpFinal = null;

                // Determine status based on tryout type (akademik system)
                $status = $this->getTryoutStatus($correctAnswers, $totalQuestions);

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

        // Combine and sort all history
        $allHistory = $tryoutHistory
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


}
