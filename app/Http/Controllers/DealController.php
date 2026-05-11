<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::with(['images', 'banners'])->latest()->get();
        return view('deals.index', compact('deals'));
    }

    public function create()
    {
        return view('deals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'subtitle'          => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'button_text'       => 'nullable|string|max:100',
            'button_link'       => 'nullable|string|max:255',
            'images'            => 'nullable|array',
            'images.*'          => 'image|mimes:jpg,png,jpeg|max:2048',
            'links.*'           => 'nullable|url',
            'banners'           => 'nullable|array',
            'banners.*'         => 'image|mimes:jpg,png,jpeg|max:2048',
            'labels'            => 'nullable|array',
            'labels.*'          => 'nullable|string|max:50',
            'existing_labels'   => 'nullable|array',
            'existing_labels.*' => 'nullable|string|max:50',
        ]);

        $deal = Deal::create($request->only([
            'title', 'subtitle', 'description', 'button_text', 'button_link',
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('deals', 'public');
                $deal->images()->create([
                    'image_path' => '/storage/' . $path,
                    'link'       => $request->links[$index] ?? null,
                    'label'      => $request->labels[$index] ?? null,
                ]);
            }
        }

        if ($request->hasFile('banners')) {
            foreach ($request->file('banners') as $banner) {
                $path = $banner->store('deal_banners', 'public');
                $deal->banners()->create(['image_path' => '/storage/' . $path]);
            }
        }

        // ✅ Activity Log
        ActivityLogger::log('created', 'Deal', $deal->title, $deal->id, [
            'title'    => $deal->title,
            'subtitle' => $deal->subtitle,
        ]);

        return redirect()->route('deals.index')->with('success', 'Deal Added Successfully');
    }

    public function edit(Deal $deal)
    {
        $deal->load(['images', 'banners']);
        return view('deals.edit', compact('deal'));
    }

    public function update(Request $request, Deal $deal)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'subtitle'           => 'nullable|string|max:255',
            'description'        => 'nullable|string',
            'button_text'        => 'nullable|string|max:100',
            'button_link'        => 'nullable|string|max:255',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpg,png,jpeg|max:2048',
            'replace_images'     => 'nullable|array',
            'replace_images.*'   => 'image|mimes:jpg,png,jpeg|max:2048',
            'existing_links'     => 'nullable|array',
            'existing_links.*'   => 'nullable|url',
            'existing_labels'    => 'nullable|array',
            'existing_labels.*'  => 'nullable|string|max:50',
            'labels'             => 'nullable|array',
            'labels.*'           => 'nullable|string|max:50',
            'banners'            => 'nullable|array',
            'banners.*'          => 'image|mimes:jpg,png,jpeg|max:2048',
            'replace_banners'    => 'nullable|array',
            'replace_banners.*'  => 'image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $deal->update($request->only([
            'title', 'subtitle', 'description', 'button_text', 'button_link',
        ]));

        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $id) {
                $imgModel = $deal->images()->find($id);
                if ($imgModel) {
                    Storage::delete(str_replace('/storage/', 'public/', $imgModel->image_path));
                    $imgModel->delete();
                }
            }
        }

        if ($request->has('delete_banners')) {
            foreach ($request->delete_banners as $id) {
                $bannerModel = $deal->banners()->find($id);
                if ($bannerModel) {
                    Storage::delete(str_replace('/storage/', 'public/', $bannerModel->image_path));
                    $bannerModel->delete();
                }
            }
        }

        if ($request->hasFile('replace_images')) {
            foreach ($request->file('replace_images') as $id => $newImage) {
                $imgModel = $deal->images()->find($id);
                if ($imgModel) {
                    Storage::delete(str_replace('/storage/', 'public/', $imgModel->image_path));
                    $path = $newImage->store('deals', 'public');
                    $imgModel->update([
                        'image_path' => '/storage/' . $path,
                        'link'       => $request->existing_links[$id] ?? $imgModel->link,
                        'label'      => $request->existing_labels[$id] ?? $imgModel->label,
                    ]);
                }
            }
        }

        if ($request->has('existing_links')) {
            foreach ($request->existing_links as $id => $link) {
                $imgModel = $deal->images()->find($id);
                if ($imgModel) $imgModel->update(['link' => $link]);
            }
        }

        if ($request->has('existing_labels')) {
            foreach ($request->existing_labels as $id => $label) {
                $imgModel = $deal->images()->find($id);
                if ($imgModel) $imgModel->update(['label' => $label]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('deals', 'public');
                $deal->images()->create([
                    'image_path' => '/storage/' . $path,
                    'link'       => $request->links[$index] ?? null,
                    'label'      => $request->labels[$index] ?? null,
                ]);
            }
        }

        if ($request->hasFile('replace_banners')) {
            foreach ($request->file('replace_banners') as $id => $newBanner) {
                $bannerModel = $deal->banners()->find($id);
                if ($bannerModel) {
                    Storage::delete(str_replace('/storage/', 'public/', $bannerModel->image_path));
                    $path = $newBanner->store('deal_banners', 'public');
                    $bannerModel->update(['image_path' => '/storage/' . $path]);
                }
            }
        }

        if ($request->hasFile('banners')) {
            foreach ($request->file('banners') as $banner) {
                $path = $banner->store('deal_banners', 'public');
                $deal->banners()->create(['image_path' => '/storage/' . $path]);
            }
        }

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Deal', $deal->title, $deal->id, [
            'title'    => $request->title,
            'subtitle' => $request->subtitle,
        ]);

        return redirect()->route('deals.index')->with('success', 'Deal Updated Successfully');
    }

    public function destroy(Deal $deal)
    {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Deal', $deal->title, $deal->id, [
            'title'    => $deal->title,
            'subtitle' => $deal->subtitle,
        ]);

        foreach ($deal->images as $image) {
            $image->delete();
        }

        foreach ($deal->banners as $banner) {
            $banner->delete();
        }

        $deal->delete();

        return redirect()->route('deals.index')->with('success', 'Deal Deleted Successfully');
    }

    public function apiLatestDeal()
    {
        $deal = Deal::with(['images', 'banners'])->latest()->first();
        return response()->json($deal);
    }
}
