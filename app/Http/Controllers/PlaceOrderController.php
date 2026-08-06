<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\PlaceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;   // ← YEH NAYA HAI
use Illuminate\Support\Str;

class PlaceOrderController extends Controller
{
    /**
     * Admin View
     */
public function index()
{
$orders = PlaceOrder::latest()->get();

    return view('admin.placeorder', compact('orders'));
}

    /**
     * Download PDF (bulk)
     */
    public function downloadPdf(Request $request)
    {
        $ids    = explode(',', $request->ids);
        $orders = PlaceOrder::whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('pdf.placeorder-pdf', compact('orders'))
                  ->setPaper('a4');

        return $pdf->download('place_orders.pdf');
    }

    /**
     * Store Order
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email',
            'phone'         => 'nullable|string|max:30',
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

$userId = $request->user()->id;

        $mockupPaths = $this->saveFiles($request, 'mockup_files', 'orders/mockup');
        $rosterPaths = $this->saveFiles($request, 'roster_files', 'orders/roster');
        $quotePaths  = $this->saveFiles($request, 'quote_files',  'orders/quote');

        $order = PlaceOrder::create([
            'user_id'       => $userId,
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
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
'is_read'       => false,
]);

        // Send order confirmation email via Brevo
        try {
            $this->sendOrderEmail($order, 'new');
        } catch (\Exception $e) {
            \Log::error('PlaceOrder Email Error: ' . $e->getMessage());
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Order placed successfully!',
            'order_number' => $order->order_number,
        ], 201);
    }

    /**
     * Update Status — and notify customer
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order = PlaceOrder::findOrFail($id);
        $order->update(['status' => $request->status]);

        // Send status update email to customer
        try {
            $this->sendStatusEmail($order);
        } catch (\Exception $e) {
            \Log::error('PlaceOrder Status Email Error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated and customer notified.'
        ]);
    }

    /**
     * User's own orders
     */
    public function myOrders()
    {
        $userId = Auth::guard('sanctum')->id();

        $orders = PlaceOrder::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn($o) => [
                'id'            => $o->id,
                'order_number'  => $o->order_number,
                'full_name'     => $o->full_name,
                'email'         => $o->email,
                'phone'         => $o->phone,
                'order_date'    => $o->order_date,
                'delivery_date' => $o->delivery_date,
                'sales_rep'     => $o->sales_rep,
                'team_colors'   => $o->team_colors,
                'notes'         => $o->notes,
                'status'        => $o->status,
                'mockup_files'  => $o->mockup_files ?? [],
                'roster_files'  => $o->roster_files ?? [],
                'quote_files'   => $o->quote_files ?? [],
                'created_at'    => $o->created_at->format('M d, Y'),
            ]);

        return response()->json([
            'success' => true,
            'data'    => $orders
        ]);
    }

    /**
     * Admin API Orders
     */
    public function adminIndex()
    {
        $orders = PlaceOrder::latest()->paginate(20);
        return response()->json([
            'success' => true,
            'data'    => $orders
        ]);
    }

    /**
     * Download Single Order PDF
     */
    public function downloadSinglePdf($id)
    {
        $order = PlaceOrder::findOrFail($id);

        $pdf = Pdf::loadView('pdf.placeorder-pdf', ['orders' => collect([$order])])
                  ->setPaper('a4');

        return $pdf->download('order-' . $order->order_number . '.pdf');
    }

    /**
     * File Upload Helper — stores original filename too
     */
    private function saveFiles(Request $request, string $field, string $folder): array
    {
        $paths = [];

        if ($request->hasFile($field)) {
            foreach ($request->file($field) as $file) {
                $ext      = $file->getClientOriginalExtension();
                $original = $file->getClientOriginalName();
                $uuid     = Str::uuid() . '.' . $ext;
                $file->move(public_path('uploads/' . $folder), $uuid);

                // Store both UUID filename and original name
                $paths[] = [
                    'filename' => $uuid,
                    'original' => $original,
                    'ext'      => strtolower($ext),
                ];
            }
        }

        return $paths;
    }

    /**
     * Send new order email (admin + customer)
     */
   private function sendOrderEmail(PlaceOrder $order, string $type = 'new')
{
    $html = view('emails.place-order-admin', ['order' => $order])->render();

    Http::withHeaders([
        'api-key'      => env('BREVO_API_KEY'),
        'Content-Type' => 'application/json',
    ])->post('https://api.brevo.com/v3/smtp/email', [
        'subject'     => 'New Place Order Received — ' . $order->order_number,
        'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
        'to'          => [
            ['email' => 'sales@prosix.com'],
            ['email' => $order->email, 'name' => $order->full_name],
        ],
        'htmlContent' => $html,
    ]);
}

    /**
     * Send status update email to customer
     */
   private function sendStatusEmail(PlaceOrder $order)
{
    $html = view('emails.place-order-status', ['order' => $order])->render();

    Http::withHeaders([
        'api-key'      => env('BREVO_API_KEY'),
        'Content-Type' => 'application/json',
    ])->post('https://api.brevo.com/v3/smtp/email', [
        'subject'     => 'Your Order #' . $order->order_number . ' is now ' . ucfirst($order->status),
        'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
        'to'          => [
            ['email' => $order->email, 'name' => $order->full_name],
        ],
        'htmlContent' => $html,
    ]);
}


/**
 * Track PlaceOrder by order_number
 */
public function trackOrder(Request $request)
{
    $tracking = trim($request->query('tracking', ''));

    if (!$tracking) {
        return response()->json(['message' => 'Tracking number required'], 422);
    }

    // ✅ PlaceOrder table mein search karo
    $order = PlaceOrder::where('order_number', $tracking)->first();

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    return response()->json([
        'id'            => $order->id,
        'order_number'  => $order->order_number,
        'status'        => $order->status,       // pending/processing/completed/cancelled
        'payment_method'=> 'Invoice',
        'payment_status'=> 'pending',
        'total'         => 0,
        'items'         => [],
        'shipping_name' => $order->full_name,
        'shipping_phone'=> $order->phone,
        'shipping_city' => '',
        'shipping_address' => '',
        'courier_name'  => null,
        'tracking_number'  => null,
        'dispatch_date' => null,
        'delivered_date'=> null,
        'admin_notes'   => $order->notes,
        'created_at'    => $order->created_at,
    ]);
}

    /**
     * CRM API: return all Prosix Place Orders.
     */
    public function crmIndex(Request $request)
    {
        if (!$this->crmAuthorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
            ], 401);
        }

        $orders = PlaceOrder::query()
            ->latest()
            ->get()
            ->map(function (PlaceOrder $order) {
                return [
                    'id'            => $order->id,
                    'user_id'       => $order->user_id,
                    'full_name'     => $order->full_name,
                    'email'         => $order->email,
                    'phone'         => $order->phone,
                    'order_number'  => $order->order_number,
                    'order_date'    => $order->order_date,
                    'delivery_date' => $order->delivery_date,
                    'sales_rep'     => $order->sales_rep,
                    'team_colors'   => $order->team_colors,
                    'notes'         => $order->notes,
                    'status'        => $order->status,
                    'is_read'       => (bool) $order->is_read,

                    'mockup_files' => $this->crmFiles(
                        $order->mockup_files,
                        'mockup'
                    ),

                    'roster_files' => $this->crmFiles(
                        $order->roster_files,
                        'roster'
                    ),

                    'quote_files' => $this->crmFiles(
                        $order->quote_files,
                        'quote'
                    ),

                    'created_at' => optional(
                        $order->created_at
                    )->toISOString(),

                    'updated_at' => optional(
                        $order->updated_at
                    )->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    /**
     * CRM API: unread Place Orders count.
     */
    public function crmUnreadCount(Request $request)
    {
        if (!$this->crmAuthorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
                'count'   => 0,
            ], 401);
        }

        return response()->json([
            'success' => true,
            'count'   => PlaceOrder::query()
                ->where('is_read', false)
                ->count(),
        ]);
    }

    /**
     * CRM API: mark one Place Order as read.
     */
    public function crmMarkRead(Request $request, int $id)
    {
        if (!$this->crmAuthorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
            ], 401);
        }

        $order = PlaceOrder::query()->findOrFail($id);

        if (!$order->is_read) {
            $order->update([
                'is_read' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Place Order marked as read.',
            'data'    => [
                'id'      => $order->id,
                'is_read' => true,
            ],
        ]);
    }

    /**
     * CRM bearer token verification.
     */
    private function crmAuthorized(Request $request): bool
    {
        $expectedToken = (string) config(
            'services.crm.token',
            env('PROSIX_CRM_TOKEN')
        );

        $providedToken = (string) $request->bearerToken();

        return $expectedToken !== ''
            && $providedToken !== ''
            && hash_equals(
                $expectedToken,
                $providedToken
            );
    }

    /**
     * Prepare uploaded files for CRM.
     */
    private function crmFiles(
        $files,
        string $folder
    ): array {
        if (is_string($files)) {
            $files = json_decode($files, true);
        }

        if (!is_array($files)) {
            return [];
        }

        return collect($files)
            ->map(function ($file) use ($folder) {
                if (is_string($file)) {
                    $filename = basename($file);

                    return [
                        'filename' => $filename,
                        'original' => $filename,
                        'url'      => asset(
                            "uploads/orders/{$folder}/{$filename}"
                        ),
                    ];
                }

                $filename =
                    $file['filename'] ??
                    $file['file_name'] ??
                    $file['name'] ??
                    null;

                if (!$filename) {
                    return null;
                }

                return [
                    'filename' => basename($filename),

                    'original' =>
                        $file['original'] ??
                        $file['original_name'] ??
                        basename($filename),

                    'ext' =>
                        $file['ext'] ??
                        strtolower(
                            pathinfo(
                                $filename,
                                PATHINFO_EXTENSION
                            )
                        ),

                    'url' => asset(
                        'uploads/orders/' .
                        $folder . '/' .
                        basename($filename)
                    ),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }


public function unreadCount()
{
    return response()->json([
        'count' => PlaceOrder::where('is_read', false)->count()
    ]);
}
public function markAllRead()
{
    PlaceOrder::where('is_read', false)
        ->update([
            'is_read' => true
        ]);

    return response()->json([
        'success' => true
    ]);
}
public function destroy($id)
{
    $order = PlaceOrder::findOrFail($id);

    // Delete uploaded files
    $folders = [
        'mockup_files' => 'uploads/orders/mockup/',
        'roster_files' => 'uploads/orders/roster/',
        'quote_files'  => 'uploads/orders/quote/',
    ];

    foreach ($folders as $column => $folder) {

        $files = $order->$column;

        if (is_string($files)) {
            $files = json_decode($files, true);
        }

        if (!is_array($files)) {
            continue;
        }

        foreach ($files as $file) {

            $filename = is_array($file)
                ? ($file['filename'] ?? null)
                : $file;

            if (!$filename) {
                continue;
            }

            $path = public_path($folder . basename($filename));

            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    $order->delete();

    return response()->json([
        'success' => true,
        'message' => 'Order deleted successfully.'
    ]);
}
}
