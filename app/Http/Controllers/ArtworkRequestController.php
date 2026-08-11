<?php

namespace App\Http\Controllers;

use App\Models\ArtworkRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArtworkRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN PAGE
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $requests = ArtworkRequest::latest()->get();

        return view('admin.artwork', compact('requests'));
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC WEBSITE - STORE ARTWORK REQUEST
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $userId = null;

        $token = $request->bearerToken();

        if ($token) {
            $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

            if ($pat) {
                $userId = $pat->tokenable_id;
            }
        }

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename =
                    time() . '_' .
                    uniqid() . '.' .
                    $image->getClientOriginalExtension();

                $image->move(
                    public_path('uploads/artwork'),
                    $filename
                );

                $imagePaths[] = $filename;
            }
        }

        $artworkRequest = ArtworkRequest::create([
            'user_id'      => $userId,
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
            'is_read'      => false,
        ]);

        try {
            $config =
                \SendinBlue\Client\Configuration::getDefaultConfiguration()
                    ->setApiKey(
                        'api-key',
                        env('BREVO_API_KEY')
                    );

            $apiInstance =
                new \SendinBlue\Client\Api\TransactionalEmailsApi(
                    new \GuzzleHttp\Client(),
                    $config
                );

            $htmlContent = view(
                'emails.artwork-request',
                ['data' => $artworkRequest]
            )->render();

            $email =
                new \SendinBlue\Client\Model\SendSmtpEmail([
                    'subject' => 'New Artwork Request Received',

                    'sender' => [
                        'name'  => 'Prosix Sports',
                        'email' => 'prosixsports@gmail.com',
                    ],

                    'to' => [
                        [
                            'email' => 'designs@prosix.com',
                        ],
                        [
                            'email' => $artworkRequest->email,
                        ],
                    ],

                    'htmlContent' => $htmlContent,
                ]);

            $apiInstance->sendTransacEmail($email);
        } catch (\Exception $e) {
            Log::error(
                'Artwork request email failed: ' .
                $e->getMessage()
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Artwork request saved successfully',
            'data' => [
                'id' => $artworkRequest->id,
            ],
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */
    public function downloadPdf(Request $request)
    {
        $ids = explode(
            ',',
            $request->input('ids', '')
        );

        $requests = ArtworkRequest::whereIn(
            'id',
            array_filter($ids)
        )->get();

        $pdf = Pdf::loadView(
            'pdf.artwork-pdf',
            compact('requests')
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);

        return $pdf->download(
            'artwork-requests-' .
            now()->format('Ymd_His') .
            '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OLD ADMIN BADGE
    |--------------------------------------------------------------------------
    */
    public function unreadCount()
    {
        return response()->json([
            'count' => ArtworkRequest::where(
                'is_read',
                false
            )->count(),
        ]);
    }

    public function markAllRead()
    {
        ArtworkRequest::where(
            'is_read',
            false
        )->update([
            'is_read' => true,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CRM API - LIST ALL ARTWORK REQUESTS
    |--------------------------------------------------------------------------
    */
    public function crmIndex(Request $request)
    {
        if (!$this->crmAuthorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
                'data' => [],
            ], 401);
        }

        $requests = ArtworkRequest::query()
            ->latest()
            ->get()
            ->map(function (ArtworkRequest $artwork) {
                return [
                    'id' => $artwork->id,
                    'user_id' => $artwork->user_id,

                    'full_name' => $artwork->full_name,
                    'email' => $artwork->email,
                    'phone' => $artwork->phone,
                    'instagram' => $artwork->instagram,
                    'address' => $artwork->address,

                    'team_name' => $artwork->team_name,
                    'role' => $artwork->role,
                    'quantity' => $artwork->quantity,
                    'team_color' => $artwork->team_color,
                    'home_away' => $artwork->home_away,
                    'design_style' => $artwork->design_style,
                    'material' => $artwork->material,

                    'products' => $this->normalizeArray(
                        $artwork->products
                    ),

                    'additional' => $artwork->additional,
                    'source' => $artwork->source,

                    'artwork_files' =>
                        $this->crmArtworkFiles(
                            $artwork->artwork_file
                        ),

                    // compatibility names for current Vue file
                    'mockup_files' =>
                        $this->crmArtworkFiles(
                            $artwork->artwork_file
                        ),

                    'roster_files' => [],
                    'quote_files' => [],

                    'is_read' => (bool) $artwork->is_read,

                    'status' =>
                        $artwork->status
                        ?? 'pending',

                    'order_number' =>
                        'AR-' .
                        str_pad(
                            (string) $artwork->id,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'created_at' =>
                        optional(
                            $artwork->created_at
                        )->toISOString(),

                    'updated_at' =>
                        optional(
                            $artwork->updated_at
                        )->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CRM API - UNREAD COUNT
    |--------------------------------------------------------------------------
    */
    public function crmUnreadCount(Request $request)
    {
        if (!$this->crmAuthorized($request)) {
            return response()->json([
                'success' => false,
                'count' => 0,
            ], 401);
        }

        return response()->json([
            'success' => true,
            'count' => ArtworkRequest::where(
                'is_read',
                false
            )->count(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CRM API - MARK ONE REQUEST READ
    |--------------------------------------------------------------------------
    */
    public function crmMarkRead(
        Request $request,
        int $id
    ) {
        if (!$this->crmAuthorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
            ], 401);
        }

        $artwork = ArtworkRequest::findOrFail($id);

        if (!$artwork->is_read) {
            $artwork->update([
                'is_read' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' =>
                'Artwork Request marked as read.',
            'data' => [
                'id' => $artwork->id,
                'is_read' => true,
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $request =
            ArtworkRequest::findOrFail($id);

        $request->delete();

        return redirect()
            ->route('admin.artwork')
            ->with(
                'success',
                'Artwork request moved to Recycle Bin.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */
    private function crmAuthorized(
        Request $request
    ): bool {
        $expectedToken = (string) config(
            'services.crm.token',
            env('PROSIX_CRM_TOKEN')
        );

        $providedToken =
            (string) $request->bearerToken();

        return
            $expectedToken !== '' &&
            $providedToken !== '' &&
            hash_equals(
                $expectedToken,
                $providedToken
            );
    }

    private function crmArtworkFiles(
        $files
    ): array {
        if (is_string($files)) {
            $decoded = json_decode(
                $files,
                true
            );

            $files = is_array($decoded)
                ? $decoded
                : [];
        }

        if (!is_array($files)) {
            return [];
        }

        return collect($files)
            ->filter()
            ->map(function ($file) {
                if (is_array($file)) {
                    $filename =
                        $file['filename']
                        ?? $file['file_name']
                        ?? $file['name']
                        ?? $file['path']
                        ?? null;

                    $original =
                        $file['original']
                        ?? $file['original_name']
                        ?? $filename;
                } else {
                    $filename = (string) $file;
                    $original = basename(
                        $filename
                    );
                }

                if (!$filename) {
                    return null;
                }

                $basename =
                    basename($filename);

                return [
                    'filename' => $basename,
                    'original' =>
                        $original
                        ?: $basename,

                    'ext' =>
                        strtolower(
                            pathinfo(
                                $basename,
                                PATHINFO_EXTENSION
                            )
                        ),

                    'url' => asset(
                        'uploads/artwork/' .
                        $basename
                    ),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeArray(
        $value
    ): array {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode(
                $value,
                true
            );

            return is_array($decoded)
                ? $decoded
                : [];
        }

        return [];
    }
}
