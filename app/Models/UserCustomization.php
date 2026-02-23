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
        'notes',
        'is_public',
    ];

    protected $casts = [
        'color_changes'   => 'array',
        'pattern_changes' => 'array',
        'is_public'       => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->belongsTo(CustomizerModel::class, 'customizer_model_id');
    }
}
