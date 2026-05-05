<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomizerModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_name',
        'title', 'description', 'price',

        // Default views
        'front_black', 'front_white', 'front_svg',
        'back_black',  'back_white',  'back_svg',
        'left_black',  'left_white',  'left_svg',
        'right_black', 'right_white', 'right_svg',

        'thumbnail',

        'navigation_id', 'category_id', 'subcategory_id',

        // Customizer
        'color_changes',
        'pattern_changes',
        'mascot_changes',
        'custom_front_svg',
        'custom_back_svg',
        'custom_left_svg',
        'custom_right_svg',
        'applications',
        'customized_at',
        'is_featured',
        'is_apparel',
        'position',
            'is_hidden',


        // ── NEW: Product-like fields ──
        'in_stock',
        'stock_quantity',
        'shipping_enabled',
        'shipping_cost',
        'free_shipping_above',
        'sizes',
        'size_chart_image',
        'colors_data',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'shipping_cost'       => 'decimal:2',
        'free_shipping_above' => 'decimal:2',
        'in_stock'            => 'boolean',
        'shipping_enabled'    => 'boolean',
        'stock_quantity'      => 'integer',
        'sizes'               => 'array',
        'colors_data'         => 'array',
        'color_changes'       => 'array',
        'pattern_changes'     => 'array',
        'mascot_changes'      => 'array',
        'applications'        => 'array',
        'customized_at'       => 'datetime',
            'is_hidden' => 'boolean',

    ];

    public function navigation()
    {
        return $this->belongsTo(Navigation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }
    public function getRouteKeyName()
{
    return 'title';
}
}
