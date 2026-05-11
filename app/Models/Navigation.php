<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Navigation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'route',
        'has_dropdown',
        'position',
        'status',
        'clickable',
    ];
}
