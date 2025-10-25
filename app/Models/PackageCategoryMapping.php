<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCategoryMapping extends Model
{
    protected $fillable = [
        'package_type',
        'kategori_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSoal::class, 'kategori_id');
    }

    /**
     * Get allowed categories for a package type
     */
    public static function getCategoriesForPackage($packageType)
    {
        // OPTIMASI: Gunakan join untuk menghindari N+1 query
        return self::join('kategori_soal', 'package_category_mappings.kategori_id', '=', 'kategori_soal.id')
            ->where('package_category_mappings.package_type', $packageType)
            ->where('kategori_soal.is_active', true)
            ->pluck('kategori_soal.kode')
            ->toArray();
    }

    /**
     * Get all package mappings as array
     */
    public static function getAllMappings()
    {
        // OPTIMASI: Cache semua mappings karena jarang berubah
        return cache()->remember('all_package_category_mappings', 60 * 24, function () {
            $mappings = [];
            $packageTypes = ['bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap'];
            
            foreach ($packageTypes as $packageType) {
                $mappings[$packageType] = self::getCategoriesForPackage($packageType);
            }
            
            return $mappings;
        });
    }

    /**
     * Update mappings for a package type
     */
    public static function updateMappings($packageType, $kategoriIds)
    {
        // Delete existing mappings for this package
        self::where('package_type', $packageType)->delete();
        
        // Insert new mappings
        $mappings = [];
        foreach ($kategoriIds as $kategoriId) {
            $mappings[] = [
                'package_type' => $packageType,
                'kategori_id' => $kategoriId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        if (!empty($mappings)) {
            self::insert($mappings);
        }
        
        // OPTIMASI: Clear cache setelah update
        self::clearCache();
    }
    
    /**
     * Clear all package mapping caches
     */
    public static function clearCache()
    {
        cache()->forget('all_package_category_mappings');
        cache()->forget('package_category_mappings');
        
        // Clear individual package caches
        $packageTypes = ['bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap'];
        foreach ($packageTypes as $packageType) {
            cache()->forget("package_categories_{$packageType}");
        }
    }
}
