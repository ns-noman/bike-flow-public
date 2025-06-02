<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'purchase_id',
        'item_id',
        'quantity',
        'unit_price',
    ];
    
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->with('unit');
    }
}
