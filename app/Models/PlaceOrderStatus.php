<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceOrderStatus extends Model
{
    protected $fillable = [
        'name',
        'value',
        'color',
        'is_custom',
        'sort_order',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'sort_order' => 'integer',
    ];
}
