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

        'front_black', 'front_white', 'front_svg',
        'back_black', 'back_white', 'back_svg',
        'left_black', 'left_white', 'left_svg',
        'right_black', 'right_white', 'right_svg',

        'thumbnail',

        'navigation_id', 'category_id', 'subcategory_id',

        // 🔥 CUSTOM
        'color_changes',
        'pattern_changes',

        'custom_front_svg',
        'custom_back_svg',
        'custom_left_svg',
        'custom_right_svg',
        'applications',
        'is_featured',
        'is_apparel',
        'customized_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'color_changes' => 'array',
        'pattern_changes' => 'array',
        'mascot_changes' => 'array',  // ✅ This is already there
        'applications' => 'array',     // ✅ This is already there
        'customized_at' => 'datetime',
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
        return $this->belongsTo(Subcategory::class);
    }
}
