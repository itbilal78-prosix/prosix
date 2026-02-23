<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealImage extends Model
{
    protected $fillable = ['deal_id', 'image_path', 'link'];
}