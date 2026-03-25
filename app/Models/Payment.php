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
        'stripe_charge_id',
        'stripe_session_id',
        'transaction_date'

    ];
    public function order()
{
    return $this->belongsTo(\App\Models\PlaceOrder::class,'order_id');
}
}
