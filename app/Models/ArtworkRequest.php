<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArtworkRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'full_name', 'email', 'phone', 'instagram', 'address',
        'team_name', 'role', 'quantity', 'team_color', 'home_away',
        'design_style', 'material', 'products', 'additional', 'source',
        'artwork_file',
        'is_read',
    ];

    protected $casts = [
        'products' => 'array',
        'is_read'  => 'boolean',
    ];
}
