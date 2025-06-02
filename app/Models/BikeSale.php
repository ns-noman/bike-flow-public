<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BikeSale extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'bike_purchase_id',
        'account_id',
        'buyer_id',
        'sale_price',
        'sale_date',
        'doc_nid',
        'doc_reg_card',
        'doc_image',
        'doc_deed',
        'doc_tax_token',
        'note',
        'reference_number',
        'name_transfer_date',
        'is_name_transfered',
        'is_repurchased',
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
