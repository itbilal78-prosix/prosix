<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlaceOrderAdminMail;
use App\Mail\PlaceOrderUserMail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\PlaceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PlaceOrderController extends Controller
{
    /**
     * Admin View
     */
    public function index()
    {
        $orders = PlaceOrder::latest()->paginate(20);
        return view('admin.placeorder', compact('orders'));
    }

    /**
     * Download PDF
     */
    public function downloadPdf(Request $request)
    {
        $ids = explode(',', $request->ids);
        $orders = PlaceOrder::whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('pdf.placeorder-pdf', compact('orders'))
                  ->setPaper('a4');

        return $pdf->download('place_orders.pdf');
    }

    /**
     * Store Order (MAIN FUNCTION)
     */
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

    // Save files
    $mockupPaths = $this->saveFiles($request, 'mockup_files', 'orders/mockup');
    $rosterPaths = $this->saveFiles($request, 'roster_files', 'orders/roster');
    $quotePaths  = $this->saveFiles($request, 'quote_files',  'orders/quote');

    // Save to DB
    $order = PlaceOrder::create([
        'user_id'       => Auth::id(),
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

    // ✅ BREVO API EMAIL (Artwork jaisa)
    try {
        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
            new \GuzzleHttp\Client(), $config
        );

        $htmlContent = view('emails.place-order-admin', [
            'order' => $order
        ])->render();

        $email = new \SendinBlue\Client\Model\SendSmtpEmail([
            'subject' => 'New Place Order Received',
            'sender'  => [
                'name'  => 'Prosix Sports',
                'email' => 'prosixsports@gmail.com'
            ],
            'to' => [
                ['email' => 'sales@prosix.com'],
                ['email' => $order->email],
            ],
            'htmlContent' => $htmlContent,
        ]);

        $apiInstance->sendTransacEmail($email);

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
     * User Orders
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
     * Update Status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        PlaceOrder::findOrFail($id)->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated.'
        ]);
    }

    /**
     * File Upload Helper
     */
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
}
