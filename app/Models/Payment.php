<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'currency',
        'payment_status',
        'stripe_payment_intent_id',
        'transaction_date',
    ];

    /**
     * Order relationship — Order N/A fix ke liye zaroori
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
