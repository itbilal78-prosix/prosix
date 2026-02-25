<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'title'           => 'required|string|max:255',
            'thumbnail'       => 'nullable|image|max:5000',
            'auto_thumbnail'  => 'nullable|string',   // base64 from canvas
            'video_url'       => 'required|mimetypes:video/mp4,video/avi,video/quicktime,video/x-msvideo|max:102400',
        ]);

        // ── Video upload ──
        $videoPath = $request->file('video_url')->store('videos', 'public');

        // ── Thumbnail logic ──
        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            // 1) Custom uploaded thumbnail
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');

        } elseif ($request->filled('auto_thumbnail')) {
            // 2) Auto-generated from video first frame (base64)
            $thumbnailPath = $this->saveBase64Image($request->auto_thumbnail);
        }

        Video::create([
            'title'       => $request->title,
            'thumbnail'   => $thumbnailPath,
            'video_url'   => $videoPath,
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
            'title'           => 'required|string|max:255',
            'thumbnail'       => 'nullable|image|max:5000',
            'auto_thumbnail'  => 'nullable|string',
            'video_url'       => 'nullable|mimetypes:video/mp4,video/avi,video/quicktime,video/x-msvideo|max:102400',
        ]);

        if ($request->hasFile('thumbnail')) {
            $video->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');

        } elseif ($request->filled('auto_thumbnail')) {
            $video->thumbnail = $this->saveBase64Image($request->auto_thumbnail);
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

    // ── API for frontend ──
    public function apiIndex()
    {
        $videos = Video::latest()->get()->map(function ($video) {
            return [
                'id'        => $video->id,
                'title'     => $video->title,
                'thumbnail' => $video->thumbnail ? asset('storage/' . $video->thumbnail) : null,
                'video_url' => asset('storage/' . $video->video_url),
            ];
        });

        return response()->json($videos);
    }

    // ── Helper: save base64 image to storage ──
    private function saveBase64Image(string $base64): string
    {
        // Strip data URI prefix: "data:image/jpeg;base64,..."
        $data = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $decoded = base64_decode($data);

        $filename = 'thumbnails/auto_' . uniqid() . '.jpg';
        Storage::disk('public')->put($filename, $decoded);

        return $filename;
    }
}
