<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'parent_id',
        'srln',
        'menu_name',
        'navicon',
        'has_child_menu',
        'is_side_menu',
        'create_route',
        'route',
        'status',
    ];
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id')->orderBy('id','asc');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('status',1)->orderBy('srln','asc');
    }
    public function childrenmenu()
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('status',1)->orderBy('srln','asc');
    }
    public function childrenforcreatemenu()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('srln','asc');
    }
}