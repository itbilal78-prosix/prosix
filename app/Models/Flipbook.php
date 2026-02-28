<?php

// =============================================
// File: app/Models/Flipbook.php
// =============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flipbook extends Model
{
    protected $fillable = [
        'title',
        'file_path',
    ];

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
