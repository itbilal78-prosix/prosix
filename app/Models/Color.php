<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel convention)
    protected $table = 'colors';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'code',
    ];
}
