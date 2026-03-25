<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();

        $event = json_decode($payload);

        // Save webhook log
        \DB::table('webhook_logs')->insert([
            'event_type' => $event->type ?? 'unknown',
            'payload' => $payload,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($event->type == 'payment_intent.succeeded') {

            $paymentIntent = $event->data->object;

            $order = Order::where(
                'stripe_payment_intent_id',
                $paymentIntent->id
            )->first();

            if ($order) {

                // update order
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'paid_amount' => $paymentIntent->amount / 100,
                    'transaction_date' => now(),
                ]);

                // update payment table
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'stripe',
                    'amount' => $paymentIntent->amount / 100,
                    'currency' => $paymentIntent->currency,
                    'payment_status' => 'paid',
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'transaction_date' => now(),
                ]);

                // timeline log
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => 'payment_received',
                    'changed_by' => 'system',
                    'note' => 'Stripe payment received'
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
