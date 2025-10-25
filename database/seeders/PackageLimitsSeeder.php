<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageLimitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packageLimits = [
            [
                'package_type' => 'free',
                'max_tryouts' => 1,
                'allowed_categories' => json_encode(['BAHASA_INGGRIS', 'PU', 'TWK', 'NUMERIK']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'bahasa_inggris',
                'max_tryouts' => 10,
                'allowed_categories' => json_encode(['BAHASA_INGGRIS']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'pu',
                'max_tryouts' => 10,
                'allowed_categories' => json_encode(['PU']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'twk',
                'max_tryouts' => 10,
                'allowed_categories' => json_encode(['TWK']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'numerik',
                'max_tryouts' => 10,
                'allowed_categories' => json_encode(['NUMERIK']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'package_type' => 'lengkap',
                'max_tryouts' => 15,
                'allowed_categories' => json_encode(['BAHASA_INGGRIS', 'PU', 'TWK', 'NUMERIK']),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('package_limits')->insert($packageLimits);
    }
}
