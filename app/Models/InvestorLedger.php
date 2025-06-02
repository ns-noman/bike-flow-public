<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorLedger extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'investor_id',
        'account_id',
        'particular',
        'debit_amount',
        'credit_amount',
        'current_balance',
        'reference_number',
        'transaction_date',
        'created_by_id',
        'updated_by_id',
    ];
}
