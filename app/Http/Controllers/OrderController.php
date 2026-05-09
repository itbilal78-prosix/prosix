<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;   // ✅ NEW
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cart'                    => 'required|array|min:1',
                'cart.*.id'               => 'required|integer',
                'cart.*.name'             => 'required|string',
                'cart.*.price'            => 'required|numeric',
                'cart.*.quantity'         => 'required|integer|min:1',
                'cart.*.size'             => 'nullable|string',
                'checkout.name'           => 'required|string|max:255',
                'checkout.phone'          => 'required|string|max:20',
                'checkout.email'          => 'nullable|email|max:255',
                'checkout.address'        => 'required|string',
                'checkout.city'           => 'required|string',
                'checkout.province'       => 'nullable|string',
                'checkout.postalCode'     => 'nullable|string',
                'checkout.country'        => 'nullable|string',
                'checkout.deliveryDays'   => 'required|string',
                'checkout.paymentMethod'  => 'required|string|in:stripe',
            ]);

            $subtotal = collect($request->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
            $shipping = 0;
            foreach ($request->cart as $item) {
                if (!($item['shipping_enabled'] ?? false)) continue;
                $cost      = $item['shipping_cost'] ?? 0;
                $freeAbove = $item['free_shipping_above'] ?? null;
                if ($freeAbove && $subtotal >= $freeAbove) continue;
                $shipping += $cost * $item['quantity'];
            }
            $total = round($subtotal + $shipping, 2);

            $stripePaymentIntentId = null;
            if ($request->checkout['paymentMethod'] === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));
                $intent = PaymentIntent::create([
                    'amount'             => (int) ($total * 100),
                    'currency'           => 'usd',
                    'payment_method'     => $request->checkout['stripeToken'],
                    'confirm'            => true,
                    'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
                ]);
                if ($intent->status !== 'succeeded') {
                    return response()->json(['success' => false, 'message' => 'Payment failed'], 422);
                }
                $stripePaymentIntentId = $intent->id;
            }

            $order = Order::create([
                'user_id'                  => auth('sanctum')->id(),
                'total'                    => $total,
                'status'                   => $stripePaymentIntentId ? 'confirmed' : 'new',
                'is_read'                  => false,
                'payment_status'           => $stripePaymentIntentId ? 'paid' : 'pending',
                'payment_method'           => $request->checkout['paymentMethod'],
                'currency'                 => 'usd',
                'stripe_payment_intent_id' => $stripePaymentIntentId,
                'paid_amount'              => $stripePaymentIntentId ? $total : null,
                'transaction_date'         => $stripePaymentIntentId ? now() : null,
                'shipping_name'            => $request->checkout['name'],
                'shipping_phone'           => $request->checkout['phone'],
                'shipping_email'           => $request->checkout['email'],
                'shipping_address'         => $request->checkout['address'],
                'shipping_city'            => $request->checkout['city'],
                'shipping_province'        => $request->checkout['province'],
                'shipping_postal_code'     => $request->checkout['postalCode'],
                'delivery_days'            => $request->checkout['deliveryDays'],
                'items'                    => $request->cart,
            ]);

            if ($stripePaymentIntentId) {
                Payment::create([
                    'order_id'                 => $order->id,
                    'payment_method'           => 'stripe',
                    'amount'                   => $total,
                    'currency'                 => 'usd',
                    'payment_status'           => 'paid',
                    'stripe_payment_intent_id' => $stripePaymentIntentId,
                    'transaction_date'         => now(),
                ]);
            }

            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => 'order_created',
                'changed_by' => 'system',
                'note'       => 'Order created successfully',
            ]);

            if ($order->shipping_email) {
                try {
                    $config      = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
                    $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client, $config);
                    $htmlContent = view('emails.new_order', ['order' => $order])->render();
                    $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                        'subject'     => 'Order Confirmation - #' . $order->order_number,
                        'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                        'to'          => [['email' => $order->shipping_email], ['email' => 'sales@prosix.com']],
                        'htmlContent' => $htmlContent,
                    ]);
                    $apiInstance->sendTransacEmail($email);
                } catch (\Exception $e) {
                    Log::error('Order email failed: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success'      => true,
                'message'      => 'Order placed successfully!',
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
            ], 201);

        } catch (CardException $e) {
            return response()->json(['success' => false, 'message' => $e->getError()->message], 422);
        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment service error'], 500);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Order creation failed'], 500);
        }
    }

    public function trackOrder(Request $request)
    {
        $tracking = trim($request->query('tracking', ''));
        if (!$tracking) return response()->json(['message' => 'Tracking number required'], 422);

        $order = Order::where('order_number', $tracking)->first();
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $items = is_string($order->items) ? json_decode($order->items, true) : (array) $order->items;

        return response()->json([
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'payment_method'   => $order->payment_method,
            'payment_status'   => $order->payment_status,
            'total'            => $order->total,
            'items'            => $items,
            'shipping_name'    => $order->shipping_name,
            'shipping_phone'   => $order->shipping_phone,
            'shipping_city'    => $order->shipping_city,
            'shipping_address' => $order->shipping_address,
            'courier_name'     => $order->courier_name,
            'tracking_number'  => $order->tracking_number,
            'dispatch_date'    => $order->dispatch_date,
            'delivered_date'   => $order->delivered_date,
            'admin_notes'      => $order->admin_notes,
            'created_at'       => $order->created_at,
        ]);
    }

   public function adminIndex()
{
    Order::where('is_read', false)->update(['is_read' => true]);
    $orders = Order::with('user')->latest()->get();

    // Categories for filter
    $categories = \App\Models\Category::whereNull('parent_id')
        ->where('status', 1)
        ->orderBy('name')
        ->get();

    return view('admin.orders.index', compact('orders', 'categories'));
}

    public function adminShow($id)
    {
        $order = Order::findOrFail($id);
        if (!$order->is_read) $order->update(['is_read' => true]);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,confirmed,production,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        // ✅ ACTIVITY LOG
        ActivityLogger::log(
            action: 'status_changed',
            module: 'Order',
            targetName: 'Order #' . $order->order_number,
            targetId: $order->id,
            changes: ['new_status' => $request->status]
        );

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $request->status,
            'changed_by' => 'admin',
            'note'       => 'Status updated by admin',
        ]);

        if ($order->shipping_email) {
            try {
                $config      = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
                $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client, $config);
                $htmlContent = view('emails.order-update', ['order' => $order])->render();
                $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                    'subject'     => 'Order Status Updated - #' . $order->order_number,
                    'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                    'to'          => [['email' => $order->shipping_email]],
                    'htmlContent' => $htmlContent,
                ]);
                $apiInstance->sendTransacEmail($email);
            } catch (\Exception $e) {
                Log::error('Status email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Order status updated + email sent');
    }

    public function updateShipping(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'courier_name'    => $request->courier_name,
            'tracking_number' => $request->tracking_number,
            'dispatch_date'   => $request->dispatch_date,
        ]);

        // ✅ ACTIVITY LOG
        ActivityLogger::log(
            action: 'updated',
            module: 'Order',
            targetName: 'Order #' . $order->order_number,
            targetId: $order->id,
            changes: [
                'courier_name'    => $request->courier_name,
                'tracking_number' => $request->tracking_number,
                'dispatch_date'   => $request->dispatch_date,
            ]
        );

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'shipping_updated',
            'changed_by' => 'admin',
            'note'       => 'Shipping info updated',
        ]);

        if ($order->shipping_email) {
            try {
                $config      = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
                $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client, $config);
                $htmlContent = view('emails.order-update', ['order' => $order])->render();
                $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                    'subject'     => 'Shipping Update - Order #' . $order->order_number,
                    'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                    'to'          => [['email' => $order->shipping_email]],
                    'htmlContent' => $htmlContent,
                ]);
                $apiInstance->sendTransacEmail($email);
            } catch (\Exception $e) {
                Log::error('Shipping email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Shipping info updated + email sent');
    }

    public function updateNotes(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['admin_notes' => $request->admin_notes]);

        // ✅ ACTIVITY LOG
        ActivityLogger::log(
            action: 'updated',
            module: 'Order',
            targetName: 'Order #' . $order->order_number,
            targetId: $order->id,
            changes: ['admin_notes' => $request->admin_notes]
        );

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'notes_updated',
            'changed_by' => 'admin',
            'note'       => 'Admin notes updated',
        ]);

        if ($order->shipping_email) {
            try {
                $config      = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
                $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client, $config);
                $htmlContent = view('emails.order-update', ['order' => $order])->render();
                $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                    'subject'     => 'Order Update - #' . $order->order_number,
                    'sender'      => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                    'to'          => [['email' => $order->shipping_email]],
                    'htmlContent' => $htmlContent,
                ]);
                $apiInstance->sendTransacEmail($email);
            } catch (\Exception $e) {
                Log::error('Notes email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Notes updated + email sent');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);

        // ✅ ACTIVITY LOG
        ActivityLogger::log(
            action: 'deleted',
            module: 'Order',
            targetName: 'Order #' . $order->order_number,
            targetId: $order->id,
            changes: ['status' => 'cancelled']
        );

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'cancelled',
            'changed_by' => 'admin',
            'note'       => 'Order cancelled by admin',
        ]);

        return back()->with('success', 'Order cancelled!');
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth('sanctum')->id())->latest()->get();
        return response()->json(['data' => $orders]);
    }

    public function show($id)
    {
        $order = Order::where('user_id', auth('sanctum')->id())->findOrFail($id);
        return response()->json($order);
    }

    public function unreadCount()
    {
        return response()->json(['count' => Order::where('is_read', false)->count()]);
    }
    public function bulkUpdateStatus(Request $request)
{
    $request->validate([
        'ids'    => 'required|array|min:1',
        'ids.*'  => 'integer|exists:orders,id',
        'status' => 'required|in:new,confirmed,production,shipped,delivered,cancelled',
    ]);

    Order::whereIn('id', $request->ids)->update(['status' => $request->status]);

    foreach ($request->ids as $id) {
        OrderStatusLog::create([
            'order_id'   => $id,
            'status'     => $request->status,
            'changed_by' => 'admin',
            'note'       => 'Bulk status update by admin',
        ]);
    }

    ActivityLogger::log(
        action: 'bulk_status_changed',
        module: 'Order',
        targetName: count($request->ids) . ' Orders',
        targetId: null,
        changes: ['status' => $request->status, 'count' => count($request->ids)]
    );

    return response()->json(['success' => true, 'message' => count($request->ids) . ' orders updated']);
}
public function downloadPdf(Request $request)
{
    $request->validate([
        'ids'   => 'required|array|min:1',
        'ids.*' => 'integer|exists:orders,id',
    ]);

    $orders = Order::with('user')->whereIn('id', $request->ids)->get();

    // Category names nikalo
    $productIds = $orders->flatMap(fn($o) => collect($o->items)->pluck('id'))->unique()->filter();
    $products   = \App\Models\Product::with('category')->whereIn('id', $productIds)->get()->keyBy('id');

$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.order-pdf', compact('orders', 'products'))
        ->setPaper('a4', 'portrait');

    $filename = count($request->ids) === 1
        ? 'order-' . $orders->first()->order_number . '.pdf'
        : 'orders-' . now()->format('Y-m-d') . '.pdf';

    return $pdf->download($filename);
}
}
