<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use App\Helpers\ActivityLogger;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::all();
        return view('colors.index', compact('colors'));
    }

    public function create()
    {
        return view('colors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
        ]);

        $color = Color::create($request->all());

        // ✅ Activity Log
        ActivityLogger::log('created', 'Color', $color->name, $color->id, [
            'name' => $color->name,
            'code' => $color->code,
        ]);

        return redirect()->route('colors.index')->with('success', 'Color added successfully!');
    }

    public function edit(Color $color)
    {
        return view('colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
        ]);

        $color->update($request->all());

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Color', $color->name, $color->id, [
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('colors.index')->with('success', 'Color updated successfully!');
    }

    public function destroy(Color $color)
    {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Color', $color->name, $color->id, [
            'name' => $color->name,
            'code' => $color->code,
        ]);

        $color->delete();
        return redirect()->route('colors.index')->with('success', 'Color deleted successfully!');
    }

    public function apiIndex()
    {
        return response()->json(Color::all());
    }
}
