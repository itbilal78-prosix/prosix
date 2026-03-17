<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Mail\PlaceOrderAdminMail;
use App\Mail\PlaceOrderUserMail;
use App\Models\PlaceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PlaceOrderController extends Controller
{
    /**
     * Submit place order
     * POST /api/place-order
     */
    public function index()
{
    $orders = PlaceOrder::latest()->paginate(20);
return view('admin.placeorder', compact('orders'));}


public function downloadPdf(Request $request)
{
    $ids = explode(',', $request->ids);
    $orders = PlaceOrder::whereIn('id', $ids)->get();

 $pdf = Pdf::loadView('pdf.placeorder-pdf', compact('orders'))
          ->setPaper('a4');

    return $pdf->download('place_orders.pdf');
}
public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email',
            'order_number'  => 'required|string',
            'order_date'    => 'required|string',
            'delivery_date' => 'nullable|string',
            'sales_rep'     => 'nullable|string|max:255',
            'team_colors'   => 'nullable|string',
            'notes'         => 'nullable|string',
            'mockup_files'  => 'nullable|array',
            'mockup_files.*'=> 'file|max:20480',
            'roster_files'  => 'nullable|array',
            'roster_files.*'=> 'file|max:20480',
            'quote_files'   => 'nullable|array',
            'quote_files.*' => 'file|max:20480',
        ]);

        // ── Save files ───────────────────────────────────────
        $mockupPaths = $this->saveFiles($request, 'mockup_files', 'orders/mockup');
        $rosterPaths = $this->saveFiles($request, 'roster_files', 'orders/roster');
        $quotePaths  = $this->saveFiles($request, 'quote_files',  'orders/quote');

        // ── Save to DB ───────────────────────────────────────
        $order = PlaceOrder::create([
            'user_id'       => Auth::id(),         // null if not logged in
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'order_number'  => $request->order_number,
            'order_date'    => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'sales_rep'     => $request->sales_rep,
            'team_colors'   => $request->team_colors,
            'notes'         => $request->notes,
            'mockup_files'  => $mockupPaths,
            'roster_files'  => $rosterPaths,
            'quote_files'   => $quotePaths,
            'status'        => 'pending',
        ]);

        // ── Email to ADMIN (same as ArtworkRequest pattern) ──
        $adminEmail = env('ADMIN_EMAIL', 'sales@prosix.com');
        try {
            Mail::to($adminEmail)->send(new PlaceOrderAdminMail($order));
        } catch (\Exception $e) {
            \Log::error('PlaceOrder Admin Mail Error: ' . $e->getMessage());
        }

        // ── Confirmation email to USER (form ki email pe) ────
        try {
            Mail::to($order->email)
                ->send(new PlaceOrderUserMail($order));
        } catch (\Exception $e) {
            \Log::error('PlaceOrder User Mail Error: ' . $e->getMessage());
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Order placed successfully!',
            'order_number' => $order->order_number,
        ], 201);
    }

    /**
     * User dashboard — My Requests
     * GET /api/user/my-requests
     */
    public function myOrders()
    {
        $orders = PlaceOrder::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(fn($o) => [
                'id'            => $o->id,
                'order_number'  => $o->order_number,
                'full_name'     => $o->full_name,
                'email'         => $o->email,
                'order_date'    => $o->order_date,
                'delivery_date' => $o->delivery_date,
                'sales_rep'     => $o->sales_rep,
                'team_colors'   => $o->team_colors,
                'notes'         => $o->notes,
                'status'        => $o->status,
                'mockup_files'  => $o->mockup_files ?? [],
                'roster_files'  => $o->roster_files ?? [],
                'quote_files'   => $o->quote_files  ?? [],
                'created_at'    => $o->created_at->format('M d, Y'),
            ]);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Admin panel — all orders
     * GET /api/admin/place-orders
     */
    public function adminIndex()
    {
        $orders = PlaceOrder::latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Admin — update status
     * PUT /api/admin/place-orders/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,processing,completed,cancelled']);
        PlaceOrder::findOrFail($id)->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    // ── Helper ───────────────────────────────────────────────
    private function saveFiles(Request $request, string $field, string $folder): array
    {
        $paths = [];
        if ($request->hasFile($field)) {
            foreach ($request->file($field) as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/' . $folder), $filename);
                $paths[] = $filename;
            }
        }
        return $paths;
    }
}
