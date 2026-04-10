<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCustomization extends Model
{
    protected $fillable = [
        'user_id',
        'customizer_model_id',
        'name',
        'color_changes',
        'pattern_changes',
        'mascot_changes',
        'applications',
        'thumbnail',
    ];

    // ✅ YEH MOST IMPORTANT HAI - bina is ke JSON string return hoga, array nahi
    protected $casts = [
        'color_changes'   => 'array',
        'pattern_changes' => 'array',
        'mascot_changes'  => 'array',
        'applications'    => 'array',
    ];

    public function model()
    {
        return $this->belongsTo(CustomizerModel::class, 'customizer_model_id');
    }
}
