<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamStoreOrderRead extends Model
{
    protected $table = 'teamstore_order_reads';

    protected $fillable = [
        'order_id',
        'viewer_id',
        'viewer_name',
        'viewer_email',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
