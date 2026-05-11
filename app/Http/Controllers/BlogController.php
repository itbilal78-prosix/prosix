<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index() {
        $blogs = Blog::latest()->paginate(10);
        return view('blogs.index', compact('blogs'));
    }

    public function create() {
        return view('blogs.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'body'        => 'required|string',
            'image'       => 'nullable|image|max:2048',
            'video'       => 'nullable|mimes:mp4,mov,avi|max:102400',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('blogs/videos', 'public');
        }

        $blog = Blog::create([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,
            'body'        => $request->body,
            'image'       => $imagePath,
            'video'       => $videoPath,
        ]);

        // ✅ Activity Log
        ActivityLogger::log('created', 'Blog', $blog->title, $blog->id, [
            'title'       => $blog->title,
            'description' => $blog->description,
        ]);

        return redirect()->route('blogs.index')->with('success', 'Blog Created Successfully!');
    }

    public function edit(Blog $blog) {
        return view('blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog) {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'body'        => 'required|string',
            'image'       => 'nullable|image|max:2048',
            'video'       => 'nullable|mimes:mp4,mov,avi|max:102400',
        ]);

        $imagePath = $blog->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        $videoPath = $blog->video;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('blogs/videos', 'public');
        }

        $blog->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'description' => $request->description,
            'body'        => $request->body,
            'image'       => $imagePath,
            'video'       => $videoPath,
        ]);

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Blog', $blog->title, $blog->id, [
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('blogs.index')->with('success', 'Blog Updated Successfully!');
    }

    public function destroy(Blog $blog) {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Blog', $blog->title, $blog->id, [
            'title' => $blog->title,
            'image' => $blog->image ? asset('storage/' . $blog->image) : null,
        ]);

        $blog->delete();
        return redirect()->route('blogs.index')->with('success', 'Blog Deleted Successfully!');
    }

    // API
    public function apiIndex() {
        $blogs = Blog::latest()->get();
        return response()->json($blogs);
    }

    public function apiShow($slug) {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }
        return response()->json($blog);
    }
}
