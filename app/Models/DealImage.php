<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealImage extends Model
{
    use SoftDeletes;

    protected $fillable = ['deal_id', 'image_path', 'link', 'label'];
}
