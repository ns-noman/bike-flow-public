<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyPayment extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'party_id',
        'account_id',
        'loan_id',
        'payment_type',
        'date',
        'amount',
        'reference_number',
        'note',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
}
