<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountLedger extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'account_id',
        'debit_amount',
        'credit_amount',
        'current_balance',
        'reference_number',
        'description',
        'transaction_date',
    ];
}
