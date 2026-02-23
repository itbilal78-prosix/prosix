<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\DealImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::with('images')->latest()->get();
        return view('deals.index', compact('deals'));
    }

    public function create()
    {
        return view('deals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'images' => 'required|array|max:6',
            'images.*' => 'image|mimes:jpg,png,jpeg|max:2048',
            'links.*' => 'nullable|url',
        ]);

        $deal = Deal::create($request->only([
            'title', 'subtitle', 'description', 'button_text', 'button_link'
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('deals', 'public');
                $deal->images()->create([
                    'image_path' => '/storage/' . $path,
                    'link' => $request->links[$index] ?? null,
                ]);
            }
        }

        return redirect()->route('deals.index')->with('success', 'Deal Added Successfully');
    }

    // Yeh edit method add karo
    public function edit(Deal $deal)
    {
        $deal->load('images'); // images bhi load karo edit ke liye
        return view('deals.edit', compact('deal'));
    }

    // Yeh update method add karo (new images add karne ka option bhi)
  public function update(Request $request, Deal $deal)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'subtitle' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'button_text' => 'nullable|string|max:100',
        'button_link' => 'nullable|string|max:255',
        'images' => 'nullable|array|max:6',
        'images.*' => 'image|mimes:jpg,png,jpeg|max:2048',
        'replace_images' => 'nullable|array',
        'replace_images.*' => 'image|mimes:jpg,png,jpeg|max:2048',
        'existing_links' => 'nullable|array',
        'existing_links.*' => 'nullable|url',
    ]);

    // Update deal details
    $deal->update($request->only([
        'title', 'subtitle', 'description', 'button_text', 'button_link'
    ]));

    // Handle replacing existing images
    if ($request->has('replace_images')) {
        foreach ($request->replace_images as $id => $newImage) {
            $imgModel = $deal->images()->find($id);
            if ($imgModel && $newImage) {
                // Delete old image from storage
                Storage::delete(str_replace('/storage/', 'public/', $imgModel->image_path));
                
                // Store new image
                $path = $newImage->store('deals', 'public');
                $imgModel->update([
                    'image_path' => '/storage/' . $path,
                    'link' => $request->existing_links[$id] ?? $imgModel->link,
                ]);
            }
        }
    } else if ($request->has('existing_links')) {
        // Update only links if no new image uploaded
        foreach ($request->existing_links as $id => $link) {
            $imgModel = $deal->images()->find($id);
            if ($imgModel) {
                $imgModel->update(['link' => $link]);
            }
        }
    }

    // Handle adding new images if any
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('deals', 'public');
            $deal->images()->create([
                'image_path' => '/storage/' . $path,
                'link' => $request->links[$index] ?? null,
            ]);
        }
    }

    return redirect()->route('deals.index')->with('success', 'Deal Updated Successfully');
}


    public function destroy(Deal $deal)
    {
        foreach ($deal->images as $image) {
            Storage::delete(str_replace('/storage/', 'public/', $image->image_path));
            $image->delete();
        }
        $deal->delete();

        return redirect()->route('deals.index')->with('success', 'Deal Deleted Successfully');
    }

    // API for frontend
public function apiLatestDeal()
{
    $deal = Deal::with('images')->latest()->first();
    return response()->json($deal);
}
}