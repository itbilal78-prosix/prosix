<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Navigation extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'route',
        'has_dropdown',
        'position',
        'status'
    ];
   
}

