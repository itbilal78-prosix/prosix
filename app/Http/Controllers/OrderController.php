<?php

namespace App\Http\Controllers;

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
    /**
     * STORE ORDER
     */
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
                // 'checkout.paymentMethod'  => 'required|string|in:stripe,cod,paypal,wire',
                'checkout.paymentMethod' => 'required|string|in:stripe',

            ]);

            /**
             * CALCULATE TOTAL
             */
            $subtotal = collect($request->cart)->sum(
                fn ($item) => $item['price'] * $item['quantity']
            );

            $shipping = 0;

            foreach ($request->cart as $item) {
                if (! ($item['shipping_enabled'] ?? false)) continue;
                $cost      = $item['shipping_cost'] ?? 0;
                $freeAbove = $item['free_shipping_above'] ?? null;
                if ($freeAbove && $subtotal >= $freeAbove) continue;
                $shipping += $cost * $item['quantity'];
            }

            $total = round($subtotal + $shipping, 2);

            /**
             * STRIPE PAYMENT
             */
            $stripePaymentIntentId = null;

            if ($request->checkout['paymentMethod'] === 'stripe') {

                Stripe::setApiKey(config('services.stripe.secret'));

                $intent = PaymentIntent::create([
                    'amount'             => (int) ($total * 100),
                    'currency'           => 'usd',
                    'payment_method'     => $request->checkout['stripeToken'],
                    'confirm'            => true,
                    'automatic_payment_methods' => [
                        'enabled'         => true,
                        'allow_redirects' => 'never',
                    ],
                ]);

                if ($intent->status !== 'succeeded') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment failed',
                    ], 422);
                }

                $stripePaymentIntentId = $intent->id;
            }

            /**
             * CREATE ORDER
             * order_number — Order Model booted() mein auto generate hoga (P6S-2026-XXXX)
             */
            $order = Order::create([
                'user_id'                  => auth('sanctum')->id(),
                'total'                    => $total,
                'status'                   => $stripePaymentIntentId ? 'confirmed' : 'new',
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

            /**
             * SAVE PAYMENT RECORD
             */
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

            /**
             * STATUS LOG
             */
            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => 'order_created',
                'changed_by' => 'system',
                'note'       => 'Order created successfully',
            ]);

            /**
             * EMAIL TO CUSTOMER
             */
            if ($order->shipping_email) {
                try {
                    $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
                        ->setApiKey('api-key', env('BREVO_API_KEY'));

                    $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
                        new \GuzzleHttp\Client, $config
                    );

                    $htmlContent = view('emails.new_order', ['order' => $order])->render();

                    $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                        'subject' => 'Order Confirmation - #' . $order->order_number,
                        'sender'  => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                        'to'      => [
                            ['email' => $order->shipping_email],
                            ['email' => 'sales@prosix.com'],
                        ],
                        'htmlContent' => $htmlContent,
                    ]);

                    $apiInstance->sendTransacEmail($email);

                } catch (\Exception $e) {
                    Log::error('Order email failed: ' . $e->getMessage());
                }
            }

            // ✅ order_number return karo — Checkout.vue success screen pe dikhayega
            return response()->json([
                'success'      => true,
                'message'      => 'Order placed successfully!',
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
            ], 201);

        } catch (CardException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getError()->message,
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment service error',
            ], 500);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed',
            ], 500);
        }
    }

    /**
     * TRACK ORDER
     * Customer order_number (P6S-2026-XXXX) ya courier tracking number se track kar sakta hai
     */
 public function trackOrder(Request $request)
{
    $tracking = trim($request->query('tracking', ''));

    if (!$tracking) {
        return response()->json(['message' => 'Tracking number required'], 422);
    }

    // ✅ SIRF order_number se search — P6S-2026-XXXX
    // courier tracking_number se NAHI — wo admin ka internal use hai
    $order = Order::where('order_number', $tracking)->first();

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    $items = is_string($order->items)
        ? json_decode($order->items, true)
        : (array) $order->items;

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
        // ✅ courier tracking number show karo (track karne ke liye nahi,
        //    sirf info ke liye — "Your parcel is with TCS: ABC123")
        'tracking_number'  => $order->tracking_number,
        'dispatch_date'    => $order->dispatch_date,
        'delivered_date'   => $order->delivered_date,
        'admin_notes'      => $order->admin_notes,
        'created_at'       => $order->created_at,
    ]);
}

    /**
     * ADMIN ORDER LIST
     */
    public function adminIndex()
    {
        $orders = Order::with('user')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }
    /**
     * ADMIN SINGLE ORDER
     */
    public function adminShow($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * UPDATE ORDER STATUS
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,confirmed,production,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $request->status,
            'changed_by' => 'admin',
            'note'       => 'Status updated by admin',
        ]);

        if ($order->shipping_email) {
            try {
                $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
                    ->setApiKey('api-key', env('BREVO_API_KEY'));

                $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
                    new \GuzzleHttp\Client, $config
                );

                $htmlContent = view('emails.order-update', ['order' => $order])->render();

                $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                    'subject' => 'Order Status Updated - #' . $order->order_number,
                    'sender'  => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                    'to'      => [['email' => $order->shipping_email]],
                    'htmlContent' => $htmlContent,
                ]);

                $apiInstance->sendTransacEmail($email);

            } catch (\Exception $e) {
                Log::error('Status email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Order status updated + email sent');
    }

    /**
     * UPDATE SHIPPING INFO
     */
    public function updateShipping(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'courier_name'    => $request->courier_name,
            'tracking_number' => $request->tracking_number,
            'dispatch_date'   => $request->dispatch_date,
        ]);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'shipping_updated',
            'changed_by' => 'admin',
            'note'       => 'Shipping info updated',
        ]);

        if ($order->shipping_email) {
            try {
                $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
                    ->setApiKey('api-key', env('BREVO_API_KEY'));

                $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
                    new \GuzzleHttp\Client, $config
                );

                $htmlContent = view('emails.order-update', ['order' => $order])->render();

                $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                    'subject' => 'Shipping Update - Order #' . $order->order_number,
                    'sender'  => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                    'to'      => [['email' => $order->shipping_email]],
                    'htmlContent' => $htmlContent,
                ]);

                $apiInstance->sendTransacEmail($email);

            } catch (\Exception $e) {
                Log::error('Shipping email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Shipping info updated + email sent');
    }

    /**
     * UPDATE ADMIN NOTES
     */
    public function updateNotes(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['admin_notes' => $request->admin_notes]);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'notes_updated',
            'changed_by' => 'admin',
            'note'       => 'Admin notes updated',
        ]);

        if ($order->shipping_email) {
            try {
                $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
                    ->setApiKey('api-key', env('BREVO_API_KEY'));

                $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
                    new \GuzzleHttp\Client, $config
                );

                $htmlContent = view('emails.order-update', ['order' => $order])->render();

                $email = new \SendinBlue\Client\Model\SendSmtpEmail([
                    'subject' => 'Order Update - #' . $order->order_number,
                    'sender'  => ['name' => 'Prosix Sports', 'email' => 'prosixsports@gmail.com'],
                    'to'      => [['email' => $order->shipping_email]],
                    'htmlContent' => $htmlContent,
                ]);

                $apiInstance->sendTransacEmail($email);

            } catch (\Exception $e) {
                Log::error('Notes email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Notes updated + email sent');
    }

    /**
     * CANCEL ORDER
     */
    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => 'cancelled',
            'changed_by' => 'admin',
            'note'       => 'Order cancelled by admin',
        ]);

        return back()->with('success', 'Order cancelled!');
    }

    /**
     * USER: My orders list
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth('sanctum')->id())
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    /**
     * USER: Single order
     */
    public function show($id)
    {
        $order = Order::where('user_id', auth('sanctum')->id())
            ->findOrFail($id);

        return response()->json($order);
    }
}
