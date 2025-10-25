<?php

namespace App\Http\Controllers;

use App\Models\PackageCategoryMapping;
use App\Models\KategoriSoal;
use Illuminate\Http\Request;

class PackageMappingController extends Controller
{
    public function index()
    {
        $kategoris = KategoriSoal::active()->get();
        $mappings = PackageCategoryMapping::getAllMappings();
        
        return view('admin.package-mapping.index', compact('kategoris', 'mappings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*' => 'array'
        ]);

        try {
            foreach ($request->mappings as $packageType => $kategoriIds) {
                PackageCategoryMapping::updateMappings($packageType, $kategoriIds);
            }

            return redirect()->route('admin.package-mapping.index')
                ->with('success', 'Pengaturan paket berhasil disimpan!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.package-mapping.index')
                ->with('error', 'Terjadi kesalahan saat menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        try {
            // Reset to default mappings for AKADEMIK packages
            $defaultMappings = [
                'bahasa_inggris' => ['BAHASA_INGGRIS'],
                'pu' => ['PU'],
                'twk' => ['TWK'],
                'numerik' => ['NUMERIK'],
                'lengkap' => ['BAHASA_INGGRIS', 'PU', 'TWK', 'NUMERIK']
            ];

            foreach ($defaultMappings as $packageType => $kategoriCodes) {
                $kategoriIds = KategoriSoal::whereIn('kode', $kategoriCodes)->pluck('id')->toArray();
                PackageCategoryMapping::updateMappings($packageType, $kategoriIds);
            }

            return redirect()->route('admin.package-mapping.index')
                ->with('success', 'Pengaturan paket berhasil direset ke default!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.package-mapping.index')
                ->with('error', 'Terjadi kesalahan saat reset: ' . $e->getMessage());
        }
    }

    public function api()
    {
        return response()->json(PackageCategoryMapping::getAllMappings());
    }
}
