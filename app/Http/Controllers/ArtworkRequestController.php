<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;   // ✅ REQUIRED
use App\Models\ArtworkRequest;
use App\Mail\ArtworkRequestMail;
use Illuminate\Support\Facades\Mail;


class ArtworkRequestController extends Controller
{
      public function index()
    {
        $requests = ArtworkRequest::latest()->paginate(15);
        return view('admin.artwork', compact('requests'));
    }
public function store(Request $request)
{
    // Validate (optional but recommended)
    $request->validate([
        'fullName' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // each file max 2MB
    ]);

    $imagePaths = [];

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/artwork'), $filename);
            $imagePaths[] = $filename;
        }
    }

    $artworkRequest = ArtworkRequest::create([
        'full_name' => $request->fullName,
        'email' => $request->email,
        'phone' => $request->phone ?? null,
        'instagram' => $request->instagram ?? null,
        'address' => $request->address ?? null,
        'team_name' => $request->teamName ?? null,
        'role' => $request->role ?? null,
        'quantity' => $request->quantity ?? null,
        'team_color' => $request->teamColor ?? null,
        'home_away' => $request->homeAway ?? null,
        'design_style' => $request->designStyle ?? null,
        'material' => $request->material ?? null,
        'products' => $request->products ?? [],
        'additional' => $request->additional ?? null,
        'source' => $request->source ?? null,
        'artwork_file' => json_encode($imagePaths), // save image filenames as JSON
    ]);

    return response()->json(['message' => 'Artwork request saved successfully']);
}


}
