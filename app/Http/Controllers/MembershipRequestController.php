<?php

namespace App\Http\Controllers;

use App\Exports\MembershipRequestsExport;
use App\Models\MembershipRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        $userId = null;
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = str_replace('Bearer ', '', $authHeader);
            $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($pat) {
                $userId = $pat->tokenable_id;
            }
        }

        $data['user_id'] = $userId;
        $data['is_read'] = false;   // ✅ NAYA - default unread

        $membership = MembershipRequest::create($data);

        try {
            $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', env('BREVO_API_KEY'));

            $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
                new \GuzzleHttp\Client, $config
            );

            $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                'subject'     => 'New Membership Request Received',
                'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                'to'          => [
                    ['email' => 'sales@prosix.com'],
                    ['email' => $membership->email],
                ],
                'htmlContent' => view('emails.membership-request', ['data' => $membership])->render(),
            ]);

            $apiInstance->sendTransacEmail($email);
        } catch (\Exception $e) {
            \Log::error('Membership email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Membership request submitted successfully! We will contact you soon.',
        ], 200);
    }

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

    public function downloadPdf(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $requests = MembershipRequest::whereIn('id', array_filter($ids))->get();

        $pdf = Pdf::loadView('pdf.membership-pdf', compact('requests'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('membership-requests-' . now()->format('Ymd_His') . '.pdf');
    }

    // ✅ NAYA — unread count for sidebar badge
    public function unreadCount()
    {
        return response()->json([
            'count' => MembershipRequest::where('is_read', false)->count()
        ]);
    }

    // ✅ NAYA — mark all as read when admin visits the page
    public function markAllRead()
    {
        MembershipRequest::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
