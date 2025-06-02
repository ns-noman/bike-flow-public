<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InvestorTransaction extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'investor_id',
        'account_id',
        'transaction_type',
        'particular',
        'debit_amount',
        'credit_amount',
        'current_balance',
        'reference_number',
        'transaction_date',
        'description',
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
