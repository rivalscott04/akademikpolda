<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Paket Bahasa Inggris',
                'description' => 'Paket khusus untuk tes Bahasa Inggris',
                'price' => 99000,
                'old_price' => 149000,
                'label' => null,
                'features' => [
                    'Bank soal Bahasa Inggris lengkap',
                    'Simulasi tes dengan sistem CAT',
                    'Analisis kemampuan linguistik',
                    'Timer simulasi ujian real'
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Paket Pengetahuan Umum',
                'description' => 'Paket khusus untuk tes Pengetahuan Umum',
                'price' => 99000,
                'old_price' => 149000,
                'label' => null,
                'features' => [
                    'Bank soal Pengetahuan Umum lengkap',
                    'Simulasi tes dengan sistem CAT',
                    'Analisis kemampuan kognitif',
                    'Timer simulasi ujian real'
                ],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Paket TWK',
                'description' => 'Paket khusus untuk Tes Wawasan Kebangsaan',
                'price' => 99000,
                'old_price' => 149000,
                'label' => null,
                'features' => [
                    'Bank soal TWK lengkap',
                    'Simulasi tes dengan sistem CAT',
                    'Analisis wawasan kebangsaan',
                    'Timer simulasi ujian real'
                ],
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Paket Numerik',
                'description' => 'Paket khusus untuk tes Numerik',
                'price' => 99000,
                'old_price' => 149000,
                'label' => null,
                'features' => [
                    'Bank soal Numerik lengkap',
                    'Simulasi tes dengan sistem CAT',
                    'Analisis logika dan angka',
                    'Timer simulasi ujian real'
                ],
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Paket Lengkap',
                'description' => 'Paket lengkap semua tes AKADEMIK',
                'price' => 299000,
                'old_price' => 399000,
                'label' => 'PALING LARIS',
                'features' => [
                    'Semua paket AKADEMIK dalam satu paket',
                    'Bank soal lengkap untuk semua kategori',
                    'Simulasi tes gabungan',
                    'Analisis kemampuan komprehensif',
                    'Laporan progress lengkap',
                    'Sertifikat penyelesaian'
                ],
                'is_active' => true,
                'sort_order' => 5
            ]
        ];

        foreach ($packages as $packageData) {
            Package::create($packageData);
        }
    }
}