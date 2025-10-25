<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            AccessTierSeeder::class,
            UserSeeder::class,
            KategoriSoalSeeder::class,
            SoalSeeder::class,
            PackageLimitsSeeder::class,
            PackageMappingSeeder::class,
            TryoutSeeder::class,
            ScoringSettingSeeder::class,
            YoutubeVideoSeeder::class,
        ]);
    }
}
