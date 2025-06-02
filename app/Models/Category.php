<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'parent_cat_id',
        'cat_type_id',
        'title',
        'image',
        'status',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_cat_id')->select('id', 'title');
    }
    public function category_type()
    {
        return $this->belongsTo(CategoryType::class, 'cat_type_id')->select('id', 'title');
    }

    public function subcategories()
    {
        // Define relationship and specify that only the required fields should be selected
        return $this->hasMany(Category::class, 'parent_cat_id')
                    ->where('status', 1) // Only active subcategories
                    ->select('id', 'parent_cat_id', 'title'); // Only select needed fields
    }
    
    
}
