<?php

// =====================================================
// STEP 4: Naya controller banao
// Command: php artisan make:controller UserRequestController
// Phir yeh poora code paste karo
// =====================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArtworkRequest;
use App\Models\MembershipRequest;

class UserRequestController extends Controller
{
    // ✅ User ki saari requests — dashboard ke liye
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $artworkRequests = ArtworkRequest::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'type'         => 'artwork',
                'full_name'    => $r->full_name,
                'email'        => $r->email,
                'team_name'    => $r->team_name,
                'design_style' => $r->design_style,
                'material'     => $r->material,
                'quantity'     => $r->quantity,
                'team_color'   => $r->team_color,
                'products'     => $r->products ?? [],
                'additional'   => $r->additional,
                'created_at'   => $r->created_at,
            ]);

        $membershipRequests = MembershipRequest::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'type'         => 'membership',
                'full_name'    => $r->name,
                'email'        => $r->email,
                'organization' => $r->organization,
                'sports'       => is_array($r->sports) ? implode(', ', $r->sports) : $r->sports,
                'level'        => $r->level,
                'additional'   => null,
                'created_at'   => $r->created_at,
            ]);

        $all = $artworkRequests
            ->concat($membershipRequests)
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $all,
        ]);
    }
}
