<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

 // App\Models\User.php — fillable mein add karo
protected $fillable = [
    'name', 'email', 'password', 'role', 'status', 'otp',
    'phone', 'location', 'is_pinned',
    'customizer_name',      // ✅ NAYA
    'send_to_customizer',   // ✅ NAYA
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function orders()
{
    return $this->hasMany(Order::class);
}

}
