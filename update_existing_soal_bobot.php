<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Soal;
use App\Models\OpsiSoal;
use App\Models\PackageCategoryMapping;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Starting update of existing soal bobot...\n";

$soals = Soal::with('opsi', 'kategori')->get();

foreach ($soals as $soal) {
    echo "Processing soal ID: {$soal->id}, Tipe: {$soal->tipe}\n";
    
    $updated = false;
    
    foreach ($soal->opsi as $opsi) {
        $oldBobot = $opsi->bobot;
        $newBobot = calculateBobot($soal->tipe, $opsi->bobot, $soal->kategori_id);
        
        if ($oldBobot != $newBobot) {
            $opsi->update(['bobot' => $newBobot]);
            echo "  Updated opsi {$opsi->opsi}: {$oldBobot} -> {$newBobot}\n";
            $updated = true;
        }
    }
    
    if (!$updated) {
        echo "  No changes needed\n";
    }
}

echo "Update completed!\n";

function calculateBobot($tipe, $inputBobot, $kategoriId = null)
{
    switch ($tipe) {
        case 'pg_pilih_2':
            // Untuk pilih 2, bobot = 0.5 untuk jawaban benar, 0 untuk salah
            return $inputBobot > 0 ? 0.5 : 0;
            
        case 'pg_bobot':
            // Untuk pg_bobot, gunakan bobot asli dari input (1-5 untuk kepribadian, 0-1 untuk lainnya)
            if ($kategoriId) {
                $kategori = \App\Models\KategoriSoal::find($kategoriId);
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
