# AKADEMIK MODULE - FULL REPLACEMENT IMPLEMENTATION GUIDE

## OVERVIEW
This document provides a complete guide for **replacing all existing PSIKOLOGI packages** with the new **AKADEMIK module** within the CERMAT POLDA system. The overall system flow, architecture, and user experience remain unchanged. Only the **package configuration, models, controllers, and views** are updated to reflect the new AKADEMIK context.

The existing package **`lengkap` remains intact** and will continue to function as the main combined package.

---

## SYSTEM ARCHITECTURE

### 1. INDUK PAKET (FINAL STRUCTURE)
- **AKADEMIK** (replacement)
  - `bahasa_inggris` (paket)
  - `pu` (paket) - Pengetahuan Umum
  - `twk` (paket) - Tes Wawasan Kebangsaan
  - `numerik` (paket)
  - `lengkap` (paket) *(existing, retained)*

### 2. KATEGORI SOAL DINAMIS
- Categories remain **DYNAMIC** and configurable by admin.
- All mappings handled via `PackageCategoryMapping` model.
- No hardcoded categories — fully database-driven.

---

## IMPLEMENTATION STEPS

### STEP 1: UPDATE CONFIG PACKAGES.PHP
**File:** `config/packages.php`

**Action:** Replace all previous PSIKOLOGI packages with AKADEMIK ones. Retain only `lengkap`.
```php
'package_limits' => [
    // AKADEMIK packages
    'bahasa_inggris' => [
        'max_tryouts' => 10,
        'description' => 'Tryout Bahasa Inggris'
    ],
    'pu' => [
        'max_tryouts' => 10,
        'description' => 'Tryout Pengetahuan Umum'
    ],
    'twk' => [
        'max_tryouts' => 10,
        'description' => 'Tryout Tes Wawasan Kebangsaan'
    ],
    'numerik' => [
        'max_tryouts' => 10,
        'description' => 'Tryout Numerik'
    ],
    'lengkap' => [
        'max_tryouts' => 15,
        'description' => 'Paket lengkap mencakup semua tes akademik'
    ]
]
```

---

### STEP 2: UPDATE PACKAGE MAPPING SEEDER
**File:** `database/seeders/PackageMappingSeeder.php`

**Action:** Replace PSIKOLOGI mappings with AKADEMIK mappings.
```php
$akademikMappings = [
    'bahasa_inggris' => ['BAHASA_INGGRIS'],
    'pu' => ['PU'],
    'twk' => ['TWK'],
    'numerik' => ['NUMERIK'],
    'lengkap' => ['BAHASA_INGGRIS', 'PU', 'TWK', 'NUMERIK']
];

foreach ($akademikMappings as $packageType => $kategoriCodes) {
    $kategoriIds = KategoriSoal::whereIn('kode', $kategoriCodes)->pluck('id')->toArray();
    PackageCategoryMapping::updateMappings($packageType, $kategoriCodes);
}
```

---

### STEP 3: CONTROLLERS UPDATE
**Files:**
- `app/Http/Controllers/KategoriSoalController.php`
- `app/Http/Controllers/SoalController.php`
- `app/Http/Controllers/SubscriptionController.php`

**Action:** Update naming and type references from `psikologi` to `akademik`.
```php
public function index(Request $request)
{
    $type = $request->get('type', 'akademik');
    $kategoris = KategoriSoal::orderBy('nama')->paginate(20);

    return view('admin.kategori.index', compact('kategoris', 'type'));
}
```

In `SubscriptionController`, keep only the new packages and `lengkap`.
```php
$packageDetails = [
    'bahasa_inggris' => [
        'name' => 'Paket Bahasa Inggris',
        'description' => 'Fokus Tes Bahasa Inggris',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal lengkap',
            'Analisis kemampuan linguistik',
            'Timer simulasi ujian',
            'Riwayat progress'
        ]
    ],
    'pu' => [
        'name' => 'Paket Pengetahuan Umum',
        'description' => 'Fokus Tes Pengetahuan Umum',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal pengetahuan umum',
            'Analisis kemampuan kognitif',
            'Timer simulasi ujian',
            'Riwayat progress'
        ]
    ],
    'twk' => [
        'name' => 'Paket TWK',
        'description' => 'Tes Wawasan Kebangsaan',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal TWK',
            'Analisis kebangsaan',
            'Timer simulasi ujian',
            'Riwayat progress'
        ]
    ],
    'numerik' => [
        'name' => 'Paket Numerik',
        'description' => 'Fokus Tes Numerik',
        'price' => 75000,
        'duration' => 30,
        'features' => [
            'Bank soal numerik',
            'Analisis logika dan angka',
            'Timer simulasi ujian',
            'Riwayat progress'
        ]
    ],
    'lengkap' => [
        'name' => 'Paket Lengkap',
        'description' => 'Semua tes dalam satu paket',
        'price' => 250000,
        'duration' => 45,
        'features' => [
            'Semua kategori AKADEMIK',
            'Simulasi penuh',
            'Analisis lengkap',
            'Riwayat & laporan performa'
        ]
    ]
];
```

---

### STEP 4: UPDATE ADMIN MENU
**File:** `resources/views/components/sidenav.blade.php`

**Action:** Replace PSIKOLOGI menu with AKADEMIK.
```php
<li>
    <a href="#"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Master Soal AKADEMIK</span> <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        <li>
            <a href="{{ route('admin.kategori.index', ['type' => 'akademik']) }}">
                <i class="fa fa-tags"></i> <span class="nav-label">Kategori Soal</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.soal.index', ['type' => 'akademik']) }}">
                <i class="fa fa-question-circle"></i> <span class="nav-label">Soal</span>
            </a>
        </li>
    </ul>
</li>
```

---

### STEP 5: UPDATE VIEWS
**Files:**
- `resources/views/admin/kategori/index.blade.php`
- `resources/views/admin/soal/index.blade.php`

**Action:** Rename titles and icons for AKADEMIK.
```php
<div class="ibox-title">
    <h5><i class="fa fa-graduation-cap"></i> Kategori Soal AKADEMIK</h5>
    <div class="ibox-tools">
        <a href="{{ route('admin.kategori.create', ['type' => 'akademik']) }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Kategori
        </a>
    </div>
</div>
```

---

### STEP 6: UPDATE ROUTES
**File:** `routes/web.php`

**Action:** Ensure routes only reference `akademik` context.
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('kategori', [KategoriSoalController::class, 'index'])->name('kategori.index');
    Route::get('soal', [SoalController::class, 'index'])->name('soal.index');
    Route::resource('kategori', KategoriSoalController::class);
    Route::resource('soal', SoalController::class);
});
```

---

## TESTING CHECKLIST

### ADMIN
- [ ] Navigasi menu AKADEMIK
- [ ] CRUD kategori & soal AKADEMIK
- [ ] Cek paket `lengkap` masih berfungsi
- [ ] Seed mappings sesuai kategori

### USER
- [ ] Lihat paket AKADEMIK & Lengkap di menu langganan
- [ ] Pembelian dan ujian berjalan lancar

### SYSTEM
- [ ] Mapping kategori dinamis berfungsi
- [ ] Timer & skor sesuai
- [ ] Pembayaran tetap valid

---

## NOTES
1. Semua nama `psikologi` dihapus dan diganti `akademik`.
2. Struktur controller & view tetap sama.
3. Tidak ada perubahan database.
4. Paket `lengkap` tetap dipertahankan dan menyertakan seluruh kategori AKADEMIK.

---

## ESTIMATED TIME
Total waktu implementasi penuh: **8 jam (maks)**
- Konfigurasi & Seeder: 1 jam
- Controller & Model update: 2 jam
- View & Menu update: 2 jam
- Pengujian & Penyesuaian: 3 jam

---

**Final Outcome:**
> Sistem lama PSIKOLOGI kini sepenuhnya digantikan oleh modul AKADEMIK, dengan alur, tampilan, dan logika yang identik — namun seluruh data, paket, dan konteks telah berganti ke domain AKADEMIK secara penuh.
