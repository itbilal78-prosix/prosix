<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceOrder extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'order_number',
        'order_date',
        'delivery_date',
        'sales_rep',
        'team_colors',
        'notes',
        'remark',
        'mockup_files',
        'roster_files',
        'quote_files',
        'is_read',
        'status',
    ];

    protected $casts = [
        'mockup_files' => 'array',
        'roster_files' => 'array',
        'quote_files' => 'array',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reads()
    {
        return $this->hasMany(PlaceOrderRead::class);
    }
}
