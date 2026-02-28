<?php

namespace App\Http\Controllers;

// ✅ YE ADD KARO
use App\Models\Flipbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FlipbookController extends Controller
{
    // LIST
    public function index()
    {
        $flipbooks = Flipbook::latest()->get();

        return view('admin.flipbooks.index', compact('flipbooks'));
    }

    // CREATE FORM
    public function create()
    {
        return view('admin.flipbooks.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:51200',
        ]);

        $path = $request->file('file')->store('flipbooks', 'public');

        Flipbook::create([
            'title' => $request->title,
            'file_path' => $path,
        ]);

        return redirect()
            ->route('admin.flipbooks.index')
            ->with('success', 'Flipbook created!');
    }

    // EDIT
    public function edit(Flipbook $flipbook)
    {
        return view('admin.flipbooks.edit', compact('flipbook'));
    }

    // UPDATE
    public function update(Request $request, Flipbook $flipbook)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf|max:51200',
        ]);

        if ($request->hasFile('file')) {

            // delete old file
            Storage::disk('public')->delete($flipbook->file_path);

            $path = $request->file('file')
                ->store('flipbooks', 'public');

            $flipbook->file_path = $path;
        }

        $flipbook->title = $request->title;
        $flipbook->save();

        return redirect()
            ->route('admin.flipbooks.index')
            ->with('success', 'Flipbook updated!');
    }

    // DELETE
    public function destroy(Flipbook $flipbook)
    {
        Storage::disk('public')->delete($flipbook->file_path);
        $flipbook->delete();

        return redirect()->route('admin.flipbooks.index')
            ->with('success', 'Flipbook deleted!');
    }

    public function show(Flipbook $flipbook)
    {
        return view('admin.flipbooks.show', compact('flipbook'));
    }

    public function apiIndex()
    {
        $flipbooks = Flipbook::latest()->get();

        return response()->json($flipbooks);
    }

    public function apiShow($id)
    {
        $flipbook = Flipbook::findOrFail($id);

        return response()->json($flipbook);
    }
}
