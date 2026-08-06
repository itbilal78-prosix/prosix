<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TeamStoreOrderRead;
use Illuminate\Http\Request;

class CRMTeamStoreController extends Controller
{
    /**
     * Verify CRM Token
     */
    private function authorized(Request $request): bool
    {
        return hash_equals(
            config('services.prosix.crm_token'),
            (string) $request->bearerToken()
        );
    }

    /**
     * Current CRM User
     */
    private function viewer(Request $request): array
    {
        return [
            'id'    => $request->header('X-CRM-User-ID'),
            'name'  => $request->header('X-CRM-User-Name'),
            'email' => $request->header('X-CRM-User-Email'),
        ];
    }

    /**
     * All TeamStore Orders
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

        $orders = Order::with([
            'user',
            'teamStoreReads' => function ($q) use ($viewer) {
                $q->where('viewer_id', $viewer['id']);
            },
        ])
        ->latest()
        ->get()
        ->map(function ($order) {

            return [

                'id' => $order->id,

                'order_number' => $order->order_number,

                'customer_name' => $order->shipping_name,

                'email' => $order->shipping_email,

                'phone' => $order->shipping_phone,

                'status' => $order->status,

                'payment_status' => $order->payment_status,

                'payment_method' => $order->payment_method,

                'currency' => $order->currency,

                'total' => $order->total,

                'tracking_number' => $order->tracking_number,

                'courier_name' => $order->courier_name,

                'delivery_days' => $order->delivery_days,

                'shipping_address' => $order->shipping_address,

                'shipping_city' => $order->shipping_city,

                'shipping_province' => $order->shipping_province,

                'shipping_postal_code' => $order->shipping_postal_code,

                'items' => $order->items,

                'admin_notes' => $order->admin_notes,

                'created_at' => $order->created_at,

                'updated_at' => $order->updated_at,

                'is_read' => $order->teamStoreReads->isNotEmpty(),

            ];

        });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Unread Count
     */
    public function unreadCount(Request $request)
    {
        if (!$this->authorized($request)) {

            return response()->json([
                'count' => 0,
            ]);

        }

        $viewer = $this->viewer($request);

        $count = Order::whereDoesntHave(
            'teamStoreReads',
            function ($q) use ($viewer) {

                $q->where(
                    'viewer_id',
                    $viewer['id']
                );

            }
        )->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Mark Read
     */
    public function markRead(Request $request, Order $order)
    {
        if (!$this->authorized($request)) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
            ], 401);

        }

        $viewer = $this->viewer($request);

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
        ]);
    }
}
