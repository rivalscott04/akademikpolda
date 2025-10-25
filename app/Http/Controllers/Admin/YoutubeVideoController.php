<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YoutubeVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YoutubeVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = YoutubeVideo::latest()->get();
        return view('admin.youtube-videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.youtube-videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'youtube_url' => 'required|string|url',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $youtubeVideo = new YoutubeVideo();
        $youtubeVideo->title = $request->title;
        $youtubeVideo->youtube_url = $request->youtube_url;
        $youtubeVideo->youtube_id = $youtubeVideo->extractVideoId($request->youtube_url);
        $youtubeVideo->description = $request->description;
        $youtubeVideo->is_active = $request->has('is_active');
        $youtubeVideo->save();

        return redirect()->route('admin.youtube-videos.index')
            ->with('success', 'YouTube video berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(YoutubeVideo $youtubeVideo)
    {
        return view('admin.youtube-videos.show', compact('youtubeVideo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(YoutubeVideo $youtubeVideo)
    {
        return view('admin.youtube-videos.edit', compact('youtubeVideo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, YoutubeVideo $youtubeVideo)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'youtube_url' => 'required|string|url',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $youtubeVideo->title = $request->title;
        $youtubeVideo->youtube_url = $request->youtube_url;
        $youtubeVideo->youtube_id = $youtubeVideo->extractVideoId($request->youtube_url);
        $youtubeVideo->description = $request->description;
        $youtubeVideo->is_active = $request->has('is_active');
        $youtubeVideo->save();

        return redirect()->route('admin.youtube-videos.index')
            ->with('success', 'YouTube video berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(YoutubeVideo $youtubeVideo)
    {
        $youtubeVideo->delete();

        return redirect()->route('admin.youtube-videos.index')
            ->with('success', 'YouTube video berhasil dihapus!');
    }
}
