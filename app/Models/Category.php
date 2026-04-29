<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
   protected $fillable = [
 'name','status','navigation_id','parent_id','highlight',
 'icon_image','highlight_image','password','position'
];

    // Subcategories
// public function subcategories()
//     {
//         return $this->hasMany(Category::class, 'parent_id'); // <-- parent_id is the key
//     }
  public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Optional: get the parent category of a subcategory
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
//    public function parent()
// {
//     return $this->belongsTo(Category::class, 'parent_id');
// }

    // Navigation
    public function navigation()
    {
        return $this->belongsTo(Navigation::class);
    }
public function models()
{
    return $this->hasMany(CustomizerModel::class, 'category_id');
}
public function templates()
{
    return $this->hasMany(\App\Models\Template::class, 'category_id');
}
}
