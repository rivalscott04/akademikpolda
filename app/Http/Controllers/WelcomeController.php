<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YoutubeVideo;
use App\Models\Package;

class WelcomeController extends Controller
{
    public function index()
    {
        $activeVideo = YoutubeVideo::getActiveVideo();
        $packages = Package::active()->ordered()->get();
        
        return view('welcome', compact('activeVideo', 'packages'));
    }
}
