<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Soal;
use App\Models\Tryout;
use App\Models\KategoriSoal;
use Illuminate\Http\Request;
use App\Models\UserTryoutSoal;
use App\Models\TryoutBlueprint;
use App\Services\ScoringService;
use App\Services\QuestionSelector;
use Illuminate\Support\Facades\DB;
use App\Models\PackageCategoryMapping;
use App\Models\UserTryoutSession; // Tambahkan model ini untuk tracking session

class TryoutController extends Controller
{
    public function index(Request $request)
    {
        $query = Tryout::query()
            // Avoid N+1 for blueprint usage in view
            ->with(['blueprints' => function ($q) {
                $q->select('tryout_id', 'kategori_id', 'jumlah');
            }, 'blueprints.kategori:id,kode,nama']);

        // Apply filters mirroring Soal index, adapted to Tryout fields
        if ($request->filled('jenis')) {
            $query->byJenisPaket($request->get('jenis'));
        }

        if ($request->filled('akses')) {
            $query->byPaket($request->get('akses'));
        }

        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        if ($request->filled('q')) {
            $query->searchTitle($request->get('q'));
        }

        $tryouts = $query->paginate(20)->withQueryString();

        return view('admin.tryout.index', compact('tryouts'));
    }
    public function create()
    {
        $kategoris = KategoriSoal::active()->get();

        // Hanya ambil soal yang belum dipakai
        foreach ($kategoris as $kategori) {
            $kategori->available_soals = $kategori->soals()->where('is_used', false)->get();
        }

        $packageMappings = PackageCategoryMapping::getAllMappings();
        $difficultyLevels = $this->getDifficultyLevels();
        return view('admin.tryout.create', compact('kategoris', 'packageMappings', 'difficultyLevels'));
    }


    public function store(Request $request)
    {
        // Build dynamic validation rules for difficulty levels
        $validationRules = [
            'judul' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'jenis_paket' => 'required|string',
            'blueprint' => 'required|array',
            'blueprint.*' => 'required|array',
        ];
        
        // Add validation rules for each difficulty level
        foreach ($this->getDifficultyLevels() as $level) {
            $validationRules["blueprint.*.{$level}"] = 'nullable|integer|min:0';
        }
        
        $request->validate($validationRules);

        $this->validateBlueprint($request->blueprint);

        // Derive akses_paket from jenis_paket dynamically using AccessTier mapping
        $derivedAccess = $this->getMinimumAccessForJenis($request->jenis_paket);

        $tryout = Tryout::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'struktur' => [],
            'shuffle_questions' => (bool) $request->get('shuffle_questions', false),
            'durasi_menit' => $request->durasi_menit,
            'akses_paket' => $derivedAccess,
            'jenis_paket' => $request->jenis_paket,
        ]);

        $rows = [];
        foreach ($request->blueprint as $kategoriId => $levels) {
            foreach ($this->getDifficultyLevels() as $level) {
                $jumlah = intval($levels[$level] ?? 0);
                if ($jumlah > 0) {
                    $soals = Soal::where('kategori_id', $kategoriId)
                        ->where('level', $level)
                        ->where('is_used', false)
                        ->limit($jumlah)
                        ->get();

                    // Tandai soal sudah dipakai dengan validasi
                    if ($soals->count() < $jumlah) {
                        throw new \Exception("Tidak cukup soal tersedia untuk kategori {$kategoriId} level {$level}. Tersedia: {$soals->count()}, Dibutuhkan: {$jumlah}");
                    }
                    
                    foreach ($soals as $soal) {
                        // Double check soal belum dipakai sebelum update
                        if (!$soal->is_used) {
                            $soal->update(['is_used' => true]);
                        } else {
                            \Log::warning("Soal ID {$soal->id} sudah ditandai sebagai used, melewati update");
                        }
                    }

                    $rows[] = [
                        'tryout_id' => $tryout->id,
                        'kategori_id' => $kategoriId,
                        'level' => $level,
                        'jumlah' => $jumlah,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        if (!empty($rows)) {
            \DB::transaction(function () use ($tryout, $rows) {
                // Delete existing blueprints
                \DB::statement('DELETE FROM tryout_blueprints WHERE tryout_id = ?', [$tryout->id]);
                
                // Insert new blueprints in bulk
                \DB::table('tryout_blueprints')->insert($rows);
            });
        }

        return redirect()->route('admin.tryout.index')
            ->with('success', 'Tryout berhasil dibuat');
    }


    public function show(Tryout $tryout)
    {
        $tryout->load(['userTryoutSoal.soal.kategori', 'blueprints']);

        // Statistik Penggunaan
        $totalPeserta = $tryout->userTryoutSoal()->distinct('user_id')->count();
        $totalAttempts = UserTryoutSession::where('tryout_id', $tryout->id)->count();
        $completedSessions = UserTryoutSession::where('tryout_id', $tryout->id)
            ->where('status', 'completed')
            ->count();
        $activeSessions = UserTryoutSession::where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->count();

        // Hitung rata-rata skor
        $averageScore = 0;
        if ($totalPeserta > 0) {
            $scores = DB::table('user_tryout_soal')
                ->select('user_id', DB::raw('SUM(skor) as total_score'))
                ->where('tryout_id', $tryout->id)
                ->groupBy('user_id')
                ->pluck('total_score');
            $averageScore = $scores->avg();
        }

        // Struktur soal dengan informasi kategori (fallback lama)
        $strukturSoal = [];
        $totalSoal = 0;

        if ($tryout->blueprints && $tryout->blueprints->count() > 0) {
            // Hitung total dari blueprint
            $grouped = $tryout->blueprints->groupBy('kategori_id');
            foreach ($grouped as $kategoriId => $rows) {
                $jumlah = $rows->sum('jumlah');
                if ($jumlah > 0) {
                    $kategori = KategoriSoal::find($kategoriId);
                    $soalTersedia = $kategori ? $kategori->soals()->count() : 0;
                    $strukturSoal[] = [
                        'kategori_id' => $kategoriId,
                        'kategori' => $kategori,
                        'jumlah' => $jumlah,
                        'soal_tersedia' => $soalTersedia,
                        'persentase' => 0
                    ];
                    $totalSoal += $jumlah;
                }
            }
            // Set persentase
            foreach ($strukturSoal as &$item) {
                $item['persentase'] = $totalSoal > 0 ? round(($item['jumlah'] / $totalSoal) * 100, 1) : 0;
            }
        } else {
            $totalSoal = array_sum($tryout->struktur ?? []);

            foreach ($tryout->struktur ?? [] as $kategoriId => $jumlah) {
                if ($jumlah > 0) {
                    $kategori = KategoriSoal::find($kategoriId);
                    $soalTersedia = $kategori ? $kategori->soals()->count() : 0;
                    $persentase = $totalSoal > 0 ? round(($jumlah / $totalSoal) * 100, 1) : 0;

                    $strukturSoal[] = [
                        'kategori_id' => $kategoriId,
                        'kategori' => $kategori,
                        'jumlah' => $jumlah,
                        'soal_tersedia' => $soalTersedia,
                        'persentase' => $persentase
                    ];
                }
            }
        }

        // Peserta terbaru
        $recentParticipants = [];
        if ($totalPeserta > 0) {
            $recentSessions = UserTryoutSession::with('user')
                ->where('tryout_id', $tryout->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($recentSessions as $session) {
                $userScore = UserTryoutSoal::where('user_id', $session->user_id)
                    ->where('tryout_id', $tryout->id)
                    ->sum('skor');

                $recentParticipants[] = [
                    'session' => $session,
                    'score' => $userScore
                ];
            }
        }

        // Tingkat penyelesaian
        $completionRate = $totalAttempts > 0 ?
            number_format(($completedSessions / $totalAttempts) * 100, 1) : 0;

        return view('admin.tryout.show', compact(
            'tryout',
            'totalPeserta',
            'totalAttempts',
            'completedSessions',
            'activeSessions',
            'averageScore',
            'strukturSoal',
            'totalSoal',
            'recentParticipants',
            'completionRate'
        ));
    }

    public function edit(Tryout $tryout)
    {
        $kategoris = KategoriSoal::active()->get();
        $packageMappings = PackageCategoryMapping::getAllMappings();
        return view('admin.tryout.edit', compact('tryout', 'kategoris', 'packageMappings'));
    }

    public function update(Request $request, Tryout $tryout)
    {
        try {
            // Build dynamic validation rules for difficulty levels
            $validationRules = [
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'durasi_menit' => 'required|integer|min:1',
                'jenis_paket' => 'required|string',
                'blueprint' => 'required|array',
                'blueprint.*' => 'required|array',
            ];
            
            // Add validation rules for each difficulty level
            foreach ($this->getDifficultyLevels() as $level) {
                $validationRules["blueprint.*.{$level}"] = 'nullable|integer|min:0';
            }
            
            $request->validate($validationRules);

            // Validasi jumlah soal tidak melebihi yang tersedia
            $this->validateBlueprint($request->blueprint, $tryout);

            // Start database transaction
            \DB::beginTransaction();

            // Kembalikan soal lama ke status is_used = false
            $oldBlueprints = $tryout->blueprints;
            foreach ($oldBlueprints as $blueprint) {
                \DB::table('soals')
                    ->where('kategori_id', $blueprint->kategori_id)
                    ->where('level', $blueprint->level)
                    ->where('is_used', true)
                    ->limit($blueprint->jumlah)
                    ->update(['is_used' => false]);
            }

            // Derive akses_paket from jenis_paket dynamically
            $derivedAccess = $this->getMinimumAccessForJenis($request->jenis_paket);

            // Update tryout data
            $tryout->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'struktur' => [],
                'shuffle_questions' => $request->boolean('shuffle_questions', false),
                'durasi_menit' => $request->durasi_menit,
                'akses_paket' => $derivedAccess,
                'jenis_paket' => $request->jenis_paket
            ]);

            // Delete old blueprints and create new ones in same transaction
            \DB::statement('DELETE FROM tryout_blueprints WHERE tryout_id = ?', [$tryout->id]);
            
            // Create new blueprints
            $blueprintRows = [];
            foreach ($request->blueprint as $kategoriId => $levels) {
                foreach ($this->getDifficultyLevels() as $level) {
                    $jumlah = intval($levels[$level] ?? 0);
                    if ($jumlah > 0) {
                        // Ambil soal yang belum dipakai dan tandai sebagai dipakai
                        $soalIds = \DB::table('soals')
                            ->where('kategori_id', $kategoriId)
                            ->where('level', $level)
                            ->where('is_used', false)
                            ->limit($jumlah)
                            ->pluck('id')
                            ->toArray();

                        // Tandai soal sudah dipakai
                        if (!empty($soalIds)) {
                            \DB::table('soals')
                                ->whereIn('id', $soalIds)
                                ->update(['is_used' => true]);
                        }

                        $blueprintRows[] = [
                            'tryout_id' => $tryout->id,
                            'kategori_id' => $kategoriId,
                            'level' => $level,
                            'jumlah' => $jumlah,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            if (!empty($blueprintRows)) {
                // Insert new blueprints in bulk
                \DB::table('tryout_blueprints')->insert($blueprintRows);
            }

            // Delete user answers (as warned in your blade template)
            \DB::table('user_tryout_soal')->where('tryout_id', $tryout->id)->delete();

            \DB::commit();

            return redirect()->route('admin.tryout.index')
                ->with('success', 'Tryout berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating tryout: ' . $e->getMessage(), [
                'tryout_id' => $tryout->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Tryout $tryout)
    {
        try {
            \DB::beginTransaction();
            
            // Kembalikan soal ke status is_used = false sebelum menghapus tryout
            $blueprints = $tryout->blueprints;
            $resetCount = 0;
            
            foreach ($blueprints as $blueprint) {
                // Reset soal berdasarkan blueprint yang ada
                $resetCount += \DB::table('soals')
                    ->where('kategori_id', $blueprint->kategori_id)
                    ->where('level', $blueprint->level)
                    ->where('is_used', true)
                    ->limit($blueprint->jumlah)
                    ->update(['is_used' => false]);
            }
            
            // Log untuk monitoring
            \Log::info("Tryout {$tryout->id} deleted, reset {$resetCount} questions to unused");
            
            // Hapus tryout (akan otomatis menghapus blueprints jika ada cascade)
            $tryout->delete();
            
            \DB::commit();
            
            return redirect()->route('admin.tryout.index')
                ->with('success', "Tryout berhasil dihapus. {$resetCount} soal dikembalikan ke status tersedia.");
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting tryout: ' . $e->getMessage(), [
                'tryout_id' => $tryout->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.tryout.index')
                ->with('error', 'Terjadi kesalahan saat menghapus tryout: ' . $e->getMessage());
        }
    }

    public function userIndex(Request $request)
    {

        $user = auth()->user();
        if (!$user->canAccessTryout()) {
            return redirect()->route('user.profile', ['userId' => $user->id])
                ->with('subscriptionError', 'Anda tidak memiliki akses ke Tryout CBT.');
        }

        // Optional filter by tryout type (kecerdasan/kepribadian/lengkap/free)
        $type = $request->get('type');

        // Build base query with dynamic allowed types
        $query = Tryout::active()->with('blueprints')->forUserPackage($user->paket_akses);

        // Apply optional type filter unconditionally (empty result is acceptable if not allowed)
        if (!empty($type)) {
            $query->byJenisPaket($type);
        }

        // Enforce FREE user quota: max 1 tryout per jenis (kecerdasan, kepribadian, lengkap)
        if ($user->paket_akses === 'free') {
            $all = $query->get();
            $grouped = $all->groupBy('jenis_paket');
            $limited = collect();
            foreach (['kecerdasan', 'kepribadian', 'lengkap'] as $jenis) {
                if ($grouped->has($jenis)) {
                    $limited->push($grouped[$jenis]->first());
                }
            }
            $tryouts = $limited->values();
            // If a type filter is provided, enforce it on the limited collection for FREE users
            if (!empty($type)) {
                $tryouts = $tryouts->where('jenis_paket', $type)->values();
            }
            // Convert to paginated collection for FREE users
            $perPage = 9;
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $items = $tryouts->slice($offset, $perPage)->values();
            $total = $tryouts->count();
            
            $tryouts = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'pageName' => 'page',
                ]
            );
            $tryouts->appends($request->query());
        } else {
            // For paid packages, use pagination with 9 items per page
            $tryouts = $query->paginate(9);
            $tryouts->appends($request->query());
        }


        $userId = auth()->id();
        $latestScores = [];
        $finalScores = [];

        foreach ($tryouts as $tryout) {
            $bahasa_inggris = \App\Models\HasilTes::where('user_id', $userId)
                ->where('jenis_tes', 'bahasa_inggris')
                ->orderBy('tanggal_tes', 'desc')
                ->value('skor_akhir');

            $pu = \App\Models\HasilTes::where('user_id', $userId)
                ->where('jenis_tes', 'pu')
                ->orderBy('tanggal_tes', 'desc')
                ->value('skor_akhir');

            $twk = \App\Models\HasilTes::where('user_id', $userId)
                ->where('jenis_tes', 'twk')
                ->orderBy('tanggal_tes', 'desc')
                ->value('skor_akhir');

            $numerik = \App\Models\HasilTes::where('user_id', $userId)
                ->where('jenis_tes', 'numerik')
                ->orderBy('tanggal_tes', 'desc')
                ->value('skor_akhir');

            $latestScores[$tryout->id] = [
                'bahasa_inggris' => $bahasa_inggris,
                'pu' => $pu,
                'twk' => $twk,
                'numerik' => $numerik,
            ];

            if ($bahasa_inggris !== null && $pu !== null && $twk !== null && $numerik !== null) {
                $scoringService = app(\App\Services\ScoringService::class);
                $finalScores[$tryout->id] = $scoringService->calculateFinalScore(
                    (float) $bahasa_inggris,
                    (float) $pu,
                    (float) $twk,
                    (float) $numerik
                );
            } else {
                $finalScores[$tryout->id] = null;
            }
        }

        return view('user.tryout.index', [
            'user' => $user,
            'tryouts' => $tryouts,
            'availableMenus' => $user->getAvailableMenus(),
            'latestScores' => $latestScores,
            'finalScores' => $finalScores,
        ]);
    }

    public function start(Tryout $tryout, Request $request)
    {
        $user = auth()->user();

        // Check if tryout is active
        if (!$tryout->is_active) {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Tryout "' . $tryout->judul . '" sedang tidak tersedia saat ini. Silakan coba lagi nanti.');
        }

        // Card ID functionality removed

        // Optional guard: enforce type from query if provided
        $requestedType = $request->get('type');
        if (!empty($requestedType) && $requestedType !== $tryout->jenis_paket) {
            return redirect()->route('user.tryout.index', ['type' => $requestedType])
                ->with('error', 'Tryout tidak sesuai dengan jenis yang dipilih.');
        }

        // Check if user can access this tryout
        if (!$this->canAccessTryout($user, $tryout)) {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Anda tidak memiliki akses ke tryout ini');
        }

        // Check if this is a restart request
        $restart = $request->get('restart', false);

        // Check existing session
        $existingSession = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        // If restart is requested or no active session, create new session
        if ($restart || !$existingSession) {
            // Jangan hapus attempt sebelumnya. Cukup tandai session lama sebagai abandoned
            if ($existingSession) {
                $existingSession->update(['status' => 'abandoned']);
            }

            // Create session baru
            $newSession = UserTryoutSession::create([
                'user_id' => $user->id,
                'tryout_id' => $tryout->id,
                'started_at' => now(),
                'status' => 'active',
                'shuffle_seed' => rand(1, 999999)
            ]);

            // Generate questions untuk session baru
            $this->generateQuestionsForUser($user, $tryout, $newSession->shuffle_seed, $newSession->id);
        }


        return redirect()->route('user.tryout.work', [
            'tryout' => $tryout
        ]);
    }

    public function getRemainingTime(Tryout $tryout)
    {
        $user = auth()->user();

        // Get active session
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak aktif'
            ]);
        }

        // Calculate remaining time
        $startTime = Carbon::parse($session->started_at);
        $currentTime = now();
        $elapsedMinutes = $startTime->diffInMinutes($currentTime);
        $remainingMinutes = max(0, $tryout->durasi_menit - $elapsedMinutes);
        $remainingSeconds = $remainingMinutes * 60;

        // Get more precise remaining time in seconds
        $elapsedSeconds = $startTime->diffInSeconds($currentTime);
        $totalSeconds = $tryout->durasi_menit * 60;
        $preciseRemainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        return response()->json([
            'success' => true,
            'remainingTime' => $preciseRemainingSeconds,
            'elapsedTime' => $elapsedSeconds,
            'totalTime' => $totalSeconds
        ]);
    }

    public function work(Tryout $tryout, Request $request)
    {
        $user = auth()->user();

        // Check if tryout is active
        if (!$tryout->is_active) {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Tryout "' . $tryout->judul . '" sedang tidak tersedia saat ini. Silakan coba lagi nanti.');
        }

        // Optional guard: if type provided and mismatch, redirect
        $requestedType = $request->get('type');
        if (!empty($requestedType) && $requestedType !== $tryout->jenis_paket) {
            return redirect()->route('user.tryout.index', ['type' => $requestedType])
                ->with('error', 'Tryout tidak sesuai dengan jenis yang dipilih.');
        }

        $questionNumber = $request->get('question', 1);
        $kategoriFilterId = $request->get('kategori_id');

        // Get or create active session
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return redirect()->route('user.tryout.start', $tryout)
                ->with('error', 'Sesi tryout tidak ditemukan. Silakan mulai tryout kembali.');
        }

        // Get user questions
        $baseQuery = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('user_tryout_session_id', $session->id)
            ->with(['soal.opsi', 'soal.kategori']);

        // Build per-category counts on full set (before filter)
        $allForCount = (clone $baseQuery)->with('soal')->get();
        $categoryCounts = $allForCount->groupBy(function ($row) {
            return $row->soal->kategori_id;
        })->map->count();

        if (!empty($kategoriFilterId)) {
            $baseQuery->whereHas('soal', function ($q) use ($kategoriFilterId) {
                $q->where('kategori_id', $kategoriFilterId);
            });
        }

        $userSoals = $baseQuery->orderBy('urutan')->get();

        // If no questions found, generate them
        if ($userSoals->isEmpty()) {
            $this->generateQuestionsForUser($user, $tryout, $session->shuffle_seed, $session->id);

            $userSoals = UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->where('user_tryout_session_id', $session->id)
                ->with(['soal.opsi', 'soal.kategori'])
                ->orderBy('urutan')
                ->get();
        }

        if ($userSoals->isEmpty()) {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Gagal menggenerate soal. Silakan coba lagi.');
        }

        $currentQuestion = $userSoals->where('urutan', $questionNumber)->first();
        if (!$currentQuestion) {
            $currentQuestion = $userSoals->first();
        }

        // PERUBAHAN: Shuffle options dengan session seed
        $currentQuestion = $this->shuffleQuestionOptions($currentQuestion, $session->shuffle_seed);

        $totalQuestions = $userSoals->count();

        // Calculate remaining time
        $startTime = Carbon::parse($session->started_at);
        $currentTime = now();
        $elapsedSeconds = $startTime->diffInSeconds($currentTime);
        $totalSeconds = $tryout->durasi_menit * 60;
        $timeLeft = max(0, $totalSeconds - $elapsedSeconds);

        // If time is up, redirect to finish
        if ($timeLeft <= 0) {
            $session->update([
                'status' => 'completed',
                'finished_at' => now()
            ]);

            return redirect()->route('user.tryout.finish', [
                'tryout' => $tryout->id
            ]);
        }

        return view('user.tryout.work', compact(
            'tryout',
            'userSoals',
            'currentQuestion',
            'totalQuestions',
            'timeLeft',
            'session',
            'categoryCounts',
            'kategoriFilterId'
        ));
    }

    public function resetAnswer(Request $request, Tryout $tryout)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
        ]);

        $userSoal = UserTryoutSoal::where('user_id', auth()->id())
            ->where('tryout_id', $tryout->id)
            ->where('soal_id', $request->soal_id)
            ->first();

        if ($userSoal) {
            $userSoal->update([
                'jawaban_user' => null,
                'sudah_dijawab' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil direset'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Soal tidak ditemukan'
        ], 404);
    }

    public function submitAnswer(Request $request, Tryout $tryout)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'jawaban' => 'required'
        ]);

        $user = auth()->user();

        // Card ID functionality removed

        // Check if tryout is active
        if (!$tryout->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Tryout sedang tidak tersedia saat ini. Jawaban tidak dapat disimpan.'
            ]);
        }

        // Check if session is still active
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tryout tidak aktif. Silakan refresh halaman dan coba lagi.'
            ]);
        }

        // Card ID functionality removed

        // Check if time is still available
        $startTime = Carbon::parse($session->started_at);
        $elapsedMinutes = $startTime->diffInMinutes(now());
        if ($elapsedMinutes >= $tryout->durasi_menit) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu tryout telah habis'
            ]);
        }

        $userSoal = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('user_tryout_session_id', $session->id)
            ->where('soal_id', $request->soal_id)
            ->first();

        if (!$userSoal) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan']);
        }

        // Handle different input types
        $jawabanArray = $request->jawaban;
        if (is_string($jawabanArray)) {
            $decoded = json_decode($jawabanArray, true);
            $jawabanArray = $decoded ?: [$jawabanArray];
        }
        if (!is_array($jawabanArray)) {
            $jawabanArray = [$jawabanArray];
        }

        // Backend guard: enforce max selections for multi-correct (e.g., pg_pilih_2)
        $soalForValidation = $userSoal->soal;
        if ($soalForValidation && $soalForValidation->tipe === 'pg_pilih_2') {
            if (count($jawabanArray) > 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 2 jawaban boleh dipilih untuk soal ini.'
                ], 422);
            }
            if (count($jawabanArray) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harus memilih tepat 2 jawaban untuk soal ini.'
                ], 422);
            }
        }

        $soal = $userSoal->soal;

        // SPECIAL HANDLING FOR BENAR/SALAH QUESTIONS
        if ($soal->tipe === 'benar_salah') {
            // For benar/salah, compare directly without conversion
            $correctAnswerOriginal = $soal->jawaban_benar;
            if (!is_array($correctAnswerOriginal)) {
                $correctAnswerOriginal = [$correctAnswerOriginal];
            }

            // Convert correct answer letters to text for comparison
            $correctAnswerText = [];
            $letters = ['A', 'B', 'C', 'D', 'E'];

            foreach ($correctAnswerOriginal as $correctLetter) {
                $letterIndex = array_search(strtoupper($correctLetter), $letters);
                if ($letterIndex !== false && isset($soal->opsi[$letterIndex])) {
                    $option = $soal->opsi[$letterIndex];
                    $optionText = is_array($option) ? $option['teks'] : $option->teks;
                    $correctAnswerText[] = $optionText;
                }
            }

            // Normalize both answers for comparison
            $correctAnswerNormalized = array_map('strtoupper', array_map('trim', $correctAnswerText));
            $userAnswerNormalized = array_map('strtoupper', array_map('trim', $jawabanArray));

            // Calculate score for benar/salah
            $isCorrect = $correctAnswerNormalized === $userAnswerNormalized;
            $skor = $isCorrect ? 1 : 0; // or use the weight from the correct option

            // Convert user answer text to letters for original jawaban (for consistency)
            $originalJawaban = [];
            foreach ($jawabanArray as $answer) {
                foreach ($soal->opsi as $index => $option) {
                    $optionText = is_array($option) ? $option['teks'] : $option->teks;
                    if (strtoupper(trim($optionText)) === strtoupper(trim($answer))) {
                        $originalJawaban[] = $letters[$index];
                        break;
                    }
                }
            }

            // Log for debugging
            \Log::info('Benar/Salah Scoring', [
                'correct_letters' => $correctAnswerOriginal,
                'correct_text' => $correctAnswerText,
                'correct_normalized' => $correctAnswerNormalized,
                'user_raw' => $jawabanArray,
                'user_normalized' => $userAnswerNormalized,
                'is_correct' => $isCorrect,
                'score' => $skor,
                'original_jawaban' => $originalJawaban
            ]);
        } else {
            // NORMAL HANDLING FOR OTHER QUESTION TYPES
            // Convert shuffled answer back to original using session seed
            $originalJawaban = $this->convertShuffledAnswerToOriginal($jawabanArray, $userSoal, $session->shuffle_seed);
            $skor = $this->calculateScore($soal, $originalJawaban);
        }

        $userSoal->update([
            'jawaban_user' => $jawabanArray, // Simpan jawaban yang sudah diacak untuk display
            'jawaban_original' => $originalJawaban, // Simpan jawaban asli untuk scoring
            'skor' => $skor,
            'sudah_dijawab' => true,
            'answered_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'skor' => $skor
        ]);
    }

    public function toggleMark(Request $request, Tryout $tryout)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'is_marked' => 'nullable|boolean'
        ]);

        $user = auth()->user();

        $userSoal = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('soal_id', $request->soal_id)
            ->first();

        if (!$userSoal) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan'], 404);
        }

        $current = (bool)$userSoal->is_marked;
        $newState = $request->has('is_marked') ? (bool)$request->boolean('is_marked') : !$current;

        $userSoal->update(['is_marked' => $newState]);

        return response()->json([
            'success' => true,
            'is_marked' => $newState
        ]);
    }


    // Metode ini tetap sama
    private function createOptionMapping($originalOptions, $shuffledOptions)
    {
        $mapping = [];
        $letters = ['A', 'B', 'C', 'D', 'E'];

        foreach ($shuffledOptions as $index => $shuffledOption) {
            // Cari posisi asli dari opsi ini
            $originalIndex = array_search($shuffledOption['opsi'], array_column($originalOptions, 'opsi'));
            $originalLetter = $letters[$originalIndex];
            $newLetter = $letters[$index];

            $mapping[$newLetter] = $originalLetter;
        }

        return $mapping;
    }

    // PERUBAHAN: Convert jawaban yang diacak kembali ke jawaban asli dengan session seed
    private function convertShuffledAnswerToOriginal($shuffledAnswers, $userSoal, $sessionSeed)
    {
        // Skip untuk soal benar/salah
        if ($userSoal->soal->tipe == 'benar_salah') {
            return $shuffledAnswers;
        }

        // PENTING: Gunakan logic yang SAMA dengan getConsistentShuffledOptions
        $seed = intval(crc32($sessionSeed . '_' . $userSoal->soal_id));

        if ($seed < 0) {
            $seed = abs($seed);
        }

        mt_srand($seed);

        $originalOptions = $userSoal->soal->opsi->toArray();
        $shuffledOptions = $originalOptions;
        shuffle($shuffledOptions);

        $mapping = $this->createOptionMapping($originalOptions, $shuffledOptions);

        // Convert shuffled answers to original
        $originalAnswers = [];
        foreach ($shuffledAnswers as $shuffledAnswer) {
            if (isset($mapping[$shuffledAnswer])) {
                $originalAnswers[] = $mapping[$shuffledAnswer];
            } else {
                $originalAnswers[] = $shuffledAnswer; // fallback
            }
        }

        return $originalAnswers;
    }

    public function debugSessionSeed(Tryout $tryout)
    {
        $user = auth()->user();

        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $userAnswers = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->with('soal')
            ->limit(3)
            ->get();

        $debugInfo = [];

        foreach ($userAnswers as $userAnswer) {
            $sessionSeedFromSession = $session ? $session->shuffle_seed : 'N/A';
            $sessionSeedFromAnswer = $userAnswer->session_seed ?? 'N/A';

            // Test shuffling dengan kedua seed
            $shuffleResult1 = $this->getConsistentShuffledOptions($userAnswer->soal, $sessionSeedFromSession);
            $shuffleResult2 = $this->getConsistentShuffledOptions($userAnswer->soal, $sessionSeedFromAnswer);

            $debugInfo[] = [
                'soal_id' => $userAnswer->soal_id,
                'session_seed_from_session' => $sessionSeedFromSession,
                'session_seed_from_answer' => $sessionSeedFromAnswer,
                'shuffle_same' => $shuffleResult1['options']->pluck('teks')->toArray() === $shuffleResult2['options']->pluck('teks')->toArray(),
                'options_session' => $shuffleResult1['options']->pluck('teks')->toArray(),
                'options_answer' => $shuffleResult2['options']->pluck('teks')->toArray(),
            ];
        }

        return response()->json($debugInfo);
    }

    // PERUBAHAN: Get shuffled options untuk display dengan session seed
    public function getShuffledOptions($soal, $sessionSeed)
    {
        if ($soal->tipe == 'benar_salah') {
            return $soal->opsi;
        }

        // PERUBAHAN: Generate seed yang konsisten dengan session seed
        $seed = crc32($sessionSeed . '_' . $soal->id);
        mt_srand($seed);

        // Shuffle options
        $options = $soal->opsi->toArray();
        shuffle($options);

        return collect($options);
    }

    private function getConsistentShuffledOptions($soal, $sessionSeed)
    {
        // Skip untuk soal benar/salah
        if ($soal->tipe == 'benar_salah') {
            return [
                'options' => $soal->opsi,
                'mapping' => [],
                'originalToShuffle' => []
            ];
        }

        // Generate seed yang konsisten
        $seed = crc32($sessionSeed . '_' . $soal->id);
        mt_srand($seed);

        // Get original options
        $originalOptions = $soal->opsi->toArray();

        // Shuffle dengan seed yang sama
        $shuffledOptions = $originalOptions;
        shuffle($shuffledOptions);

        // Create mapping
        $letters = ['A', 'B', 'C', 'D', 'E'];
        $shuffleToOriginalMapping = [];
        $originalToShuffleMapping = [];

        foreach ($shuffledOptions as $shuffleIndex => $shuffledOption) {
            // Cari index asli dari opsi ini
            foreach ($originalOptions as $origIndex => $origOption) {
                $shuffledText = is_array($shuffledOption) ? $shuffledOption['teks'] : $shuffledOption->teks;
                $originalText = is_array($origOption) ? $origOption['teks'] : $origOption->teks;

                if ($shuffledText === $originalText) {
                    $originalLetter = $letters[$origIndex];
                    $shuffledLetter = $letters[$shuffleIndex];

                    $shuffleToOriginalMapping[$shuffledLetter] = $originalLetter;
                    $originalToShuffleMapping[$originalLetter] = $shuffledLetter;
                    break;
                }
            }
        }

        return [
            'options' => collect($shuffledOptions),
            'shuffleToOriginal' => $shuffleToOriginalMapping,
            'originalToShuffle' => $originalToShuffleMapping
        ];
    }

    public function getQuestionReviewData($userAnswer)
    {
        $sessionSeed = $userAnswer->session_seed ?? auth()->id();

        // Get consistent shuffled options
        $shuffleData = $this->getConsistentShuffledOptions($userAnswer->soal, $sessionSeed);

        // Convert jawaban benar original ke shuffled
        $correctAnswerOriginal = $userAnswer->soal->jawaban_benar;
        if (!is_array($correctAnswerOriginal)) {
            $correctAnswerOriginal = [$correctAnswerOriginal];
        }

        // Jawaban user sudah dalam format shuffled (dari jawaban_user)
        $userAnswerShuffled = $userAnswer->jawaban_user ?? [];
        if (!is_array($userAnswerShuffled)) {
            $userAnswerShuffled = [$userAnswerShuffled];
        }

        // Khusus untuk soal benar/salah, konversi ke format yang sama
        if ($userAnswer->soal->tipe == 'benar_salah') {
            // Convert correct answer (letters) to option text
            $correctAnswerText = [];
            $letters = ['A', 'B', 'C', 'D', 'E'];

            foreach ($correctAnswerOriginal as $correctLetter) {
                $letterIndex = array_search(strtoupper($correctLetter), $letters);
                if ($letterIndex !== false && isset($userAnswer->soal->opsi[$letterIndex])) {
                    $option = $userAnswer->soal->opsi[$letterIndex];
                    $optionText = is_array($option) ? $option['teks'] : $option->teks;
                    $correctAnswerText[] = $optionText;
                }
            }

            $correctAnswerShuffled = $correctAnswerText;

            // Normalize untuk perbandingan (both are now text)
            $correctAnswerNormalized = array_map('strtoupper', array_map('trim', $correctAnswerShuffled));
            $userAnswerNormalized = array_map('strtoupper', array_map('trim', $userAnswerShuffled));

            // Debug
            \Log::info('Benar/Salah Comparison Fixed', [
                'correct_letters' => $correctAnswerOriginal,
                'correct_text' => $correctAnswerShuffled,
                'correct_normalized' => $correctAnswerNormalized,
                'user_raw' => $userAnswerShuffled,
                'user_normalized' => $userAnswerNormalized,
                'is_match' => $correctAnswerNormalized === $userAnswerNormalized
            ]);
        } else {
            $correctAnswerShuffled = $this->convertOriginalAnswerToShuffled(
                $correctAnswerOriginal,
                $shuffleData['originalToShuffle']
            );
        }

        // Cari opsi terbaik dari shuffled (bobot tertinggi)
        $shuffledOptionsCollection = collect($shuffleData['options']);
        $bestShuffledOption = $shuffledOptionsCollection->sortByDesc(function ($opt) {
            return is_array($opt) ? $opt['bobot'] : $opt->bobot;
        })->first();

        // Cari letter untuk opsi terbaik sesuai shuffle
        $letters = ['A', 'B', 'C', 'D', 'E'];
        $bestIndex = null;

        // Loop manual untuk mencari index yang tepat
        foreach ($shuffledOptionsCollection->values() as $index => $option) {
            $shuffledText = is_array($option) ? $option['teks'] : $option->teks;
            $shuffledBobot = is_array($option) ? $option['bobot'] : $option->bobot;

            $bestText = is_array($bestShuffledOption) ? $bestShuffledOption['teks'] : $bestShuffledOption->teks;
            $bestBobot = is_array($bestShuffledOption) ? $bestShuffledOption['bobot'] : $bestShuffledOption->bobot;

            // Cocokkan berdasarkan teks DAN bobot untuk memastikan tepat
            if ($shuffledText === $bestText && $shuffledBobot == $bestBobot) {
                $bestIndex = $index;
                break;
            }
        }

        // Fallback jika tidak ditemukan
        if ($bestIndex === null) {
            $bestIndex = 0; // Default ke A jika ada masalah
        }

        $bestOptionLetter = $letters[$bestIndex] ?? 'A';
        $bestOptionBobot = is_array($bestShuffledOption) ? $bestShuffledOption['bobot'] : $bestShuffledOption->bobot;

        return [
            'shuffledOptions' => $shuffleData['options'],
            'correctAnswerShuffled' => $correctAnswerShuffled,
            'userAnswerShuffled' => $userAnswerShuffled,
            'mapping' => $shuffleData,
            'bestOption' => [
                'letter' => $bestOptionLetter,
                'bobot'  => $bestOptionBobot,
            ]
        ];
    }

    public function finish(Tryout $tryout)
    {

        $user = auth()->user();

        // Check if tryout is active
        if (!$tryout->is_active) {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Tryout "' . $tryout->judul . '" sedang tidak tersedia saat ini.');
        }

        // Update session status
        $session = UserTryoutSession::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'active')
            ->first();

        if ($session) {
            $session->update([
                'status' => 'completed',
                'finished_at' => now()
            ]);
        }

        // Jika session tidak ditemukan, cari session yang sudah completed
        if (!$session) {
            $session = UserTryoutSession::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->where('status', 'completed')
                ->orderBy('finished_at', 'desc')
                ->first();
        }

        if (!$session) {
            return redirect()->route('user.tryout.index')->with('error', 'Session tryout tidak ditemukan');
        }

        $userAnswers = UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('user_tryout_session_id', $session->id)
            ->with(['soal.opsi', 'soal.kategori'])
            ->orderBy('urutan')
            ->get();

        if ($userAnswers->isEmpty()) {
            return redirect()->route('user.tryout.index')->with('error', 'Data tryout tidak ditemukan');
        }

        // PENTING: Pastikan semua userAnswer menggunakan session seed yang sama
        $sessionSeed = $session ? $session->shuffle_seed : $user->id;

        // Update semua user answers dengan session seed yang konsisten
        UserTryoutSoal::where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('user_tryout_session_id', $session->id)
            ->whereNull('session_seed')
            ->update(['session_seed' => $sessionSeed]);

        // Backfill session_id untuk jawaban yang mungkin belum terset
        if ($session) {
            UserTryoutSoal::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->whereNull('user_tryout_session_id')
                ->update(['user_tryout_session_id' => $session->id]);
        }

        // Refresh data setelah update
        $userAnswers = $userAnswers->fresh();

        // Generate review data dengan session seed yang konsisten
        $reviewData = [];
        foreach ($userAnswers as $userAnswer) {
            $reviewData[$userAnswer->id] = $this->getQuestionReviewData($userAnswer);
        }

        $totalScore = $userAnswers->sum('skor');
        $totalQuestions = $userAnswers->count();

        // TKP dihitung hanya untuk tryout kepribadian atau lengkap
        $tkpFinalScore = null;
        $tkpN = null; // jumlah soal TKP
        $tkpT = null; // total poin TKP (raw sum 1..5)
        // AKADEMIK scoring - semua menggunakan benar salah (0 atau 1)
        $akademikQuestions = collect();
        if (in_array($tryout->jenis_paket, ['bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap'], true)) {
            try {
                $akademikKategoriCodes = \App\Models\PackageCategoryMapping::getCategoriesForPackage($tryout->jenis_paket);
                $akademikQuestions = $userAnswers->filter(function ($ans) use ($akademikKategoriCodes) {
                    $kategori = $ans->soal->kategori ?? null;
                    return $kategori && in_array($kategori->kode, $akademikKategoriCodes);
                });

                if ($akademikQuestions->count() > 0) {
                    $totalQuestions = $akademikQuestions->count();
                    $correctAnswers = $akademikQuestions->where('skor', '>', 0)->count();
                    $akademikFinalScore = ($correctAnswers / $totalQuestions) * 100;

                    // Persist to session for quick retrieval
                    if ($session) {
                        $session->update(['akademik_final_score' => $akademikFinalScore]);
                    }
                }
            } catch (\Throwable $e) {
                // Fail-safe: ignore AKADEMIK score calculation errors to avoid blocking result page
            }
        }

        $correctAnswers = $userAnswers->where('skor', '>', 0)->count();
        $wrongAnswers = $totalQuestions - $correctAnswers;

        // Calculate category scores
        $categoryScores = [];
        $categoryGroups = $userAnswers->groupBy('soal.kategori_id');

        // Card ID functionality removed

        foreach ($categoryGroups as $kategoriId => $answers) {
            $kategori = $answers->first()->soal->kategori;
            $categoryScore = $answers->sum('skor');
            $categoryCorrect = $answers->where('skor', '>', 0)->count();
            $categoryTotal = $answers->count();

            $categoryScores[] = [
                'nama' => $kategori->nama,
                'score' => $categoryScore,
                'correct' => $categoryCorrect,
                'total' => $categoryTotal
            ];
        }

        // Persist TKP score to hasil_tes as a summarized record (no breakdown)
        if (!is_null($tkpFinalScore)) {
            try {
                $durationMinutes = $tryout->durasi_menit;
                $durationSeconds = $durationMinutes * 60;
                $tkpCount = $tkpQuestions->count();
                $averageTime = $tkpCount > 0 ? round($durationSeconds / $tkpCount, 2) : null;

                // Determine TKP score category
                $tkpKategoriSkor = $this->getTkpScoreCategory($tkpFinalScore);

                // Use updateOrCreate to prevent duplicates based on session ID
                \App\Models\HasilTes::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'jenis_tes' => 'kepribadian',
                        'detail_jawaban' => json_encode([
                            'N' => $tkpCount,
                            'T' => (int) round($tkpQuestions->sum('skor')),
                            'skor_tkp' => $tkpFinalScore,
                            'session_id' => $session->id,
                        ])
                    ],
                    [
                        'skor_benar' => 0,
                        'skor_salah' => 0,
                        'waktu_total' => $durationSeconds,
                        'average_time' => $averageTime,
                        'tkp_final_score' => $tkpFinalScore,
                        'skor_akhir' => $tkpFinalScore,
                        'kategori_skor' => $tkpKategoriSkor,
                        'tanggal_tes' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // ignore persistence errors
            }
        }

        // Persist intelligence test results to hasil_tes
        if ($tryout->jenis_paket === 'kecerdasan' || $tryout->jenis_paket === 'lengkap') {
            try {
                $durationMinutes = $tryout->durasi_menit;
                $durationSeconds = $durationMinutes * 60;
                $averageTime = $totalQuestions > 0 ? round($durationSeconds / $totalQuestions, 2) : null;

                // Calculate final score percentage
                $finalScore = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

                // Determine score category
                $kategoriSkor = $this->getScoreCategory($finalScore);

                // Use updateOrCreate to prevent duplicates based on session ID
                \App\Models\HasilTes::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'jenis_tes' => 'kecerdasan',
                        'detail_jawaban' => json_encode([
                            'total_questions' => $totalQuestions,
                            'correct_answers' => $correctAnswers,
                            'wrong_answers' => $wrongAnswers,
                            'total_score' => $totalScore,
                            'final_score' => $finalScore,
                            'category_scores' => $categoryScores,
                            'session_id' => $session->id,
                        ])
                    ],
                    [
                        'skor_benar' => $correctAnswers,
                        'skor_salah' => $wrongAnswers,
                        'waktu_total' => $durationSeconds,
                        'average_time' => $averageTime,
                        'skor_akhir' => $finalScore,
                        'kategori_skor' => $kategoriSkor,
                        'tanggal_tes' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // ignore persistence errors
            }
        }

        // Server-side per-question review support
        $requestedReview = request()->get('review');
        $currentReviewNumber = is_numeric($requestedReview) ? max(1, min((int)$requestedReview, $totalQuestions)) : 1;
        $currentReviewItem = $userAnswers->firstWhere('urutan', $currentReviewNumber) ?? $userAnswers->first();

        $isTkp = !is_null($tkpFinalScore) && in_array($tryout->jenis_paket, ['kepribadian', 'lengkap'], true);

        return view('user.tryout.result', compact(
            'tryout',
            'userAnswers',
            'reviewData',
            'totalScore',
            'totalQuestions',
            'correctAnswers',
            'wrongAnswers',
            'categoryScores',
            'currentReviewNumber',
            'currentReviewItem',
            'tkpFinalScore',
            'tkpN',
            'tkpT',
            'isTkp'
        ));
    }

    // PERUBAHAN: Helper method untuk generate shuffled options dengan session seed
    public function getShuffledOptionsForReview($soal, $sessionSeed)
    {
        if ($soal->tipe == 'benar_salah') {
            return $soal->opsi;
        }

        // Generate seed yang konsisten dengan session
        $seed = crc32($sessionSeed . '_' . $soal->id);
        mt_srand($seed);

        // Shuffle options
        $options = $soal->opsi->toArray();
        shuffle($options);

        return collect($options);
    }

    public function restart(Tryout $tryout)
    {
        // Check if tryout is active first
        if (!$tryout->is_active) {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Tryout "' . $tryout->judul . '" sedang tidak tersedia saat ini. Tidak dapat memulai ulang.');
        }

        return $this->start($tryout, request()->merge(['restart' => true]));
    }

    private function canAccessTryout($user, $tryout)
    {
        // Use new package system
        return $user->canAccessSpecificTryout($tryout);
    }

    /**
     * Get available difficulty levels from database schema
     */
    private function getDifficultyLevels()
    {
        // Get difficulty levels from database schema
        $levels = \DB::select("SHOW COLUMNS FROM soals LIKE 'level'");
        if (!empty($levels)) {
            $enumValues = $levels[0]->Type;
            // Extract enum values from string like "enum('dasar','mudah','sedang','sulit','tersulit','ekstrem')"
            preg_match_all("/'([^']+)'/", $enumValues, $matches);
            return $matches[1] ?? [];
        }
        
        // Fallback to default levels if schema query fails
        return ['dasar', 'mudah', 'sedang', 'sulit', 'tersulit', 'ekstrem'];
    }

    /**
     * Get minimum akses_paket required for a given jenis_paket
     * Uses dynamic mapping from access_tiers table
     */
    private function getMinimumAccessForJenis($jenisPaket)
    {
        // Map jenis_paket to akses_paket sesuai dengan enum yang ada di database
        switch ($jenisPaket) {
            case 'free':
                return 'free';
            case 'lengkap':
                return 'vip';
            default:
                return 'premium';
        }
    }

    /**
     * Validate blueprint against available questions
     */
    private function validateBlueprint($blueprint, $tryout = null)
    {
        $errors = [];
        $totalSoal = 0;
        $kategoriDenganSoal = 0;

        foreach ($blueprint as $kategoriId => $levels) {
            $kategori = KategoriSoal::find($kategoriId);
            if (!$kategori) continue;

            $totalSoalKategori = 0;
            $kategoriMemilikiSoal = false;

            foreach ($levels as $level => $jumlah) {
                $jumlah = (int) $jumlah;
                if ($jumlah <= 0) continue;

                $kategoriMemilikiSoal = true;
                $totalSoalKategori += $jumlah;
                $totalSoal += $jumlah;

                // Get available questions
                $available = $kategori->soals()->where('level', $level)->where('is_used', false)->where('is_active', true)->count();

                // If this is an update operation, add back the current blueprint questions
                if ($tryout) {
                    $currentUsed = $tryout->blueprints()
                        ->where('kategori_id', $kategoriId)
                        ->where('level', $level)
                        ->value('jumlah') ?? 0;
                    $available += $currentUsed;
                }

                if ($jumlah > $available) {
                    $levelText = ucfirst($level);
                    $errors[] = "Kategori {$kategori->nama} ({$kategori->kode}): Jumlah soal {$levelText} yang diminta ({$jumlah}) melebihi soal yang tersedia ({$available})";
                }
            }

            // Validasi: setiap kategori yang dipilih harus memiliki minimal 1 soal
            if ($totalSoalKategori > 0) {
                $kategoriDenganSoal++;
            }
        }

        // Validasi: minimal 1 soal total di seluruh tryout
        if ($totalSoal == 0) {
            $errors[] = "Tryout harus memiliki minimal 1 soal dari kategori manapun.";
        }

        // Validasi: minimal 1 kategori harus dipilih
        if ($kategoriDenganSoal == 0) {
            $errors[] = "Minimal 1 kategori harus dipilih dengan minimal 1 soal.";
        }

        // Fix: Use the correct way to throw ValidationException
        if (!empty($errors)) {
            // Method 1: Using withMessages (Recommended)
            throw \Illuminate\Validation\ValidationException::withMessages([
                'blueprint' => $errors
            ]);

            // Alternative Method 2: Using make validator
            // $validator = \Illuminate\Support\Facades\Validator::make([], []);
            // $validator->errors()->add('blueprint', implode(' ', $errors));
            // throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    // PERUBAHAN: Generate questions dengan session seed
    private function generateQuestionsForUser($user, $tryout, $sessionSeed = null, $sessionId = null)
    {
        $urutan = 1;
        $totalGenerated = 0;

        // Jika tidak ada session seed, generate yang baru
        if (!$sessionSeed) {
            $sessionSeed = rand(1, 999999);
        }

        DB::beginTransaction();

        try {
            // 1) Cari baseline: session pertama user untuk tryout ini yang punya set soal
            $baselineSession = UserTryoutSession::where('user_id', $user->id)
                ->where('tryout_id', $tryout->id)
                ->orderBy('id', 'asc')
                ->first();

            $baselineSet = collect();
            if ($baselineSession) {
                $baselineSet = UserTryoutSoal::where('user_id', $user->id)
                    ->where('tryout_id', $tryout->id)
                    ->where('user_tryout_session_id', $baselineSession->id)
                    ->orderBy('urutan')
                    ->get(['soal_id', 'level']);
            }

            // 2) Jika baseline kosong (belum pernah attempt), pilih set soal sesuai blueprint/struktur sebagai baseline
            if ($baselineSet->isEmpty()) {
                if ($tryout->relationLoaded('blueprints') || $tryout->blueprints()->exists()) {
                    $tryout->load('blueprints');
                    $selector = new QuestionSelector();
                    $selector->validateBlueprintAvailability($tryout);
                    $picked = $selector->pickByBlueprint($tryout);
                    $baselineSet = $picked->map(function ($soal) {
                        return (object)['soal_id' => $soal->id, 'level' => $soal->level];
                    });
                } else {
                    // Fallback: gunakan struktur lama per kategori
                    $temp = collect();
                    foreach ($tryout->struktur as $kategoriId => $jumlah) {
                        if ($jumlah > 0) {
                            $availableSoals = Soal::active()->byKategori($kategoriId)->count();
                            if ($availableSoals < $jumlah) {
                                throw new \Exception("Kategori ID {$kategoriId} tidak memiliki cukup soal aktif. Tersedia: {$availableSoals}, Dibutuhkan: {$jumlah}");
                            }
                            $soals = Soal::active()->byKategori($kategoriId)->inRandomOrder()->limit($jumlah)->get();
                            $soals->each(function ($s) use (&$temp) {
                                $temp->push((object)['soal_id' => $s->id, 'level' => $s->level ?? null]);
                            });
                        }
                    }
                    $baselineSet = $temp;
                }

                // Jika ini adalah attempt pertama (sessionId == baselineSession? tidak ada baselineSession), simpan baseline sesuai urutan sekarang
                // Insert langsung dengan urutan (mungkin di-shuffle tergantung setting)
                $ordered = $baselineSet;
            } else {
                // 3) Untuk attempt selanjutnya: gunakan set soal baseline yang sama
                $ordered = $baselineSet;
            }

            // 4) Acak urutan saja per attempt, deterministik dengan session seed
            $ordered = $ordered->values();
            $indices = range(0, $ordered->count() - 1);
            if ($ordered->count() > 0) {
                $seed = crc32($sessionSeed . '_' . $tryout->id . '_' . $user->id . '_order');
                mt_srand($seed);
                shuffle($indices);
            }

            foreach ($indices as $idx) {
                $row = $ordered[$idx];
                UserTryoutSoal::create([
                    'user_id' => $user->id,
                    'tryout_id' => $tryout->id,
                    'user_tryout_session_id' => $sessionId,
                    'soal_id' => $row->soal_id,
                    'level' => $row->level,
                    'urutan' => $urutan++,
                    'session_seed' => $sessionSeed,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $totalGenerated++;
            }

            if ($totalGenerated == 0) {
                throw new \Exception('Tidak ada soal yang berhasil digenerate');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error generating questions: ' . $e->getMessage());
            throw $e;
        }

        return $totalGenerated;
    }


    // Tambahkan method ini ke TryoutController

    /**
     * Generate shuffled options yang konsisten untuk soal tertentu
     */


    /**
     * Convert jawaban original ke shuffled untuk display
     */
    private function convertOriginalAnswerToShuffled($originalAnswers, $mapping)
    {
        if (!is_array($originalAnswers)) {
            $originalAnswers = [$originalAnswers];
        }

        $shuffledAnswers = [];
        foreach ($originalAnswers as $original) {
            $shuffledAnswers[] = $mapping[$original] ?? $original;
        }

        return $shuffledAnswers;
    }

    /**
     * Get review data untuk satu soal
     */


    /**
     * Update method shuffleQuestionOptions untuk konsistensi
     */
    private function shuffleQuestionOptions($userSoal, $sessionSeed)
    {
        // Skip untuk soal benar/salah
        if ($userSoal->soal->tipe == 'benar_salah') {
            return $userSoal;
        }

        // Gunakan method yang konsisten dengan SEED YANG SAMA
        $shuffleData = $this->getConsistentShuffledOptions($userSoal->soal, $sessionSeed);

        // Update soal object dengan opsi yang sudah diacak
        $userSoal->soal->shuffled_opsi = $shuffleData['options'];

        // Store mapping untuk conversion nanti
        $userSoal->option_mapping = $shuffleData['shuffleToOriginal'];

        return $userSoal;
    }


    private function calculateScore($soal, $jawaban)
    {
        // AKADEMIK scoring - semua menggunakan benar salah (0 atau 1)
        switch ($soal->tipe) {
            case 'benar_salah':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;

            case 'pg_satu':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;

            case 'gambar':
                return $jawaban[0] === $soal->jawaban_benar ? 1 : 0;

            case 'pg_bobot':
                // AKADEMIK: semua bobot di-cap di 1 (benar salah)
                $totalBobot = 0;
                foreach ($jawaban as $opsi) {
                    $opsiSoal = $soal->opsi()->where('opsi', $opsi)->first();
                    if ($opsiSoal) {
                        $totalBobot += $opsiSoal->bobot;
                    }
                }
                return min($totalBobot, 1); // Cap at 1 for 0-1 scale

            case 'pg_pilih_2':
                // Binary scoring: kedua jawaban harus benar untuk mendapat skor 1.0, selain itu 0.0
                if (count($jawaban) !== 2) return 0;

                $totalBobot = 0;
                foreach ($jawaban as $opsi) {
                    $opsiSoal = $soal->opsi()->where('opsi', $opsi)->first();
                    if ($opsiSoal) {
                        $totalBobot += floatval($opsiSoal->bobot);
                    }
                }

                // Hanya skor 1.0 jika total bobot = 1.0 (kedua jawaban benar), selain itu 0.0
                return $totalBobot == 1.0 ? 1.0 : 0.0;

            default:
                return 0;
        }
    }

    /**
     * Toggle status aktif/nonaktif tryout
     */
    public function toggleStatus(Request $request, Tryout $tryout)
    {
        try {
            $request->validate([
                'is_active' => 'required'
            ]);

            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($isActive === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status yang dipilih tidak valid. Silakan coba lagi.'
                ], 422);
            }

            $tryout->update([
                'is_active' => $isActive
            ]);

            $statusText = $tryout->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "Tryout \"{$tryout->judul}\" berhasil {$statusText}",
                'is_active' => $tryout->is_active
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang dikirim tidak valid. Silakan coba lagi.'
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Toggle Status Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status tryout. Silakan coba lagi atau hubungi administrator jika masalah berlanjut.'
            ], 500);
        }
    }

    /**
     * Show paket lengkap completion status and final score
     */
    public function paketLengkapStatus()
    {
        $user = auth()->user();

        if ($user->paket_akses !== 'lengkap') {
            return redirect()->route('user.tryout.index')
                ->with('error', 'Anda tidak memiliki paket lengkap.');
        }

        $service = app(\App\Services\PaketLengkapService::class);
        $status = $service->getCompletionStatus($user);
        $summary = $service->getDashboardSummary($user);

        return view('user.tryout.paket-lengkap-status', [
            'user' => $user,
            'status' => $status,
            'summary' => $summary
        ]);
    }

    private function getScoreCategory($score)
    {
        if ($score >= 90) return 'Sangat Tinggi';
        if ($score >= 81) return 'Tinggi';
        if ($score >= 71) return 'Baik';
        if ($score >= 61) return 'Cukup';
        if ($score >= 51) return 'Rendah';
        return 'Sangat Rendah';
    }

    private function getTkpScoreCategory($score)
    {
        if ($score >= 91) return 'Sangat Tinggi';
        if ($score >= 76) return 'Tinggi';
        if ($score >= 61) return 'Cukup Tinggi';
        if ($score >= 41) return 'Sedang';
        return 'Rendah';
    }
}
