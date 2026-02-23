<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'payment_method',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'delivery_days',
        'items',              // JSON field
    ];

    protected $casts = [
        'items' => 'array',
    ];
public function user()
{
    return $this->belongsTo(User::class);
}

}