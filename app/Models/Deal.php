<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'description', 'button_text', 'button_link'
    ];

    public function images()
    {
        return $this->hasMany(DealImage::class);
    }
}