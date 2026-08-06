<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TeamStoreOrderRead;
use Illuminate\Http\Request;

class CRMTeamStoreController extends Controller
{
    /**
     * Verify CRM bearer token safely.
     */
 private function authorized(Request $request): bool
{
    $expectedToken = (string) config(
        'services.crm.token',
        env('PROSIX_CRM_TOKEN', '')
    );

    $providedToken = (string) $request->bearerToken();

    if (
        $expectedToken === '' ||
        $providedToken === ''
    ) {
        return false;
    }

    return hash_equals(
        $expectedToken,
        $providedToken
    );
}

    /**
     * Current CRM user details from request headers.
     */
    private function viewer(Request $request): array
    {
        return [
            'id' => trim(
                (string) $request->header(
                    'X-CRM-User-ID'
                )
            ),

            'name' => trim(
                (string) $request->header(
                    'X-CRM-User-Name'
                )
            ),

            'email' => trim(
                (string) $request->header(
                    'X-CRM-User-Email'
                )
            ),
        ];
    }

    /**
     * Return all TeamStore orders for CRM.
     */
    public function index(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
            ], 401);
        }

        $viewer = $this->viewer($request);

        if ($viewer['id'] === '') {
            return response()->json([
                'success' => false,
                'message' => 'CRM viewer ID is required.',
            ], 422);
        }

        $orders = Order::query()
            ->with([
                'user',

                'teamStoreReads' => function ($query) use (
                    $viewer
                ) {
                    $query->where(
                        'viewer_id',
                        $viewer['id']
                    );
                },
            ])
            ->latest()
            ->get()
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,

                    'order_number' =>
                        $order->order_number,

                    'customer_name' =>
                        $order->shipping_name,

                    'email' =>
                        $order->shipping_email,

                    'phone' =>
                        $order->shipping_phone,

                    'status' =>
                        $order->status,

                    'payment_status' =>
                        $order->payment_status,

                    'payment_method' =>
                        $order->payment_method,

                    'currency' =>
                        $order->currency,

                    'total' =>
                        $order->total,

                    'paid_amount' =>
                        $order->paid_amount,

                    'transaction_date' =>
                        optional(
                            $order->transaction_date
                        )->toISOString(),

                    'tracking_number' =>
                        $order->tracking_number,

                    'courier_name' =>
                        $order->courier_name,

                    'dispatch_date' =>
                        optional(
                            $order->dispatch_date
                        )->toISOString(),

                    'delivered_date' =>
                        optional(
                            $order->delivered_date
                        )->toISOString(),

                    'delivery_days' =>
                        $order->delivery_days,

                    'shipping_address' =>
                        $order->shipping_address,

                    'shipping_city' =>
                        $order->shipping_city,

                    'shipping_province' =>
                        $order->shipping_province,

                    'shipping_postal_code' =>
                        $order->shipping_postal_code,

                    'items' =>
                        is_array($order->items)
                            ? $order->items
                            : [],

                    'admin_notes' =>
                        $order->admin_notes,

                    /*
                     * Current CRM user ka personal
                     * read status.
                     */
                    'is_read' =>
                        $order
                            ->teamStoreReads
                            ->isNotEmpty(),

                    'created_at' =>
                        optional(
                            $order->created_at
                        )->toISOString(),

                    'updated_at' =>
                        optional(
                            $order->updated_at
                        )->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Current CRM user ka unread count.
     */
    public function unreadCount(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
                'count' => 0,
            ], 401);
        }

        $viewer = $this->viewer($request);

        if ($viewer['id'] === '') {
            return response()->json([
                'success' => false,
                'message' => 'CRM viewer ID is required.',
                'count' => 0,
            ], 422);
        }

        $count = Order::query()
            ->whereDoesntHave(
                'teamStoreReads',
                function ($query) use ($viewer) {
                    $query->where(
                        'viewer_id',
                        $viewer['id']
                    );
                }
            )
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Current CRM user ke liye order read mark karo.
     */
    public function markRead(
        Request $request,
        Order $order
    ) {
        if (!$this->authorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
            ], 401);
        }

        $viewer = $this->viewer($request);

        if ($viewer['id'] === '') {
            return response()->json([
                'success' => false,
                'message' => 'CRM viewer ID is required.',
            ], 422);
        }

        TeamStoreOrderRead::updateOrCreate(
            [
                'order_id' => $order->id,
                'viewer_id' => $viewer['id'],
            ],
            [
                'viewer_name' => $viewer['name'],
                'viewer_email' => $viewer['email'],
                'read_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' =>
                'TeamStore order marked as read.',

            'data' => [
                'id' => $order->id,
                'is_read' => true,
            ],
        ]);
    }
}
