<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class ExpenseHead extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'expense_category_id',
        'title',
        'code',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by_id = Auth::guard('admin')->user()->id;
        });

        static::updating(function ($model) {
            $model->updated_by_id = Auth::guard('admin')->user()->id;
        });
    }
}
