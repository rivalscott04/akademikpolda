<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\YoutubeVideo;

class YoutubeVideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        YoutubeVideo::create([
            'title' => 'Demo Video Cermat Polda',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Example video
            'youtube_id' => 'dQw4w9WgXcQ',
            'is_active' => true,
            'description' => 'Video demo untuk layar laptop di halaman utama'
        ]);
    }
}
