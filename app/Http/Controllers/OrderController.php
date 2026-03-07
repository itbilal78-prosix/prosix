<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\CardException;
use Stripe\Exception\ApiErrorException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            // ── Validation ──
            $validated = $request->validate([
                'cart'                      => 'required|array|min:1',
                'cart.*.id'                 => 'required|integer',
                'cart.*.name'               => 'required|string',
                'cart.*.price'              => 'required|numeric',
                'cart.*.quantity'           => 'required|integer|min:1',
                'cart.*.size'               => 'nullable|string',
                'checkout'                  => 'required|array',
                'checkout.name'             => 'required|string|max:255',
                'checkout.phone'            => 'required|string|max:20',
                'checkout.email'            => 'nullable|email|max:255',
                'checkout.address'          => 'required|string',
                'checkout.city'             => 'required|string',
                'checkout.province'         => 'nullable|string',
                'checkout.postalCode'       => 'nullable|string',
                'checkout.country'          => 'nullable|string',
                'checkout.deliveryDays'     => 'required|string',
                'checkout.paymentMethod'    => 'required|string|in:stripe,cod,applepay,googlepay,cashapp,venmo,zelle,paypal,wire',
                'checkout.stripeToken'      => 'nullable|string',
            ]);

            // ── Calculate total ──
            $subtotal = collect($request->cart)->sum(function ($item) {
                return (float) $item['price'] * (int) $item['quantity'];
            });

            $totalQty = collect($request->cart)->sum(fn($i) => (int) $i['quantity']);

            // Shipping logic (matches frontend)
            if ($totalQty > 3) {
                $shipping = 0;
            } elseif ($totalQty === 3) {
                $shipping = round($subtotal * 0.10, 2);
            } else {
                $shipping = 19;
            }

            $total = $subtotal + $shipping;

            // ── Stripe Payment Processing ──
            $stripePaymentId     = null;
            $stripeClientSecret  = null;

            if ($request->input('checkout.paymentMethod') === 'stripe') {
                $stripeToken = $request->input('checkout.stripeToken');
                if (!$stripeToken) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stripe payment token is required.',
                    ], 422);
                }

                Stripe::setApiKey(config('services.stripe.secret'));

                // Create PaymentIntent (amount in cents)
                $paymentIntent = PaymentIntent::create([
                    'amount'               => (int) round($total * 100),
                    'currency'             => 'usd',
                    'payment_method'       => $stripeToken,
                    'confirm'              => true,
                    'automatic_payment_methods' => [
                        'enabled'          => true,
                        'allow_redirects'  => 'never',
                    ],
                    'description'          => 'Order from ' . $request->input('checkout.name'),
                    'metadata'             => [
                        'customer_name'    => $request->input('checkout.name'),
                        'customer_phone'   => $request->input('checkout.phone'),
                        'customer_email'   => $request->input('checkout.email', ''),
                    ],
                ]);

                // Handle requires_action (3D Secure)
                if ($paymentIntent->status === 'requires_action') {
                    return response()->json([
                        'success'       => false,
                        'requires_action' => true,
                        'client_secret' => $paymentIntent->client_secret,
                        'message'       => '3D Secure authentication required.',
                    ], 200);
                }

                if ($paymentIntent->status !== 'succeeded') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment was not successful. Please try again.',
                    ], 422);
                }

                $stripePaymentId = $paymentIntent->id;
            }

            // ── Create Order ──
            $order = Order::create([
                'user_id'              => auth()->check() ? auth()->id() : null,
                'total'                => $total,
                'status'               => 'pending',
                'payment_method'       => $request->input('checkout.paymentMethod'),
                'payment_id'           => $stripePaymentId,
                'shipping_name'        => $request->input('checkout.name'),
                'shipping_phone'       => $request->input('checkout.phone'),
                'shipping_address'     => $request->input('checkout.address'),
                'shipping_city'        => $request->input('checkout.city'),
                'shipping_province'    => $request->input('checkout.province'),
                'shipping_postal_code' => $request->input('checkout.postalCode'),
                'shipping_country'     => $request->input('checkout.country'),
                'delivery_days'        => $request->input('checkout.deliveryDays'),
                'items'                => $request->cart,
            ]);

            // Optional: queue email notification
            // Mail::to('admin@yourstore.com')->queue(new NewOrderMail($order));

            return response()->json([
                'success'  => true,
                'message'  => 'Order placed successfully!',
                'order_id' => $order->id,
            ], 201);

        } catch (CardException $e) {
            // Stripe card declined
            return response()->json([
                'success' => false,
                'message' => $e->getError()->message ?? 'Your card was declined.',
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Payment service error. Please try again.',
            ], 500);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.',
            ], 500);
        }
    }

    // ── Admin: Orders List ──
    public function adminIndex()
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    // ── Admin: Single Order ──
    public function adminShow($id)
    {
        $order = Order::with('user')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }
}
