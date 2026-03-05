<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category_id',
        'subcategory_id',
        'is_featured',
        'is_apparel',
        'show_in_category',

        // ── New fields ──
        'shipping_enabled',
        'shipping_cost',
        'free_shipping_above',
        'in_stock',
        'stock_quantity',
        'sizes',
        'colors',
        'gallery_images',
        'size_chart_image',
    ];

    protected $casts = [
        'is_featured'       => 'boolean',
        'is_apparel'        => 'boolean',
        'show_in_category'  => 'boolean',
        'shipping_enabled'  => 'boolean',
        'in_stock'          => 'boolean',
        'sizes'             => 'array',
        'colors'            => 'array',
        'gallery_images'    => 'array',
        'shipping_cost'     => 'float',
        'free_shipping_above' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }
}
