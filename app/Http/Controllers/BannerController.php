<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position', 'asc')->get();
        return view('banner.index', compact('banners'));
    }

    public function create()
    {
        return view('banner.create');
    }

    public function store(Request $request)
    {
        $maxPosition = Banner::max('position') ?? 0;

        $request->validate([
            'title'                   => 'required|string|max:255',
            'button_text'             => 'nullable|string|max:100',
            'button_link'             => 'nullable|url',
            'background_image'        => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'mobile_background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'png_image'               => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:51200',
        ], [
            'png_image.max'               => 'PNG image must not be greater than 10MB.',
            'background_image.max'        => 'Background image must not be greater than 10MB.',
            'mobile_background_image.max' => 'Mobile image must not be greater than 10MB.',
        ]);

        $bgPath = $request->hasFile('background_image')
            ? $request->background_image->store('banners/backgrounds', 'public')
            : null;

        $mobileBgPath = $request->hasFile('mobile_background_image')
            ? $request->mobile_background_image->store('banners/backgrounds', 'public')
            : null;

        $pngPath = $request->hasFile('png_image')
            ? $request->png_image->store('banners/png', 'public')
            : null;

        $banner = Banner::create([
            'title'                   => $request->title,
            'button_text'             => $request->button_text,
            'button_link'             => $request->button_link,
            'background_image'        => $bgPath,
            'mobile_background_image' => $mobileBgPath,
            'png_image'               => $pngPath,
            'position'                => $maxPosition + 1,
        ]);

        // ✅ Activity Log
        ActivityLogger::log('created', 'Banner', $banner->title, $banner->id, [
            'title'       => $banner->title,
            'button_text' => $banner->button_text,
            'button_link' => $banner->button_link,
        ]);

        return redirect()->route('banners.index')->with('success', 'Banner created successfully!');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('banner.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title'                   => 'required|string|max:255',
            'button_text'             => 'nullable|string|max:100',
            'button_link'             => 'nullable|url',
            'background_image'        => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'mobile_background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'png_image'               => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ]);

        $banner->title       = $request->title;
        $banner->button_text = $request->button_text;
        $banner->button_link = $request->button_link;

        if ($request->hasFile('background_image')) {
            if ($banner->background_image) Storage::disk('public')->delete($banner->background_image);
            $banner->background_image = $request->background_image->store('banners/backgrounds', 'public');
        }

        if ($request->hasFile('mobile_background_image')) {
            if ($banner->mobile_background_image) Storage::disk('public')->delete($banner->mobile_background_image);
            $banner->mobile_background_image = $request->mobile_background_image->store('banners/backgrounds', 'public');
        }

        if ($request->has('remove_mobile_image')) {
            if ($banner->mobile_background_image) Storage::disk('public')->delete($banner->mobile_background_image);
            $banner->mobile_background_image = null;
        }

        if ($request->hasFile('png_image')) {
            if ($banner->png_image) Storage::disk('public')->delete($banner->png_image);
            $banner->png_image = $request->png_image->store('banners/png', 'public');
        }

        $banner->save();

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Banner', $banner->title, $banner->id, [
            'title'       => $banner->title,
            'button_text' => $banner->button_text,
            'button_link' => $banner->button_link,
        ]);

        return redirect()->route('banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Banner', $banner->title, $banner->id, [
            'title'            => $banner->title,
            'background_image' => $banner->background_image
                                    ? asset('storage/' . $banner->background_image)
                                    : null,
        ]);

        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner moved to Recycle Bin.');
    }

    public function apiIndex()
    {
        return Banner::orderBy('position', 'asc')->get()->map(function ($banner) {
            return [
                'title'                 => $banner->title,
                'buttonText'            => $banner->button_text,
                'buttonLink'            => $banner->button_link,
                'backgroundImage'       => $banner->background_image
                                            ? asset('storage/' . $banner->background_image)
                                            : null,
                'mobileBackgroundImage' => $banner->mobile_background_image
                                            ? asset('storage/' . $banner->mobile_background_image)
                                            : null,
                'pngImage'              => $banner->png_image
                                            ? asset('storage/' . $banner->png_image)
                                            : null,
            ];
        });
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('order') as $item) {
            Banner::where('id', $item['id'])->update(['position' => $item['position']]);
        }
        return response()->json(['status' => 'success']);
    }
}
