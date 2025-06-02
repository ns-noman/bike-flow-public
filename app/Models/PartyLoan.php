<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyLoan extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'party_id',
        'account_id',
        'loan_no',
        'loan_type',
        'amount',
        'loan_date',
        'due_date',
        'last_payment_date',
        'paid_amount',
        'reference_number',
        'payment_status',
        'status',
        'note',
        'created_by_id',
        'updated_by_id',
    ];
}
