<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'svg_data',
        'image_data',
        'description',
        'source',
        'box_index',
        'category_id',

        // Mascot selected colors
        'color_count',
        'selected_colors',
        'color_mappings',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

        'color_count' => 'integer',
        'selected_colors' => 'array',
        'color_mappings' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
