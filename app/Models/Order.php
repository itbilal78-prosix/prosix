<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'currency',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_session_id',
        'paid_amount',
        'transaction_date',
        'shipping_name',
        'shipping_phone',
        'shipping_email',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'delivery_days',
        'items',
        'tracking_number',
        'courier_name',
        'dispatch_date',
        'delivered_date',
        'admin_notes',
    ];

    protected $casts = [
        'items'            => 'array',
        'transaction_date' => 'datetime',
        'dispatch_date'    => 'datetime',
        'delivered_date'   => 'datetime',
    ];

    /**
     * ✅ Order create hote waqt auto order_number generate karo
     * Format: P6S-2026-XXXX (e.g. P6S-2026-4821)
     */
    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        do {
            $year   = date('Y');
            $random = rand(1000, 9999);
            $number = "P6S-{$year}-{$random}";
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    // ─── Relationships ───────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }
}
