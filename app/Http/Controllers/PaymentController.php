<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function index()
    {
        // Eager load order relationship — Order N/A fix
        $payments = Payment::with('order')->latest()->get();

        // Stripe se real card details fetch karo
        Stripe::setApiKey(config('services.stripe.secret'));

        $payments = $payments->map(function ($payment) {
            $payment->card_last4   = null;
            $payment->card_brand   = null;
            $payment->real_status  = $payment->payment_status;

            if ($payment->stripe_payment_intent_id) {
                try {
                    $intent = PaymentIntent::retrieve([
                        'id'     => $payment->stripe_payment_intent_id,
                        'expand' => ['payment_method'],
                    ]);

                    // Real Stripe status
                    $payment->real_status = $intent->status; // succeeded / requires_payment_method / etc.

                    // Card last4 + brand
                    if (
                        isset($intent->payment_method) &&
                        isset($intent->payment_method->card)
                    ) {
                        $payment->card_last4 = $intent->payment_method->card->last4;
                        $payment->card_brand = $intent->payment_method->card->brand;
                    }
                } catch (\Exception $e) {
                    // Stripe error — silently ignore, fallback to DB value
                }
            }

            return $payment;
        });

        return view('admin.payments.index', compact('payments'));
    }
}
