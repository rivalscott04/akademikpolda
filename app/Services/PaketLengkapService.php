<?php

namespace App\Services;

use App\Models\User;
use App\Models\HasilTes;
use App\Models\UserTryoutSession;
use App\Models\PackageCategoryMapping;
use App\Models\ScoringSetting;
use App\Services\ScoringService;
use Illuminate\Support\Collection;

class PaketLengkapService
{
    /**
     * Get completion status for paket lengkap user
     */
    public function getCompletionStatus(User $user): array
    {
        if ($user->paket_akses !== 'lengkap') {
            return [
                'is_eligible' => false,
                'message' => 'User tidak memiliki paket lengkap'
            ];
        }

        // OPTIMASI: Cache hasil untuk user ini (cache 10 detik untuk hasil instant)
        return cache()->remember("paket_lengkap_status_{$user->id}", 10, function () use ($user) {
            // OPTIMASI: Load semua data dalam 1 query besar
            $allData = $this->getAllCompletionDataInOneQuery($user);
            
            $akademikStatus = $allData['akademik'];
            $simulasiStatus = $allData['simulasi'];

            // Akademik wajib + simulasi wajib
            $isComplete = $akademikStatus['completed'] && $simulasiStatus['completed'];

            return [
                'is_eligible' => true,
                'is_complete' => $isComplete,
                'akademik' => $akademikStatus,
                'simulasi' => $simulasiStatus,
                'final_score' => $isComplete ? $this->calculateFinalScoreFromData($allData) : null,
                'scoring_info' => $isComplete ? $this->getScoringInfo($allData) : null
            ];
        });
    }




    /**
     * Calculate final score for paket lengkap
     */
    public function calculateFinalScore(User $user): ?float
    {
        $status = $this->getCompletionStatus($user);
        
        if (!$status['is_complete']) {
            return null;
        }

        $akademikScore = $status['akademik']['score'];
        $simulasiScore = $status['simulasi']['score'];

        // Kedua skor wajib
        if (!is_numeric($akademikScore) || !is_numeric($simulasiScore)) {
            return null;
        }

        // Calculate average of both scores
        $finalScore = ($akademikScore + $simulasiScore) / 2;
        
        return round($finalScore, 2);
    }


    /**
     * Get progress percentage for paket lengkap
     */
    public function getProgressPercentage(User $user): int
    {
        if ($user->paket_akses !== 'lengkap') {
            return 0;
        }

        // OPTIMASI: Cache progress percentage (cache 10 detik untuk hasil instant)
        return cache()->remember("paket_lengkap_progress_{$user->id}", 10, function () use ($user) {
            $status = $this->getCompletionStatus($user);
            $completedCount = 0;

            // Akademik wajib (1 poin)
            if ($status['akademik']['completed']) $completedCount++;
            
            // Simulasi wajib (1 poin)
            if ($status['simulasi']['completed']) $completedCount++;

            // Total maksimal 2 poin (akademik + simulasi)
            return round(($completedCount / 2) * 100);
        });
    }

    /**
     * Get summary for paket lengkap dashboard
     */
    public function getDashboardSummary(User $user): array
    {
        $status = $this->getCompletionStatus($user);
        
        if (!$status['is_eligible']) {
            return [
                'title' => 'Paket Lengkap',
                'progress' => 0,
                'status' => 'not_eligible',
                'message' => 'Anda tidak memiliki paket lengkap'
            ];
        }

        $progress = $this->getProgressPercentage($user);
        
        if ($status['is_complete']) {
            return [
                'title' => 'Paket Lengkap',
                'progress' => 100,
                'status' => 'completed',
                'message' => 'Paket lengkap sudah selesai!',
                'final_score' => $status['scoring_info']['final_score'],
                'passed' => $status['scoring_info']['passed'],
                'passing_grade' => $status['scoring_info']['passing_grade'],
                'details' => [
                    'akademik' => $status['akademik'],
                    'simulasi' => $status['simulasi']
                ]
            ];
        }

        $remainingTasks = [];
        if (!$status['akademik']['completed']) $remainingTasks[] = 'Tes AKADEMIK';
        if (!$status['simulasi']['completed']) $remainingTasks[] = 'Simulasi Nilai';

        return [
            'title' => 'Paket Lengkap',
            'progress' => $progress,
            'status' => 'in_progress',
            'message' => 'Selesaikan: ' . implode(', ', $remainingTasks),
            'details' => [
                'akademik' => $status['akademik'],
                'simulasi' => $status['simulasi']
            ]
        ];
    }

    /**
     * Clear cache for a specific user
     */
    public function clearUserCache(User $user): void
    {
        cache()->forget("paket_lengkap_status_{$user->id}");
        cache()->forget("paket_lengkap_progress_{$user->id}");
    }

    /**
     * Clear all paket lengkap caches
     */
    public function clearAllCache(): void
    {
        // Note: This is a simple approach. For production, consider using cache tags
        $users = User::where('package', 'lengkap')->get();
        foreach ($users as $user) {
            $this->clearUserCache($user);
        }
    }

    /**
     * OPTIMASI: Load semua data completion dalam 1 query besar
     */
    private function getAllCompletionDataInOneQuery(User $user): array
    {
        // 1. Ambil data akademik (query terpisah karena tabel berbeda)
        $akademikResult = HasilTes::where('user_id', $user->id)
            ->whereIn('jenis_tes', ['bahasa_inggris', 'pu', 'twk', 'numerik'])
            ->orderBy('tanggal_tes', 'desc')
            ->first();

        $akademikStatus = [
            'completed' => $akademikResult ? true : false,
            'score' => $akademikResult ? $akademikResult->skor_akhir : null,
            'tanggal' => $akademikResult ? $akademikResult->tanggal_tes : null,
            'message' => $akademikResult ? 'Tes AKADEMIK sudah selesai' : 'Belum mengerjakan tes AKADEMIK'
        ];

        // 2. Ambil data simulasi
        $simulasiResult = HasilTes::where('user_id', $user->id)
            ->where('jenis_tes', 'lengkap')
            ->orderBy('tanggal_tes', 'desc')
            ->first();

        $simulasiStatus = [
            'completed' => $simulasiResult ? true : false,
            'score' => $simulasiResult ? $simulasiResult->skor_akhir : null,
            'tanggal' => $simulasiResult ? $simulasiResult->tanggal_tes : null,
            'message' => $simulasiResult ? 'Simulasi Nilai sudah selesai' : 'Belum mengerjakan Simulasi Nilai'
        ];

        // 3. OPTIMASI: Single query untuk semua tryout sessions dengan eager loading
        $completedTryouts = UserTryoutSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with([
                'tryout:id,judul',
                'tryout.blueprints.kategori:id,kode'
            ])
            ->orderBy('finished_at', 'desc')
            ->get();

        // 4. OPTIMASI: Single query untuk semua user answers dengan eager loading
        $allUserAnswers = \App\Models\UserTryoutSoal::where('user_id', $user->id)
            ->whereIn('user_tryout_session_id', $completedTryouts->pluck('id'))
            ->with(['soal:id,kategori_id', 'soal.kategori:id,kode'])
            ->get();

        // 5. Group answers by session untuk efisiensi
        $answersBySession = $allUserAnswers->groupBy('user_tryout_session_id');

        return [
            'akademik' => $akademikStatus,
            'simulasi' => $simulasiStatus
        ];
    }



    /**
     * Calculate final score from pre-loaded data
     */
    private function calculateFinalScoreFromData(array $allData): float
    {
        $akademikScore = $allData['akademik']['score'];
        $simulasiScore = $allData['simulasi']['score'];

        // Kedua skor wajib
        if (!is_numeric($akademikScore) || !is_numeric($simulasiScore)) {
            return 0;
        }

        // Calculate average of both scores
        $finalScore = ($akademikScore + $simulasiScore) / 2;
        
        return round($finalScore, 2);
    }

    /**
     * Get scoring information including pass/fail status
     */
    private function getScoringInfo(array $allData): array
    {
        $akademikScore = $allData['akademik']['score'];
        $simulasiScore = $allData['simulasi']['score'];

        // Calculate final score as average of both scores
        $finalScore = ($akademikScore + $simulasiScore) / 2;
        
        // Simple passing grade check (adjust as needed)
        $passingGrade = 60; // Default passing grade
        $passed = $finalScore >= $passingGrade;

        return [
            'final_score' => round($finalScore, 2),
            'passed' => $passed,
            'passing_grade' => $passingGrade,
            'weights' => [
                'akademik' => 50,
                'simulasi' => 50
            ]
        ];
    }
}
