<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'subtitle', 'description', 'button_text', 'button_link'
    ];

    public function images()
    {
        return $this->hasMany(DealImage::class);
    }

    public function banners()
    {
        return $this->hasMany(\App\Models\DealBanner::class);
    }
}
