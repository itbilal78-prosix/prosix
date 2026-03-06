<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    public function index()
{
    $colors = Color::all(); // gets all colors from DB
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
            'code' => 'required|string', // hex color code
        ]);

        Color::create($request->all());

        return redirect()->route('colors.index')->with('success', 'Color added successfully!');
    }
    public function edit(Color $color)
{
    return view('colors.edit', compact('color'));
}
public function apiIndex()
{
    return response()->json(Color::all());
}
public function update(Request $request, Color $color)
{
    $request->validate([
        'name' => 'required|string',
        'code' => 'required|string',
    ]);

    $color->update($request->all());

    return redirect()->route('colors.index')->with('success', 'Color updated successfully!');
}

public function destroy(Color $color)
{
    $color->delete();
    return redirect()->route('colors.index')->with('success', 'Color deleted successfully!');
}

}
