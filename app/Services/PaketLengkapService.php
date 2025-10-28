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
                'bahasa_inggris' => $allData['bahasa_inggris'],
                'pu' => $allData['pu'],
                'twk' => $allData['twk'],
                'numerik' => $allData['numerik'],
                'final_score' => $akademikStatus['completed'] ? $this->calculateFinalScoreFromData($allData) : null,
                'scoring_info' => $akademikStatus['completed'] ? $this->getScoringInfo($allData) : null
            ];
        });
    }




    /**
     * Calculate final score for paket lengkap
     */
    public function calculateFinalScore(User $user): ?float
    {
        $status = $this->getCompletionStatus($user);
        
        if (!$status['akademik']['completed']) {
            return null;
        }

        // Hitung final score dengan formula: (skor tiap paket) dijumlahkan dibagi 4
        $bahasaInggrisScore = $status['bahasa_inggris']['score'] ?? 0;
        $puScore = $status['pu']['score'] ?? 0;
        $twkScore = $status['twk']['score'] ?? 0;
        $numerikScore = $status['numerik']['score'] ?? 0;

        $finalScore = round(($bahasaInggrisScore + $puScore + $twkScore + $numerikScore) / 4, 2);
        
        return $finalScore;
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

            // Akademik wajib (1 poin) - semua jenis tes harus selesai
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
                'weights' => $status['scoring_info']['weights'],
                'details' => [
                    'akademik' => $status['akademik'],
                    'simulasi' => $status['simulasi'],
                    'bahasa_inggris' => $status['bahasa_inggris'],
                    'pu' => $status['pu'],
                    'twk' => $status['twk'],
                    'numerik' => $status['numerik']
                ]
            ];
        }

        $remainingTasks = [];
        if (!$status['akademik']['completed']) {
            $akademikTasks = [];
            if (!$status['bahasa_inggris']['completed']) $akademikTasks[] = 'Bahasa Inggris';
            if (!$status['pu']['completed']) $akademikTasks[] = 'PU';
            if (!$status['twk']['completed']) $akademikTasks[] = 'TWK';
            if (!$status['numerik']['completed']) $akademikTasks[] = 'Numerik';
            $remainingTasks[] = 'Tes AKADEMIK (' . implode(', ', $akademikTasks) . ')';
        }
        if (!$status['simulasi']['completed']) $remainingTasks[] = 'Simulasi Nilai';

        return [
            'title' => 'Paket Lengkap',
            'progress' => $progress,
            'status' => 'in_progress',
            'message' => 'Selesaikan: ' . implode(', ', $remainingTasks),
            'details' => [
                'akademik' => $status['akademik'],
                'simulasi' => $status['simulasi'],
                'bahasa_inggris' => $status['bahasa_inggris'],
                'pu' => $status['pu'],
                'twk' => $status['twk'],
                'numerik' => $status['numerik']
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
        // 1. Ambil data per jenis tes akademik
        $bahasaInggrisResult = HasilTes::where('user_id', $user->id)
            ->where('jenis_tes', 'bahasa_inggris')
            ->orderBy('tanggal_tes', 'desc')
            ->first();

        $puResult = HasilTes::where('user_id', $user->id)
            ->where('jenis_tes', 'pu')
            ->orderBy('tanggal_tes', 'desc')
            ->first();

        $twkResult = HasilTes::where('user_id', $user->id)
            ->where('jenis_tes', 'twk')
            ->orderBy('tanggal_tes', 'desc')
            ->first();

        $numerikResult = HasilTes::where('user_id', $user->id)
            ->where('jenis_tes', 'numerik')
            ->orderBy('tanggal_tes', 'desc')
            ->first();

        // Status per jenis tes
        $bahasaInggrisStatus = [
            'completed' => $bahasaInggrisResult ? true : false,
            'score' => $bahasaInggrisResult ? $bahasaInggrisResult->skor_akhir : null,
            'tanggal' => $bahasaInggrisResult ? $bahasaInggrisResult->tanggal_tes : null,
            'message' => $bahasaInggrisResult ? 'Tes Bahasa Inggris sudah selesai' : 'Belum mengerjakan tes Bahasa Inggris'
        ];

        $puStatus = [
            'completed' => $puResult ? true : false,
            'score' => $puResult ? $puResult->skor_akhir : null,
            'tanggal' => $puResult ? $puResult->tanggal_tes : null,
            'message' => $puResult ? 'Tes PU sudah selesai' : 'Belum mengerjakan tes PU'
        ];

        $twkStatus = [
            'completed' => $twkResult ? true : false,
            'score' => $twkResult ? $twkResult->skor_akhir : null,
            'tanggal' => $twkResult ? $twkResult->tanggal_tes : null,
            'message' => $twkResult ? 'Tes TWK sudah selesai' : 'Belum mengerjakan tes TWK'
        ];

        $numerikStatus = [
            'completed' => $numerikResult ? true : false,
            'score' => $numerikResult ? $numerikResult->skor_akhir : null,
            'tanggal' => $numerikResult ? $numerikResult->tanggal_tes : null,
            'message' => $numerikResult ? 'Tes Numerik sudah selesai' : 'Belum mengerjakan tes Numerik'
        ];

        // Status akademik (semua jenis tes harus selesai)
        $akademikCompleted = $bahasaInggrisStatus['completed'] && 
                           $puStatus['completed'] && 
                           $twkStatus['completed'] && 
                           $numerikStatus['completed'];

        $akademikStatus = [
            'completed' => $akademikCompleted,
            'score' => $akademikCompleted ? $this->calculateFinalScoreFromData([
                'bahasa_inggris' => $bahasaInggrisStatus,
                'pu' => $puStatus,
                'twk' => $twkStatus,
                'numerik' => $numerikStatus
            ]) : null,
            'tanggal' => $akademikCompleted ? max(
                $bahasaInggrisResult->tanggal_tes,
                $puResult->tanggal_tes,
                $twkResult->tanggal_tes,
                $numerikResult->tanggal_tes
            ) : null,
            'message' => $akademikCompleted ? 'Semua tes AKADEMIK sudah selesai' : 'Belum menyelesaikan semua tes AKADEMIK'
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
            'simulasi' => $simulasiStatus,
            'bahasa_inggris' => $bahasaInggrisStatus,
            'pu' => $puStatus,
            'twk' => $twkStatus,
            'numerik' => $numerikStatus
        ];
    }



    /**
     * Calculate final score from pre-loaded data
     * Formula: (skor tiap paket × bobot) dijumlahkan dibagi 4
     */
    private function calculateFinalScoreFromData(array $allData): float
    {
        // Ambil skor per jenis tes dari data akademik
        $bahasaInggrisScore = $allData['bahasa_inggris']['score'] ?? 0;
        $puScore = $allData['pu']['score'] ?? 0;
        $twkScore = $allData['twk']['score'] ?? 0;
        $numerikScore = $allData['numerik']['score'] ?? 0;

        // Gunakan ScoringService untuk menghitung dengan bobot
        $scoringService = app(ScoringService::class);
        $result = $scoringService->calculateFinalScore(
            (float) $bahasaInggrisScore,
            (float) $puScore,
            (float) $twkScore,
            (float) $numerikScore
        );
        
        // Sesuai permintaan: (skor tiap paket × bobot) dijumlahkan dibagi 4
        $setting = \App\Models\ScoringSetting::current();
        $w1 = $setting->weight_bahasa_inggris / 100;
        $w2 = $setting->weight_pu / 100;
        $w3 = $setting->weight_twk / 100;
        $w4 = $setting->weight_numerik / 100;

        $weightedSum = ($w1 * $bahasaInggrisScore) + ($w2 * $puScore) + ($w3 * $twkScore) + ($w4 * $numerikScore);
        $finalScore = $weightedSum / 4;
        
        return round($finalScore, 2);
    }

    /**
     * Get scoring information including pass/fail status
     */
    private function getScoringInfo(array $allData): array
    {
        // Ambil skor per jenis tes dari data akademik
        $bahasaInggrisScore = $allData['bahasa_inggris']['score'] ?? 0;
        $puScore = $allData['pu']['score'] ?? 0;
        $twkScore = $allData['twk']['score'] ?? 0;
        $numerikScore = $allData['numerik']['score'] ?? 0;

        // Hitung final score dengan formula: (skor tiap paket × bobot) dijumlahkan dibagi 4
        $setting = \App\Models\ScoringSetting::current();
        $w1 = $setting->weight_bahasa_inggris / 100;
        $w2 = $setting->weight_pu / 100;
        $w3 = $setting->weight_twk / 100;
        $w4 = $setting->weight_numerik / 100;

        $weightedSum = ($w1 * $bahasaInggrisScore) + ($w2 * $puScore) + ($w3 * $twkScore) + ($w4 * $numerikScore);
        $finalScore = round($weightedSum / 4, 2);
        
        // Cek kelulusan berdasarkan passing grade
        $passed = $finalScore >= (float) $setting->passing_grade;

        return [
            'final_score' => $finalScore,
            'passed' => $passed,
            'passing_grade' => (int) $setting->passing_grade,
            'weights' => [
                'bahasa_inggris' => (int) $setting->weight_bahasa_inggris,
                'pu' => (int) $setting->weight_pu,
                'twk' => (int) $setting->weight_twk,
                'numerik' => (int) $setting->weight_numerik,
            ]
        ];
    }
}
