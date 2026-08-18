<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [

        // Order
        'order_number',
        'user_id',
        'total',
        'status',

        // Payment
        'payment_status',
        'payment_method',
        'currency',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_session_id',
        'paid_amount',
        'transaction_date',

        // Shipping
        'shipping_name',
        'shipping_phone',
        'shipping_email',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'delivery_days',

        // Items
        'items',

        // Tracking
        'tracking_number',
        'courier_name',
        'dispatch_date',
        'delivered_date',

        // Admin
        'admin_notes',
        'remark',
        'is_read',

        // ============================================
        // AGREEMENT / TERMS RECORD
        // ============================================

        'terms_accepted',
        'terms_accepted_at',

        'agreement_pdf',
        'agreement_version',

        // Customer IP address
        'agreement_ip',

        // Browser / device
        'agreement_user_agent',

        // website_checkout etc.
        'agreement_acceptance_source',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'items' => 'array',

        'transaction_date' => 'datetime',

        'dispatch_date' => 'datetime',

        'delivered_date' => 'datetime',

        // Agreement
        'terms_accepted' => 'boolean',

        'terms_accepted_at' => 'datetime',

        'is_read' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    |
    | Order create hote waqt automatic order number generate hota hai.
    |
    | Example:
    | P6S-2026-4821
    |
    */

    protected static function booted(): void
    {
        static::creating(function ($order) {

            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Order Number
    |--------------------------------------------------------------------------
    */

    public static function generateOrderNumber(): string
    {
        do {

            $year = date('Y');

            $random = rand(1000, 9999);

            $number = "P6S-{$year}-{$random}";

        } while (
            static::where('order_number', $number)->exists()
        );

        return $number;
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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


    public function teamStoreReads()
    {
        return $this->hasMany(
            \App\Models\TeamStoreOrderRead::class
        );
    }
}
