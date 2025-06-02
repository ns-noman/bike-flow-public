<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class BikePurchase extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'investor_id',
        'bike_id',
        'account_id',
        'seller_id',
        'broker_id',
        'bike_sale_id',
        'purchase_price',
        'servicing_cost',
        'total_cost',
        'purchase_date',
        'doc_nid',
        'doc_reg_card',
        'doc_image',
        'doc_deed',
        'doc_tax_token',
        'note',
        'reference_number',
        'purchase_status',
        'selling_status',
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
