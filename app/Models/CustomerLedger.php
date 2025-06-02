<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'customer_id',
        'sale_id',
        'payment_id',
        'account_id',
        'particular',
        'date',
        'debit_amount',
        'credit_amount',
        'current_balance',
        'reference_number',
        'note',
        'created_by_id',
        'updated_by_id',
    ];
}
