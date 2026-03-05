<?php

namespace App\Http\Controllers;
 use App\Exports\MembershipRequestsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\MembershipRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMembershipRequest;  // ← yeh mail class banayenge
class MembershipRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'address'      => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'state'        => 'nullable|string|max:255',
            'zip'          => 'nullable|string|max:20',
            'phone'        => 'required|string|max:50',
            'role'         => 'required|string|max:50',
            'sports'       => 'required|array|min:1|max:2',
            'level'        => 'required|string|max:50',
        ]);

        // Save to database
        $membership = MembershipRequest::create($data);
        // Admin ko email bhejo
        try {
            Mail::to('sales@prosix.com')->send(new NewMembershipRequest($membership));
        } catch (\Exception $e) {
            // Email fail hone par bhi user ko success dikhao
            \Log::error('Membership email failed: ' . $e->getMessage());
        }


        return response()->json([
            'message' => 'Membership request submitted successfully! We will contact you soon.'
        ], 200);
    }

    // Admin ke liye all requests ki list
    public function index()
    {
        $requests = MembershipRequest::latest()->get();
        return view('admin.memberships', compact('requests'));
    }

public function download(Request $request)
{
    
    $ids = explode(',', $request->query('ids', ''));

    if (empty($ids)) {
        return redirect()->back()->with('error', 'No requests selected.');
    }

    $fileName = 'membership_requests_' . now()->format('Ymd_His') . '.xlsx';
    return Excel::download(new MembershipRequestsExport($ids), $fileName);
}







}
