<?php

namespace Database\Seeders;

use App\Models\PackageCategoryMapping;
use App\Models\KategoriSoal;
use Illuminate\Database\Seeder;

class PackageMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dynamic package mappings based on available categories
        $packageTypes = ['free', 'bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap'];
        
        foreach ($packageTypes as $packageType) {
            $kategoriCodes = $this->getPackageMapping($packageType);
            
            if (!empty($kategoriCodes)) {
                $kategoriIds = KategoriSoal::whereIn('kode', $kategoriCodes)->pluck('id')->toArray();
                PackageCategoryMapping::updateMappings($packageType, $kategoriIds);
            }
        }
    }

    /**
     * Get package mapping based on package type (dynamic)
     */
    private function getPackageMapping($packageType)
    {
        // Get all available categories from database
        $allCategories = KategoriSoal::active()->pluck('kode')->toArray();
        
        switch ($packageType) {
            case 'free':
                // FREE bisa akses semua kategori yang ada
                return $allCategories;
                
            case 'bahasa_inggris':
                // Bahasa Inggris: ambil kategori BAHASA_ING
                return array_filter($allCategories, function($cat) {
                    return $cat === 'BAHASA_ING';
                });
                
            case 'pu':
                // Pengetahuan Umum: ambil kategori PU
                return array_filter($allCategories, function($cat) {
                    return $cat === 'PU';
                });
                
            case 'twk':
                // Tes Wawasan Kebangsaan: ambil kategori TWK
                return array_filter($allCategories, function($cat) {
                    return $cat === 'TWK';
                });
                
            case 'numerik':
                // Numerik: ambil kategori NUMERIK
                return array_filter($allCategories, function($cat) {
                    return $cat === 'NUMERIK';
                });
                
            case 'lengkap':
                // Lengkap: ambil semua kategori AKADEMIK
                return array_filter($allCategories, function($cat) {
                    return in_array($cat, ['BAHASA_ING', 'PU', 'TWK', 'NUMERIK']);
                });
                
            default:
                return [];
        }
    }
}
