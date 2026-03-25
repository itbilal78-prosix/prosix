<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'currency',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_session_id',
        'tracking_number',
        'courier_name',
        'dispatch_date',
        'delivered_date',
        'admin_notes',
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
        'items'
    ];

    protected $casts = [
        'items' => 'array',
        'dispatch_date' => 'date',
        'delivered_date' => 'date',
        'transaction_date' => 'datetime',
    ];

    /**
     * Auto-generate Order Number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {

            $latestOrder = self::latest()->first();

            $number = 1;

            if ($latestOrder && $latestOrder->order_number) {

                $lastNumber = intval(substr($latestOrder->order_number, -4));

                $number = $lastNumber + 1;
            }

            $order->order_number =
                'ORD-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        });
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
