<?php

namespace App\Http\Controllers;

use App\Models\Pattern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatternController extends Controller
{
    public function index()
    {
$patterns = Pattern::latest()->get();
        return view('patterns.index', compact('patterns'));
    }

    public function create()
    {
        return view('patterns.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'svg' => 'required|mimes:svg,xml|max:2048',
        ]);

        $path = $request->file('svg')->store('patterns', 'public');

        Pattern::create([
            'name' => $request->name,
            'svg_path' => $path,
        ]);

        return redirect()->route('patterns.index')
            ->with('success', 'Pattern created successfully!');
    }

    public function edit(Pattern $pattern)
    {
        return view('patterns.edit', compact('pattern'));
    }

    public function update(Request $request, Pattern $pattern)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'svg' => 'nullable|mimes:svg,xml|max:2048',
        ]);

        if ($request->hasFile('svg')) {
            // Optional: purana file delete kar sakte ho
            // Storage::disk('public')->delete($pattern->svg_path);

            $path = $request->file('svg')->store('patterns', 'public');
            $pattern->svg_path = $path;
        }

        $pattern->name = $request->name;
        $pattern->save();

        return redirect()->route('patterns.index')
            ->with('success', 'Pattern updated successfully!');
    }

    public function destroy(Pattern $pattern)
    {
        // Optional: file bhi delete karna chahiye storage se
        // Storage::disk('public')->delete($pattern->svg_path);

        $pattern->delete();

        return redirect()->route('patterns.index')
            ->with('success', 'Pattern deleted successfully!');
    }


}
