<?php

namespace Database\Seeders;

use App\Models\KategoriSoal;
use App\Models\OpsiSoal;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class SoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder soal akan dibuat secara dinamis melalui import dari Word
        // Seeder ini sengaja dikosongkan untuk menghindari hardcode soal
        $this->command->info("SoalSeeder dikosongkan. Gunakan import dari Word untuk menambah soal.");
    }
}
