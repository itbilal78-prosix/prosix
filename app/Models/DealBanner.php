<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealBanner extends Model
{
    use HasFactory;
    protected $fillable = ['deal_id', 'image_path'];

public function deal()
{
    return $this->belongsTo(Deal::class);
}
}
