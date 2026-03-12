<?php

namespace App\Http\Controllers;

use App\Mail\ArtworkRequestMail;
use App\Models\ArtworkRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    // ✅ Logged-in user ka ID nikalo (guest ke liye null)
   $userId = null;
$token = $request->bearerToken();
\Log::info('Artwork Token: ' . ($token ?? 'NULL'));

if ($token) {
    $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
    \Log::info('Artwork User ID: ' . ($pat ? $pat->tokenable_id : 'NOT FOUND'));
    if ($pat) {
        $userId = $pat->tokenable_id;
    }
}

    $imagePaths = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/artwork'), $filename);
            $imagePaths[] = $filename;
        }
    }

    $artworkRequest = ArtworkRequest::create([
        'user_id'      => $userId,   // ✅ yeh add hua
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
    $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
        ->setApiKey('api-key', env('BREVO_API_KEY'));

    $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
        new \GuzzleHttp\Client(), $config
    );

    $htmlContent = view('emails.artwork-request', ['data' => $artworkRequest])->render();

    $email = new \SendinBlue\Client\Model\SendSmtpEmail([
        'subject'     => 'New Artwork Request Received',
        'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
        'to'          => [
            ['email' => 'designs@prosix.com'],
            ['email' => $artworkRequest->email],
        ],
        'htmlContent' => $htmlContent,
    ]);

    $apiInstance->sendTransacEmail($email);
} catch (\Exception $e) {
    \Log::error('Email failed: '.$e->getMessage());
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

        return $pdf->download('artwork-requests-'.now()->format('Ymd_His').'.pdf');
    }
}
