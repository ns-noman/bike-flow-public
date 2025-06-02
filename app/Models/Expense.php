<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'account_id',
        'expense_no',
        'date',
        'total_amount',
        'reference_number',
        'note',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by_id');
    }
    public function expense_details()
    {
        return $this->hasMany(ExpenseDetails::class, 'expense_id')->with(['expense_head','expense_cat']);
    }
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
