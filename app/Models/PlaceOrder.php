<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceOrder extends Model
{
    protected $fillable = [
        'user_id', 'full_name', 'email', 'phone',
        'order_number', 'order_date', 'delivery_date',
        'sales_rep', 'team_colors', 'notes',
        'mockup_files', 'roster_files', 'quote_files','is_read',
        'status',
    ];

    protected $casts = [
        'mockup_files' => 'array',
        'roster_files' => 'array',
        'quote_files'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
