<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'organization',
        'state',
        'zip',
        'phone',
        'role',
        'sports',
        'level'
    ];

   protected $casts = [
    'sports' => 'array',
];
}
