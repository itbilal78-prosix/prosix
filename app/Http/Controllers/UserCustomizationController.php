<?php

namespace App\Http\Controllers;

use App\Models\CustomizerModel;
use App\Models\UserCustomization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCustomizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Sirf logged-in users hi access kar sakein
    }

    /**
     * Show user's saved custom designs (My Designs page)
     */
    public function index()
    {
        $designs = UserCustomization::where('user_id', Auth::id())
            ->with('model') // Base model ka title, thumbnail etc load karo
            ->latest()
            ->get();

        return view('user.my-designs', compact('designs'));
    }

    /**
     * Store NEW user customization
     * (Jab user pehli baar save karta hai)
     */
    public function store(Request $request)
    {
        $request->validate([
            'model_id'        => 'required|exists:customizer_models,id',
            'color_changes'   => 'required|json',
            'pattern_changes' => 'nullable|json',
            'name'            => 'nullable|string|max:100',
        ]);

        $customization = UserCustomization::create([
            'user_id'             => Auth::id(),
            'customizer_model_id' => $request->model_id,
            'name'                => $request->name ?? 'Design ' . now()->format('d M Y H:i'),
            'color_changes'       => $request->color_changes,        // yeh JSON string hai
            'pattern_changes'     => $request->pattern_changes ?? null,
        ]);

        return response()->json([
            'success'          => true,
            'customization_id' => $customization->id,
            'message'          => 'Your design saved successfully!'
        ]);
    }

    /**
     * Update EXISTING user customization
     * (Jab user apna purana saved design edit karke save karta hai)
     */
    public function update(Request $request, $id)
    {
        $customization = UserCustomization::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'color_changes'   => 'json',
            'pattern_changes' => 'nullable|json',
            'name'            => 'nullable|string|max:100',
        ]);

        $customization->update($request->only([
            'color_changes',
            'pattern_changes',
            'name'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Your design updated successfully!'
        ]);
    }

    /**
     * Delete a saved design
     */
    public function destroy($id)
    {
        $customization = UserCustomization::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $customization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Design deleted successfully!'
        ]);
    }

    /**
     * Optional: Load a saved design (for edit mode)
     */
    public function show($id)
    {
        $customization = UserCustomization::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('model')
            ->firstOrFail();

        $colors = \App\Models\Color::all(); // agar color palette chahiye

        return view('customize', compact('customization', 'colors'))
            ->with([
                'model'         => $customization->model,
                'isSavedDesign' => true
            ]);
    }
    // UserCustomizationController.php
public function designs()
{
    $designs = UserCustomization::where('user_id', auth()->id())
        ->with('model:id,title,thumbnail')
        ->select('id', 'name', 'model_id', 'created_at')
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'data' => $designs
    ]);
}
public function dashboardData()
{
    $total = UserCustomization::where('user_id', auth()->id())->count();
    $recent = UserCustomization::where('user_id', auth()->id())
        ->with('model:id,title,thumbnail')
        ->select('id', 'name', 'model_id', 'created_at')
        ->latest()
        ->take(5)
        ->get();

    return response()->json([
        'success' => true,
        'data' => [
            'stats' => [
                'total_designs' => $total,
                // aur kuch stats add kar sakte ho
            ],
            'recent_designs' => $recent
        ]
    ]);
}
}