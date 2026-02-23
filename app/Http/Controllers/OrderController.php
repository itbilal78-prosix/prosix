<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validation – stripe allowed
            $validated = $request->validate([
                'cart' => 'required|array|min:1',
                'cart.*.id' => 'required|integer',
                'cart.*.name' => 'required|string',
                'cart.*.price' => 'required|numeric',
                'cart.*.quantity' => 'required|integer|min:1',
                'cart.*.size' => 'nullable|string',
                'checkout' => 'required|array',
                'checkout.name' => 'required|string|max:255',
                'checkout.phone' => 'required|string|max:20',
                'checkout.address' => 'required|string',
                'checkout.city' => 'required|string',
                'checkout.province' => 'nullable|string',
                'checkout.postalCode' => 'nullable|string',
                'checkout.deliveryDays' => 'required|string',
                'checkout.paymentMethod' => 'required|string|in:cod,stripe',
            ]);

            // Total calculate
            $total = collect($request->cart)->sum(function ($item) {
                return (float) $item['price'] * (int) $item['quantity'];
            });

            // Order create – user_id sahi save hoga agar login hai
            $order = Order::create([
                'user_id'              => auth()->check() ? auth()->id() : null,
                'total'                => $total,
                'status'               => 'pending',
                'payment_method'       => $request->input('checkout.paymentMethod'),
                'shipping_name'        => $request->input('checkout.name'),
                'shipping_phone'       => $request->input('checkout.phone'),
                'shipping_address'     => $request->input('checkout.address'),
                'shipping_city'        => $request->input('checkout.city'),
                'shipping_province'    => $request->input('checkout.province'),
                'shipping_postal_code' => $request->input('checkout.postalCode'),
                'delivery_days'        => $request->input('checkout.deliveryDays'),
                'items'                => $request->cart,  // json auto encode
            ]);

            // Email queue pe bhejo (fast aur reliable)
            // Mail::to('itbilal78@gmail.com')->send(new NewOrderMail($order));

            return response()->json([
                'success'  => true,
                'message'  => 'Order placed successfully!',
                'order_id' => $order->id,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.',
                // production mein ye line hata dena
                'debug'   => $e->getMessage()
            ], 500);
        }
    }

  // Admin Orders List (Blade view)
public function adminIndex()
{
    $orders = Order::with('user')
        ->latest()
        ->paginate(20);

    return view('admin.orders.index', compact('orders'));
}


// Admin Single Order View
public function adminShow($id)
{
    $order = Order::with('user')->findOrFail($id);

    return view('admin.orders.show', compact('order'));
}

}