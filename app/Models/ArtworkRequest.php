<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkRequest extends Model
{
   protected $fillable = [
    'user_id',      // ✅ NAYA
    'full_name', 'email', 'phone', 'instagram', 'address',
    'team_name', 'role', 'quantity', 'team_color', 'home_away',
    'design_style', 'material', 'products', 'additional', 'source',
    'artwork_file',
];

    protected $casts = [
        'products' => 'array'
    ];
}
