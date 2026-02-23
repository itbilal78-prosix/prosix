<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;

    protected $table = 'social_medias'; // <--- yaha table name set karo
    protected $fillable = ['name', 'logo', 'link'];
}
