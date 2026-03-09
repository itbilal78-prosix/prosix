<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Navigation;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    // 🔹 Blade index page
    public function index()
    {
        $navigations = Navigation::orderBy('position')->get();
        return view('navigations.index', compact('navigations'));
    }

    // 🔹 Show create page
    public function create()
    {
        return view('navigations.create'); // ye view me form hoga
    }

    // 🔹 Store new navigation
   public function store(Request $request)
{
    $validated = $request->validate([
        'title'        => 'required|string|max:255',
        'slug'         => 'required|string|unique:navigations,slug',
        'route'        => 'nullable|string',
        'position'     => 'integer',
    ]);

    // Checkbox values
    $validated['status'] = $request->has('status');         // true if checked
    $validated['has_dropdown'] = $request->has('has_dropdown');
    $validated['clickable'] = $request->has('clickable');

    Navigation::create($validated);

    return redirect()->route('navigations.index')
                     ->with('success', 'Navigation created successfully');
}


    // 🔹 Show edit page
    public function edit(Navigation $navigation)
    {
        return view('navigations.edit', compact('navigation'));
    }

    // 🔹 Update navigation
  public function update(Request $request, Navigation $navigation)
{
    $validated = $request->validate([
        'title'        => 'required|string|max:255',
        'slug'         => 'required|string|unique:navigations,slug,' . $navigation->id,
        'route'        => 'nullable|string',
        'position'     => 'integer',
    ]);

    $validated['status'] = $request->has('status');
    $validated['has_dropdown'] = $request->has('has_dropdown');
    $validated['clickable'] = $request->has('clickable');

    $navigation->update($validated);

    return redirect()->route('navigations.index')
                     ->with('success', 'Navigation updated successfully');
}


    // 🔹 Delete
    public function destroy(Navigation $navigation)
    {
        $navigation->delete();
        return redirect()->route('navigations.index')
                         ->with('success', 'Navigation deleted successfully');
    }

    // 🔹 Toggle status
    public function toggleStatus(Navigation $navigation)
    {
        $navigation->status = !$navigation->status;
        $navigation->save();

        return redirect()->route('navigations.index')
                         ->with('success', 'Navigation status updated');
    }

    // 🔹 API index for frontend
    public function apiIndex()
    {
        $navigations = Navigation::where('status', 1)
            ->orderBy('position')
            ->get();

        return response()->json($navigations);
    }
}
