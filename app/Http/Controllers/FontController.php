<?php

namespace App\Http\Controllers;

use App\Models\Font;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class FontController extends Controller
{
    public function index()
    {
        $fonts = Font::latest()->get();
        return view('admin.fonts.index', compact('fonts'));
    }

    public function create()
    {
        return view('admin.fonts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|file',
        ]);

        $font       = new Font;
        $font->name = $request->name;

        if ($request->hasFile('file')) {
            $font->file = $request->file('file')->store('fonts', 'public');
        }

        $font->save();

        // ✅ Activity Log
        ActivityLogger::log('created', 'Font', $font->name, $font->id, [
            'name' => $font->name,
            'file' => $font->file,
        ]);

        return redirect()->route('fonts.index')->with('success', 'Font added successfully!');
    }

    public function show(Font $font)
    {
        return view('admin.fonts.show', compact('font'));
    }

    public function edit(Font $font)
    {
        return view('admin.fonts.edit', compact('font'));
    }

    public function update(Request $request, Font $font)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|file',
        ]);

        $font->name = $request->name;

        if ($request->hasFile('file')) {
            if ($font->file && file_exists(storage_path('app/public/' . $font->file))) {
                unlink(storage_path('app/public/' . $font->file));
            }
            $font->file = $request->file('file')->store('fonts', 'public');
        }

        $font->save();

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Font', $font->name, $font->id, [
            'name' => $font->name,
            'file' => $font->file,
        ]);

        return redirect()->route('fonts.index')->with('success', 'Font updated successfully!');
    }

    public function destroy(Font $font)
    {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Font', $font->name, $font->id, [
            'name' => $font->name,
            'file' => $font->file,
        ]);

        $font->delete();
        return redirect()->route('fonts.index')->with('success', 'Font deleted successfully!');
    }
}
