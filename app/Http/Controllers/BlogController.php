<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    // Web: List blogs (with pagination)
    public function index() {
        $blogs = Blog::latest()->paginate(10);
        return view('blogs.index', compact('blogs'));
    }

    // Web: Create blog page
    public function create() {
        return view('blogs.create');
    }

    // Web: Store blog
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi|max:102400',
        ]);

        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        $videoPath = null;
        if($request->hasFile('video')){
            $videoPath = $request->file('video')->store('blogs/videos', 'public');
        }

        Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'body' => $request->body,
            'image' => $imagePath,
            'video' => $videoPath,
        ]);

        return redirect()->route('blogs.index')->with('success','Blog Created Successfully!');
    }

    // Web: Edit blog page
    public function edit(Blog $blog) {
        return view('blogs.edit', compact('blog'));
    }

    // Web: Update blog
    public function update(Request $request, Blog $blog) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi|max:102400',
        ]);

        $imagePath = $blog->image;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        $videoPath = $blog->video;
        if($request->hasFile('video')){
            $videoPath = $request->file('video')->store('blogs/videos', 'public');
        }

        $blog->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'body' => $request->body,
            'image' => $imagePath,
            'video' => $videoPath,
        ]);

        return redirect()->route('blogs.index')->with('success','Blog Updated Successfully!');
    }

    // Web: Delete blog
    public function destroy(Blog $blog) {
        $blog->delete();
        return redirect()->route('blogs.index')->with('success', 'Blog Deleted Successfully!');
    }

    // ========================
    // API Methods for Frontend
    // ========================

    // API: Get all blogs
    public function apiIndex() {
        $blogs = Blog::latest()->get();
        return response()->json($blogs);
    }

    // API: Get single blog by slug
    public function apiShow($slug) {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        return response()->json($blog);
    }
}
