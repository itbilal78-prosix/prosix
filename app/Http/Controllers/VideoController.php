<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->get();
        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        return view('videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'thumbnail' => 'required|image',
            'video_url' => 'required|mimetypes:video/mp4,video/avi,video/mov|max:20000',
        ]);

        // Upload files
        $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        $videoPath = $request->file('video_url')->store('videos', 'public');

        Video::create([
            'title' => $request->title,
            'thumbnail' => $thumbnailPath,
            'video_url' => $videoPath,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video added successfully.');
    }

    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image',
            'video_url' => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:20000',
        ]);

        if ($request->hasFile('thumbnail')) {
            $video->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($request->hasFile('video_url')) {
            $video->video_url = $request->file('video_url')->store('videos', 'public');
        }

        $video->title = $request->title;
        $video->save();

        return redirect()->route('videos.index')->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video deleted successfully.');
    }
    // app/Http/Controllers/VideoController.php

public function apiIndex()
{
    $videos = Video::latest()->get();

    $videos = $videos->map(function($video) {
        return [
            'id'       => $video->id,
            'title'    => $video->title,
            'thumbnail' => asset('storage/' . $video->thumbnail),
            'video_url' => asset('storage/' . $video->video_url),
        ];
    });

    return response()->json($videos);
}


}
