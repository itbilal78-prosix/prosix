<?php

namespace App\Http\Controllers;

use App\Models\Pattern;
use App\Helpers\ActivityLogger;
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
            'svg'  => 'required|mimes:svg,xml|max:2048',
        ]);

        $path = $request->file('svg')->store('patterns', 'public');

        $pattern = Pattern::create([
            'name'     => $request->name,
            'svg_path' => $path,
        ]);

        // ✅ Activity Log
        ActivityLogger::log('created', 'Pattern', $pattern->name, $pattern->id, [
            'name'     => $pattern->name,
            'svg_path' => $pattern->svg_path,
        ]);

        return redirect()->route('patterns.index')->with('success', 'Pattern created successfully!');
    }

    public function edit(Pattern $pattern)
    {
        return view('patterns.edit', compact('pattern'));
    }

    public function update(Request $request, Pattern $pattern)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'svg'  => 'nullable|mimes:svg,xml|max:2048',
        ]);

        if ($request->hasFile('svg')) {
            $path = $request->file('svg')->store('patterns', 'public');
            $pattern->svg_path = $path;
        }

        $pattern->name = $request->name;
        $pattern->save();

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Pattern', $pattern->name, $pattern->id, [
            'name'     => $pattern->name,
            'svg_path' => $pattern->svg_path,
        ]);

        return redirect()->route('patterns.index')->with('success', 'Pattern updated successfully!');
    }

    public function destroy(Pattern $pattern)
    {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Pattern', $pattern->name, $pattern->id, [
            'name'     => $pattern->name,
            'svg_path' => $pattern->svg_path,
        ]);

        $pattern->delete();

        return redirect()->route('patterns.index')->with('success', 'Pattern deleted successfully!');
    }
}
