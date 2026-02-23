<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
  public function index(Request $request)
{
    $query = Testimonial::query()->latest();

    // Optional: live search support (same as your products page)
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Important: paginate instead of get()
    $testimonials = $query->paginate(10);   // ← 10, 15, 20 ... whatever you prefer

    return view('testimonials.index', compact('testimonials'));
}

    public function create()
    {
        return view('testimonials.create');
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'rating'   => 'required|integer|min:1|max:5',
        'text'     => 'required|string|min:10',
        'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $data = $request->only(['name', 'position', 'rating', 'text']);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('testimonials', 'public');
        $data['image'] = $path;
    }

    Testimonial::create($data);

    return redirect()->route('testimonials.index')->with('success', 'Testimonial created successfully.');
}



    public function edit(Testimonial $testimonial)
    {
        return view('testimonials.edit', compact('testimonial'));
    }

   public function update(Request $request, Testimonial $testimonial)
{
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'rating'   => 'required|integer|min:1|max:5',
        'text'     => 'required|string|min:10',
        'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $data = $request->only(['name', 'position', 'rating', 'text']);

    if ($request->hasFile('image')) {
        // Purani image delete optional
        if ($testimonial->image) {
Storage::disk('public')->delete($testimonial->image);
        }
        $path = $request->file('image')->store('testimonials', 'public');
        $data['image'] = $path;
    }

    $testimonial->update($data);

    return redirect()->route('testimonials.index')->with('success', 'Testimonial updated successfully.');
}

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
    // app/Http/Controllers/TestimonialController.php

public function apiIndex()
{
    $testimonials = Testimonial::latest()
        ->select('name', 'position', 'text', 'image', 'rating') // جتنے فیلڈ چاہیے
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