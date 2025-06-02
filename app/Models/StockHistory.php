<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'item_id',
        'date',
        'particular',
        'stock_in_qty',
        'stock_out_qty',
        'rate',
        'current_stock',
        'created_by_id',
    ];
}
