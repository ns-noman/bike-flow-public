<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class FrontendMenu extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'parent_id',
        'srln',
        'title',
        'slug',
        'is_in_menus',
        'is_in_pages',
        'status',
        'created_by_id',
        'updated_by_id',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model){
            $model->slug = Str::slug($model->title);
            $model->created_by_id = Auth::guard('admin')->user()->id;
        });

        static::created(function ($model) {
            $model->slug = Str::slug($model->title . '-' . $model->id);
            $model->saveQuietly();
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->title);
            $model->updated_by_id = Auth::guard('admin')->user()->id;
        });
        static::updated(function ($model) {
            $model->slug = Str::slug($model->title . '-' . $model->id);
            $model->saveQuietly();
        });
    }


    public function children_index()
    {
        return $this->hasMany(FrontendMenu::class, 'parent_id')->orderBy('srln','asc');
    }
    public function children_create()
    {
        return $this->hasMany(FrontendMenu::class, 'parent_id')->where('status',1)->orderBy('srln','asc');
    }
    
    public function frontendsubmenus()
    {
        return $this->hasMany(FrontendMenu::class, 'parent_id')->where(['is_in_menus'=>1, 'status'=>1])->select(['id', 'parent_id', 'title', 'slug'])->orderBy('srln');
    }
}
