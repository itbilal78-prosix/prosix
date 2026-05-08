<?php

namespace App\Http\Controllers;

use App\Models\ArtworkRequest;
use App\Models\Banner;

class RecycleBinController extends Controller
{
    public function index()
    {
        $artworks = ArtworkRequest::onlyTrashed()->latest('deleted_at')->get();
        $banners  = Banner::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.recycle-bin.index', compact('artworks', 'banners'));
    }

    public function restoreArtwork($id)
    {
        $artwork = ArtworkRequest::onlyTrashed()->findOrFail($id);
        $artwork->restore();

        return back()->with('success', 'Artwork request restored successfully.');
    }

    public function deleteArtwork($id)
    {
        $artwork = ArtworkRequest::onlyTrashed()->findOrFail($id);
        $artwork->forceDelete();

        return back()->with('success', 'Artwork request permanently deleted.');
    }

    public function restoreBanner($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        $banner->restore();

        return back()->with('success', 'Banner restored successfully.');
    }

    public function deleteBanner($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        $banner->forceDelete();

        return back()->with('success', 'Banner permanently deleted.');
    }
}
