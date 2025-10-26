<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\KategoriSoal;
use App\Models\OpsiSoal;
use App\Models\PackageCategoryMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class SoalController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'akademik');
        $query = Soal::with(['kategori', 'opsi']);

        // Apply kategori filter
        if ($request->filled('kategori')) {
            $query->byKategori($request->kategori);
        }

        // Apply tipe filter
        if ($request->filled('tipe')) {
            $query->byTipe($request->tipe);
        }

        // Apply level filter
        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        $soals = $query->paginate(20)->withQueryString();
        $kategoris = KategoriSoal::active()->get();
        $difficultyLevels = $this->getDifficultyLevels();

        return view('admin.soal.index', compact('soals', 'kategoris', 'difficultyLevels', 'type'));
    }

    public function create()
    {
        $kategoris = KategoriSoal::active()->get();
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar'];

        return view('admin.soal.create', compact('kategoris', 'tipes'));
    }

    public function getKepribadianCategories()
    {
        $kepribadianCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
        return response()->json($kepribadianCodes);
    }

    public function getAkademikCategories()
    {
        $akademikCodes = PackageCategoryMapping::getCategoriesForPackage('akademik');
        return response()->json($akademikCodes);
    }

    /**
     * Calculate bobot berdasarkan tipe soal
     */
    private function calculateBobot($tipe, $inputBobot, $kategoriId = null)
    {
        switch ($tipe) {
            case 'pg_pilih_2':
                // Untuk pilih 2, bobot = 0.5 untuk jawaban benar, 0 untuk salah
                return $inputBobot > 0 ? 0.5 : 0;
                
            case 'pg_bobot':
                // Untuk pg_bobot, gunakan bobot asli dari input (1-5 untuk kepribadian, 0-1 untuk lainnya)
                if ($kategoriId) {
                    $kategori = KategoriSoal::find($kategoriId);
                    if ($kategori) {
                        $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                        if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                            // Untuk kategori kepribadian, bobot 1-5
                            return max(1, min(5, $inputBobot));
                        }
                    }
                }
                // Untuk non-kepribadian, bobot 0-1
                return max(0, min(1, $inputBobot));
                
            case 'pg_satu':
            case 'gambar':
                // Untuk PG 1 jawaban dan gambar, bobot = 1 untuk benar, 0 untuk salah
                return $inputBobot > 0 ? 1 : 0;
                
            case 'benar_salah':
                // Untuk benar/salah, bobot = 1 untuk benar, 0 untuk salah
                return $inputBobot > 0 ? 1 : 0;
                
            default:
                return $inputBobot;
        }
    }

    public function store(Request $request)
    {
        
        $rules = [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2,gambar',
            'level' => 'required|in:dasar,mudah,sedang,sulit,tersulit,ekstrem',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'pembahasan_type' => 'nullable|in:text,image,both',
            'pembahasan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:5'
        ];

        // Add image validation if tipe is gambar
        if ($request->tipe === 'gambar') {
            $rules['gambar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        // Dynamic validation for kepribadian categories (TKP/PSIKOTES via package mapping)
        // PERBAIKAN: Validasi bobot 1-5 hanya untuk tipe pg_bobot dengan kategori kepribadian
        if ($request->filled('kategori_id') && $request->tipe === 'pg_bobot') {
            $kategori = KategoriSoal::find($request->kategori_id);
            if ($kategori) {
                $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                    $rules['opsi.*.bobot'] = 'required|integer|between:1,5';
                }
            }
        }

        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $e->getMessage());
        }

        try {
            DB::transaction(function () use ($request) {
                $soalData = [
                    'pertanyaan' => $request->pertanyaan,
                    'tipe' => $request->tipe,
                    'level' => $request->level,
                    'kategori_id' => $request->kategori_id,
                    'pembahasan' => $request->pembahasan,
                    'pembahasan_type' => $request->pembahasan_type ?? 'text',
                    'jawaban_benar' => $request->jawaban_benar
                ];

                // Handle image upload
                if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
                    $image = $request->file('gambar');
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('soal_images', $imageName, 'public');
                    $soalData['gambar'] = $imagePath;
                }

                // Pembahasan image upload
                if (in_array($request->pembahasan_type, ['image', 'both']) && $request->hasFile('pembahasan_image')) {
                    $img = $request->file('pembahasan_image');
                    $name = time() . '_' . $img->getClientOriginalName();
                    $path = $img->storeAs('pembahasan_images', $name, 'public');
                    $soalData['pembahasan_image'] = $path;
                }

                $soal = Soal::create($soalData);

                foreach ($request->opsi as $index => $opsiData) {
                    // Set bobot berdasarkan tipe soal
                    $bobot = $this->calculateBobot($request->tipe, $opsiData['bobot'] ?? 0, $request->kategori_id);
                    
                    OpsiSoal::create([
                        'soal_id' => $soal->id,
                        'opsi' => chr(65 + $index), // A, B, C, D, E
                        'teks' => $opsiData['teks'],
                        'bobot' => $bobot
                    ]);
                }
            });
            
            return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan soal: ' . $e->getMessage());
        }
    }

    public function uploadWord(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:docx',
            ]);

            $file = $request->file('file');
            $path = $file->getRealPath();

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
            $soals = $this->parseWordDocument($phpWord);

            DB::beginTransaction();

            foreach ($soals as $index => $soalData) {
                try {
                    $soal = Soal::create([
                        'kategori_id'   => $soalData['kategori_id'],
                        'tipe'          => $soalData['tipe'],
                        'level'         => $soalData['level'] ?? 'mudah',
                        'pertanyaan'    => $soalData['pertanyaan'],
                        'jawaban_benar' => $soalData['jawaban_benar'] ?? null,
                        'pembahasan'    => $soalData['pembahasan'] ?? null,
                    ]);

                    if (!empty($soalData['opsi']) && is_array($soalData['opsi'])) {
                        foreach ($soalData['opsi'] as $opsiData) {
                            OpsiSoal::create([
                                'soal_id' => $soal->id,
                                'opsi'    => $opsiData['opsi'] ?? null,
                                'teks'    => $opsiData['teks'] ?? '',
                                'bobot'   => $opsiData['bobot'] ?? 0,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    throw $e; // biar rollback
                }
            }

            DB::commit();

            return redirect()->route('admin.soal.index')
                ->with('success', 'Soal berhasil diupload dari file Word');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat upload Word: ' . $e->getMessage());
        }
    }



    /**
     * Parse Word Document ke array soal
     */
    private function parseWordDocument($phpWord)
    {
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }


        $blocks = preg_split('/---/', $text);
        $soals = [];

        foreach ($blocks as $i => $block) {
            $lines = array_filter(array_map('trim', explode("\n", $block)));

            if (empty($lines)) {
                continue;
            }


            // default
            $soalData = [
                'kategori_id'   => null,
                'tipe'          => null,
                'level'         => null,
                'pertanyaan'    => '',
                'opsi'          => [],
                'jawaban_benar' => null,
                'pembahasan'    => null,
            ];

            $mode = null;

            foreach ($lines as $line) {
                if (str_starts_with($line, '[kategori]')) {
                    $kode = trim(str_replace('[kategori]', '', $line));
                    $kategori = KategoriSoal::where('kode', $kode)->first();

                    if (!$kategori) {
                        throw new \Exception("Kategori dengan kode '{$kode}' tidak ditemukan di database.");
                    }

                    $soalData['kategori_id'] = $kategori->id;
                } elseif (str_starts_with($line, '[tipe]')) {
                    $soalData['tipe'] = trim(str_replace('[tipe]', '', $line));
                } elseif (str_starts_with($line, '[level]')) {
                    $soalData['level'] = trim(str_replace('[level]', '', $line));
                } elseif (str_starts_with($line, 'Pertanyaan:')) {
                    $mode = 'pertanyaan';
                    continue;
                } elseif (str_starts_with($line, 'Opsi:')) {
                    $mode = 'opsi';
                    continue;
                } elseif (str_starts_with($line, 'Jawaban Benar:')) {
                    $soalData['jawaban_benar'] = trim(str_replace('Jawaban Benar:', '', $line));
                    $mode = null;
                    continue;
                } elseif (str_starts_with($line, 'Pembahasan:')) {
                    $mode = 'pembahasan';
                    continue;
                }

                // isi data sesuai mode
                if ($mode === 'pertanyaan') {
                    $soalData['pertanyaan'] .= ($soalData['pertanyaan'] ? "\n" : '') . $line;
                } elseif ($mode === 'opsi') {
                    // format umum: A. teks | bobot: x
                    if (preg_match('/^([A-Z])\.\s*(.*?)\s*\|\s*bobot:\s*(\d+)/i', $line, $m)) {
                        $opsiKey = $m[1];
                        $opsiText = $m[2];
                        $bobot = (int)$m[3];

                        $soalData['opsi'][] = [
                            'opsi'  => $opsiKey,
                            'teks'  => $opsiText,
                            'bobot' => $bobot,
                        ];
                    }
                } elseif ($mode === 'pembahasan') {
                    $soalData['pembahasan'] .= ($soalData['pembahasan'] ? "\n" : '') . $line;
                }
            }

            $soals[] = $soalData;
        }


        return $soals;
    }




    public function edit(Soal $soal)
    {
        $soal->load(['kategori', 'opsi']);
        $kategoris = KategoriSoal::active()->get();
        $tipes = ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar'];

        return view('admin.soal.edit', compact('soal', 'kategoris', 'tipes'));
    }

    public function update(Request $request, Soal $soal)
    {

        $rules = [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:benar_salah,pg_satu,pg_bobot,pg_pilih_2,gambar',
            'level' => 'required|in:dasar,mudah,sedang,sulit,tersulit,ekstrem',
            'kategori_id' => 'required|exists:kategori_soal,id',
            'pembahasan' => 'nullable|string',
            'pembahasan_type' => 'nullable|in:text,image,both',
            'pembahasan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'jawaban_benar' => 'nullable|string',
            'opsi' => 'required|array',
            'opsi.*.teks' => 'required|string',
            'opsi.*.bobot' => 'nullable|numeric|min:0|max:1'
        ];

        // Add image validation if tipe is gambar and new image is uploaded
        if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
            $rules['gambar'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        // Dynamic validation for kepribadian categories
        // PERBAIKAN: Dynamic validation hanya untuk pg_bobot dengan kategori kepribadian
        if ($request->filled('kategori_id') && $request->tipe === 'pg_bobot') {
            $kategori = KategoriSoal::find($request->kategori_id);
            if ($kategori) {
                $kepribadianKategoriCodes = PackageCategoryMapping::getCategoriesForPackage('kepribadian');
                if (in_array($kategori->kode, $kepribadianKategoriCodes)) {
                    $rules['opsi.*.bobot'] = 'required|numeric|between:1,5';
                }
            }
        }

        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            throw $e;
        }

        try {
            DB::transaction(function () use ($request, $soal) {
                $soalData = [
                    'pertanyaan' => $request->pertanyaan,
                    'tipe' => $request->tipe,
                    'level' => $request->level,
                    'kategori_id' => $request->kategori_id,
                    'pembahasan' => $request->pembahasan,
                    'pembahasan_type' => $request->pembahasan_type ?? $soal->pembahasan_type ?? 'text',
                    'jawaban_benar' => $request->jawaban_benar
                ];

                // Handle image upload
                if ($request->tipe === 'gambar' && $request->hasFile('gambar')) {
                    if ($soal->gambar && Storage::disk('public')->exists($soal->gambar)) {
                        Storage::disk('public')->delete($soal->gambar);
                    }
                    $image = $request->file('gambar');
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('soal_images', $imageName, 'public');
                    $soalData['gambar'] = $imagePath;
                } elseif ($request->tipe !== 'gambar' && $soal->gambar) {
                    if (Storage::disk('public')->exists($soal->gambar)) {
                        Storage::disk('public')->delete($soal->gambar);
                    }
                    $soalData['gambar'] = null;
                }

                // Handle pembahasan image
                if (in_array($request->pembahasan_type, ['image', 'both']) && $request->hasFile('pembahasan_image')) {
                    if ($soal->pembahasan_image && Storage::disk('public')->exists($soal->pembahasan_image)) {
                        Storage::disk('public')->delete($soal->pembahasan_image);
                    }
                    $img = $request->file('pembahasan_image');
                    $name = time() . '_' . $img->getClientOriginalName();
                    $path = $img->storeAs('pembahasan_images', $name, 'public');
                    $soalData['pembahasan_image'] = $path;
                } elseif ($request->pembahasan_type === 'text') {
                    if ($soal->pembahasan_image && Storage::disk('public')->exists($soal->pembahasan_image)) {
                        Storage::disk('public')->delete($soal->pembahasan_image);
                    }
                    $soalData['pembahasan_image'] = null;
                }

                $soal->update($soalData);

                // Delete existing options
                $soal->opsi()->delete();

                // Create new options
                foreach ($request->opsi as $index => $opsiData) {
                    // Set bobot berdasarkan tipe soal
                    $bobot = $this->calculateBobot($request->tipe, $opsiData['bobot'] ?? 0, $request->kategori_id);
                    
                    OpsiSoal::create([
                        'soal_id' => $soal->id,
                        'opsi' => chr(65 + $index),
                        'teks' => $opsiData['teks'],
                        'bobot' => $bobot
                    ]);
                }
            });

            return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui soal: ' . $e->getMessage());
        }
    }

    public function show(Soal $soal)
    {
        return redirect()->route('admin.soal.index')->with('success', 'Redirected to soal list');
    }

    public function destroy(Soal $soal)
    {
        // Delete image if exists
        if ($soal->gambar && Storage::disk('public')->exists($soal->gambar)) {
            Storage::disk('public')->delete($soal->gambar);
        }

        $soal->delete();
        return redirect()->route('admin.soal.index')->with('success', 'Soal berhasil dihapus');
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
     * Get difficulty level labels for display
     */
    public function getDifficultyLabels()
    {
        $levels = $this->getDifficultyLevels();
        $labels = [];
        
        foreach ($levels as $level) {
            $labels[$level] = ucfirst($level);
        }
        
        return $labels;
    }
}
