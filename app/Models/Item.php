<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'cat_type_id',
        'cat_id',
        'sub_cat_id',
        'unit_id',
        'name',
        'description',
        'image',
        'purchase_price',
        'sale_price',
        'vat',
        'sold_qty',
        'current_stock',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }
    public function sub_category()
    {
        return $this->belongsTo(Category::class, 'sub_cat_id');
    }
    public function category_type()
    {
        return $this->belongsTo(CategoryType::class, 'cat_type_id')->select('id', 'title');
    }                           
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->select('id', 'title', 'unit_type');
    }
}
