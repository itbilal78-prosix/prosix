<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::query()->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $testimonials = $query->paginate(10);
        return view('testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'rating'   => 'required|integer|min:1|max:5',
            'text'     => 'required|string|min:10',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'position', 'rating', 'text']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial = Testimonial::create($data);

        // ✅ Activity Log
        ActivityLogger::log('created', 'Testimonial', $testimonial->name, $testimonial->id, [
            'name'     => $testimonial->name,
            'position' => $testimonial->position,
            'rating'   => $testimonial->rating,
        ]);

        return redirect()->route('testimonials.index')->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'rating'   => 'required|integer|min:1|max:5',
            'text'     => 'required|string|min:10',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'position', 'rating', 'text']);

        if ($request->hasFile('image')) {
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Testimonial', $testimonial->name, $testimonial->id, [
            'name'     => $request->name,
            'position' => $request->position,
            'rating'   => $request->rating,
        ]);

        return redirect()->route('testimonials.index')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Testimonial', $testimonial->name, $testimonial->id, [
            'name'     => $testimonial->name,
            'position' => $testimonial->position,
            'image'    => $testimonial->image ? asset('storage/' . $testimonial->image) : null,
        ]);

        $testimonial->delete();
        return redirect()->route('testimonials.index')->with('success', 'Testimonial deleted successfully.');
    }

    public function apiIndex()
    {
        $testimonials = Testimonial::latest()
            ->select('name', 'position', 'text', 'image', 'rating')
            ->get()
            ->map(function ($item) {
                return [
                    'name'     => $item->name,
                    'position' => $item->position,
                    'text'     => $item->text,
                    'image'    => $item->image ? asset('storage/' . $item->image) : null,
                    'rating'   => (int) $item->rating,
                ];
            });

        return response()->json($testimonials);
    }
}
