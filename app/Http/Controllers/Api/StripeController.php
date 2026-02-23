<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        // Stripe secret key set karo (env se)
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $amount = $request->input('amount'); // frontend se dollars mein aa raha (jaise 49.99)

            if (!is_numeric($amount) || $amount <= 0) {
                return response()->json(['error' => 'Invalid amount'], 400);
            }

            // Amount ko cents mein convert (Stripe cents mangta hai)
            $paymentIntent = PaymentIntent::create([
                'amount'                 => $amount * 100,
                'currency'               => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                // Optional: metadata add kar sakte ho (order details ke liye)
                // 'metadata' => ['order_id' => 'temp-order-123'],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}