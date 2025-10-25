<?php

namespace Database\Seeders;

use App\Models\Tryout;
use App\Models\TryoutBlueprint;
use App\Models\KategoriSoal;
use Illuminate\Database\Seeder;

class TryoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil kategori soal yang tersedia
        $kategoris = KategoriSoal::all();
        
        if ($kategoris->isEmpty()) {
            $this->command->warn('Tidak ada kategori soal ditemukan. Jalankan KategoriSoalSeeder terlebih dahulu.');
            return;
        }

        // Tryout 1: Tryout Bahasa Inggris
        $tryout1 = Tryout::create([
            'judul' => 'Tryout Bahasa Inggris',
            'deskripsi' => 'Tryout Bahasa Inggris untuk persiapan tes POLRI dengan 20 soal dalam 30 menit',
            'struktur' => [
                'BAHASA_INGGRIS' => 20
            ],
            'durasi_menit' => 30,
            'akses_paket' => 'bahasa_inggris',
            'jenis_paket' => 'bahasa_inggris',
            'shuffle_questions' => true,
            'is_active' => true
        ]);

        // Blueprint untuk Tryout 1
        $this->createBlueprints($tryout1, [
            ['kategori' => 'BAHASA_INGGRIS', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'BAHASA_INGGRIS', 'level' => 'sedang', 'jumlah' => 10],
        ]);

        // Tryout 2: Tryout Pengetahuan Umum
        $tryout2 = Tryout::create([
            'judul' => 'Tryout Pengetahuan Umum',
            'deskripsi' => 'Tryout Pengetahuan Umum untuk persiapan tes POLRI dengan 20 soal dalam 30 menit',
            'struktur' => [
                'PU' => 20
            ],
            'durasi_menit' => 30,
            'akses_paket' => 'pu',
            'jenis_paket' => 'pu',
            'shuffle_questions' => true,
            'is_active' => true
        ]);

        // Blueprint untuk Tryout 2
        $this->createBlueprints($tryout2, [
            ['kategori' => 'PU', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'PU', 'level' => 'sedang', 'jumlah' => 10],
        ]);

        // Tryout 3: Tryout TWK
        $tryout3 = Tryout::create([
            'judul' => 'Tryout TWK',
            'deskripsi' => 'Tryout Tes Wawasan Kebangsaan untuk persiapan tes POLRI dengan 20 soal dalam 30 menit',
            'struktur' => [
                'TWK' => 20
            ],
            'durasi_menit' => 30,
            'akses_paket' => 'twk',
            'jenis_paket' => 'twk',
            'shuffle_questions' => true,
            'is_active' => true
        ]);

        // Blueprint untuk Tryout 3
        $this->createBlueprints($tryout3, [
            ['kategori' => 'TWK', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'TWK', 'level' => 'sedang', 'jumlah' => 10],
        ]);

        // Tryout 4: Tryout Numerik
        $tryout4 = Tryout::create([
            'judul' => 'Tryout Numerik',
            'deskripsi' => 'Tryout Numerik untuk persiapan tes POLRI dengan 20 soal dalam 30 menit',
            'struktur' => [
                'NUMERIK' => 20
            ],
            'durasi_menit' => 30,
            'akses_paket' => 'numerik',
            'jenis_paket' => 'numerik',
            'shuffle_questions' => true,
            'is_active' => true
        ]);

        // Blueprint untuk Tryout 4
        $this->createBlueprints($tryout4, [
            ['kategori' => 'NUMERIK', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'NUMERIK', 'level' => 'sedang', 'jumlah' => 10],
        ]);

        // Tryout 5: Tryout Lengkap
        $tryout5 = Tryout::create([
            'judul' => 'Tryout Lengkap AKADEMIK',
            'deskripsi' => 'Tryout lengkap semua kategori AKADEMIK dengan 80 soal dalam 120 menit',
            'struktur' => [
                'BAHASA_INGGRIS' => 20,
                'PU' => 20,
                'TWK' => 20,
                'NUMERIK' => 20
            ],
            'durasi_menit' => 120,
            'akses_paket' => 'lengkap',
            'jenis_paket' => 'lengkap',
            'shuffle_questions' => true,
            'is_active' => true
        ]);

        // Blueprint untuk Tryout 5
        $this->createBlueprints($tryout5, [
            ['kategori' => 'BAHASA_INGGRIS', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'BAHASA_INGGRIS', 'level' => 'sedang', 'jumlah' => 10],
            ['kategori' => 'PU', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'PU', 'level' => 'sedang', 'jumlah' => 10],
            ['kategori' => 'TWK', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'TWK', 'level' => 'sedang', 'jumlah' => 10],
            ['kategori' => 'NUMERIK', 'level' => 'mudah', 'jumlah' => 10],
            ['kategori' => 'NUMERIK', 'level' => 'sedang', 'jumlah' => 10],
        ]);

        $this->command->info('TryoutSeeder berhasil dijalankan!');
        $this->command->info('Dibuat 5 tryout AKADEMIK: 4 tryout individual + 1 tryout lengkap.');
    }

    /**
     * Buat blueprint untuk tryout
     */
    private function createBlueprints(Tryout $tryout, array $blueprints)
    {
        foreach ($blueprints as $blueprint) {
            $kategori = KategoriSoal::where('kode', $blueprint['kategori'])->first();
            
            if ($kategori) {
                TryoutBlueprint::create([
                    'tryout_id' => $tryout->id,
                    'kategori_id' => $kategori->id,
                    'level' => $blueprint['level'],
                    'jumlah' => $blueprint['jumlah']
                ]);
            }
        }
    }
}
