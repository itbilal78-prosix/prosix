<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Navigation;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        $navigations = Navigation::orderBy('position')->get();
        return view('navigations.index', compact('navigations'));
    }

    public function create()
    {
        return view('navigations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'slug'     => 'required|string|unique:navigations,slug',
            'route'    => 'nullable|string',
            'position' => 'integer',
        ]);

        $validated['status']       = $request->has('status');
        $validated['has_dropdown'] = $request->has('has_dropdown');
        $validated['clickable']    = $request->has('clickable');

        $navigation = Navigation::create($validated);

        // ✅ Activity Log
        ActivityLogger::log('created', 'Navigation', $navigation->title, $navigation->id, [
            'title'  => $navigation->title,
            'slug'   => $navigation->slug,
            'status' => $navigation->status,
        ]);

        return redirect()->route('navigations.index')->with('success', 'Navigation created successfully');
    }

    public function edit(Navigation $navigation)
    {
        return view('navigations.edit', compact('navigation'));
    }

    public function update(Request $request, Navigation $navigation)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'slug'     => 'required|string|unique:navigations,slug,' . $navigation->id,
            'route'    => 'nullable|string',
            'position' => 'integer',
        ]);

        $validated['status']       = $request->has('status');
        $validated['has_dropdown'] = $request->has('has_dropdown');
        $validated['clickable']    = $request->has('clickable');

        $navigation->update($validated);

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Navigation', $navigation->title, $navigation->id, [
            'title'  => $request->title,
            'slug'   => $request->slug,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('navigations.index')->with('success', 'Navigation updated successfully');
    }

    public function destroy(Navigation $navigation)
    {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Navigation', $navigation->title, $navigation->id, [
            'title' => $navigation->title,
            'slug'  => $navigation->slug,
        ]);

        $navigation->delete();
        return redirect()->route('navigations.index')->with('success', 'Navigation deleted successfully');
    }

    public function toggleStatus(Navigation $navigation)
    {
        $navigation->status = !$navigation->status;
        $navigation->save();

        return redirect()->route('navigations.index')->with('success', 'Navigation status updated');
    }

    public function apiIndex()
    {
        $navigations = Navigation::where('status', 1)->orderBy('position')->get();
        return response()->json($navigations);
    }
}
