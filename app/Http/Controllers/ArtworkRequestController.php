<?php

namespace App\Http\Controllers;

use App\Mail\ArtworkRequestMail;
use App\Models\ArtworkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ArtworkRequestController extends Controller
{
    public function index()
    {
        $requests = ArtworkRequest::latest()->get();
        return view('admin.artwork', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email'    => 'required|email|max:255',
'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads/artwork'), $filename);
                $imagePaths[] = $filename;
            }
        }

        $artworkRequest = ArtworkRequest::create([
            'full_name'    => $request->fullName,
            'email'        => $request->email,
            'phone'        => $request->phone ?? null,
            'instagram'    => $request->instagram ?? null,
            'address'      => $request->address ?? null,
            'team_name'    => $request->teamName ?? null,
            'role'         => $request->role ?? null,
            'quantity'     => $request->quantity ?? null,
            'team_color'   => $request->teamColor ?? null,
            'home_away'    => $request->homeAway ?? null,
            'design_style' => $request->designStyle ?? null,
            'material'     => $request->material ?? null,
            'products'     => $request->products ?? [],
            'additional'   => $request->additional ?? null,
            'source'       => $request->source ?? null,
            'artwork_file' => json_encode($imagePaths),
        ]);

        try {
            Mail::to('designs@prosix.com')->send(new ArtworkRequestMail($artworkRequest));
            Mail::to($artworkRequest->email)->send(new ArtworkRequestMail($artworkRequest));
        } catch (\Exception $e) {
            \Log::error('Artwork email failed: '.$e->getMessage());
        }

        return response()->json(['message' => 'Artwork request saved successfully']);
    }

    public function downloadPdf(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $requests = ArtworkRequest::whereIn('id', array_filter($ids))->get();

        $pdf = Pdf::loadView('pdf.artwork-pdf', compact('requests'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions(['isRemoteEnabled' => true, 'chroot' => public_path()]);

        return $pdf->download('artwork-requests-' . now()->format('Ymd_His') . '.pdf');
    }
}
