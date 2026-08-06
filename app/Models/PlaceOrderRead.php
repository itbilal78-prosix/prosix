<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceOrderRead extends Model
{
    protected $fillable = [
        'place_order_id',
        'source',
        'viewer_id',
        'viewer_name',
        'viewer_email',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function placeOrder()
    {
        return $this->belongsTo(PlaceOrder::class);
    }
}
