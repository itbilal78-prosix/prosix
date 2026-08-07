<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\TeamStoreOrderRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CRMTeamStoreController extends Controller
{
    private function authorized(Request $request): bool
    {
        $expectedToken = (string) config(
            'services.crm.token',
            env('PROSIX_CRM_TOKEN', '')
        );

        $providedToken = (string) $request->bearerToken();

        if ($expectedToken === '' || $providedToken === '') {
            return false;
        }

        return hash_equals($expectedToken, $providedToken);
    }

    private function viewer(Request $request): array
    {
        return [
            'id' => trim((string) $request->header('X-CRM-User-ID')),
            'name' => trim((string) $request->header('X-CRM-User-Name')),
            'email' => trim((string) $request->header('X-CRM-User-Email')),
        ];
    }

    public function index(Request $request): JsonResponse
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
                'teamStoreReads' => function ($query) use ($viewer) {
                    $query->where('viewer_id', $viewer['id']);
                },
            ])
            ->latest()
            ->get();

        $productIds = $orders
            ->flatMap(function (Order $order) {
                return collect($this->normalizeItems($order->items))
                    ->pluck('id');
            })
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $categoryIds = $products
            ->pluck('category_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $categories = Category::query()
            ->whereIn('id', $categoryIds)
            ->get()
            ->keyBy('id');

        $data = $orders
            ->map(function (Order $order) use ($products, $categories) {
                $items = collect($this->normalizeItems($order->items))
                    ->map(function (array $item) use ($products, $categories) {
                        $productId = isset($item['id'])
                            ? (int) $item['id']
                            : null;

                        $product = $productId
                            ? $products->get($productId)
                            : null;

                        $category = $product?->category_id
                            ? $categories->get((int) $product->category_id)
                            : null;

                        return array_merge($item, [
                            'product_id' => $productId,
                            'name' => $item['name']
                                ?? $product?->name
                                ?? 'TeamStore Product',
                            'image' => $item['image']
                                ?? $product?->thumbnail
                                ?? null,
                            'category_id' => $category?->id,
                            'category_name' => $category?->name ?? 'Other',
                            'category_icon_image' => $category?->icon_image,
                        ]);
                    })
                    ->values()
                    ->all();

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->shipping_name,
                    'email' => $order->shipping_email,
                    'phone' => $order->shipping_phone,
                    'status' => $order->status,
                    'remark' => $order->remark,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'tracking_number' => $order->tracking_number,
                    'courier_name' => $order->courier_name,
                    'delivery_days' => $order->delivery_days,
                    'shipping_address' => $order->shipping_address,
                    'shipping_city' => $order->shipping_city,
                    'shipping_province' => $order->shipping_province,
                    'shipping_postal_code' => $order->shipping_postal_code,
                    'dispatch_date' => optional($order->dispatch_date)->toISOString(),
                    'delivered_date' => optional($order->delivered_date)->toISOString(),
                    'items' => $items,
                    'admin_notes' => $order->admin_notes,
                    'is_read' => $order->teamStoreReads->isNotEmpty(),
                    'created_at' => optional($order->created_at)->toISOString(),
                    'updated_at' => optional($order->updated_at)->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        if (!$this->authorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
                'count' => 0,
            ], 401);
        }

        $viewer = $this->viewer($request);

        $count = Order::query()
            ->whereDoesntHave(
                'teamStoreReads',
                function ($query) use ($viewer) {
                    $query->where('viewer_id', $viewer['id']);
                }
            )
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function markRead(Request $request, Order $order): JsonResponse
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
            'data' => [
                'id' => $order->id,
                'is_read' => true,
            ],
        ]);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        if (!$this->authorized($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized CRM request.',
            ], 401);
        }

        $validated = $request->validate([
            'status' => 'sometimes|required|string|max:100',
            'remark' => 'sometimes|nullable|string|max:5000',
            'tracking_number' => 'sometimes|nullable|string|max:255',
            'courier_name' => 'sometimes|nullable|string|max:255',
        ]);

        $oldStatus = $order->status;

        $updateData = [];

        foreach ([
            'status',
            'remark',
            'tracking_number',
            'courier_name',
        ] as $field) {
            if (array_key_exists($field, $validated)) {
                $updateData[$field] = $validated[$field];
            }
        }

        if (!$updateData) {
            return response()->json([
                'success' => false,
                'message' => 'No update data received.',
            ], 422);
        }

        $order->update($updateData);
        $order->refresh();

        if (
            array_key_exists('status', $updateData) &&
            $oldStatus !== $order->status
        ) {
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_by' => 'crm',
                'note' => 'Status updated from CRM TeamStore',
            ]);

            try {
                ActivityLogger::log(
                    action: 'status_changed',
                    module: 'Order',
                    targetName: 'Order #' . $order->order_number,
                    targetId: $order->id,
                    changes: [
                        'old_status' => $oldStatus,
                        'new_status' => $order->status,
                        'source' => 'CRM TeamStore',
                    ]
                );
            } catch (\Throwable $exception) {
                Log::warning('CRM activity log failed', [
                    'order_id' => $order->id,
                    'message' => $exception->getMessage(),
                ]);
            }

            /*
             * Customer ko email ONLY status change par.
             * Remark / tracking typing par duplicate emails nahi jayengi.
             */
            if ($order->shipping_email) {
                try {
                    $config =
                        \SendinBlue\Client\Configuration::getDefaultConfiguration()
                            ->setApiKey('api-key', env('BREVO_API_KEY'));

                    $apiInstance =
                        new \SendinBlue\Client\Api\TransactionalEmailsApi(
                            new \GuzzleHttp\Client(),
                            $config
                        );

                    $htmlContent = view(
                        'emails.order-update',
                        ['order' => $order]
                    )->render();

                    $email =
                        new \SendinBlue\Client\Model\SendSmtpEmail([
                            'subject' =>
                                'Order Status Updated - #' .
                                $order->order_number,
                            'sender' => [
                                'name' => 'Prosix Sports',
                                'email' => 'prosixsports@gmail.com',
                            ],
                            'to' => [
                                ['email' => $order->shipping_email],
                            ],
                            'htmlContent' => $htmlContent,
                        ]);

                    $apiInstance->sendTransacEmail($email);
                } catch (\Throwable $exception) {
                    Log::error('CRM status email failed', [
                        'order_id' => $order->id,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'TeamStore order updated.',
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'remark' => $order->remark,
                'tracking_number' => $order->tracking_number,
                'courier_name' => $order->courier_name,
            ],
        ]);
    }

    private function normalizeItems(mixed $items): array
    {
        if (is_array($items)) {
            return $items;
        }

        if (is_string($items)) {
            $decoded = json_decode($items, true);

            return is_array($decoded)
                ? $decoded
                : [];
        }

        return [];
    }
}
