<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccessTier;

class AccessTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['key' => 'bahasa_inggris', 'name' => 'Bahasa Inggris'],
            ['key' => 'pu', 'name' => 'Pengetahuan Umum'],
            ['key' => 'twk', 'name' => 'TWK'],
            ['key' => 'numerik', 'name' => 'Numerik'],
            ['key' => 'lengkap', 'name' => 'Lengkap'],
            ['key' => 'free', 'name' => 'Free'],
        ];

        foreach ($tiers as $tier) {
            AccessTier::firstOrCreate(['key' => $tier['key']], $tier);
        }
    }
}


