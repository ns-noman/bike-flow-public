<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransferHistory extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'transfer_date',
        'from_account_id',
        'to_account_id',
        'amount',
        'reference_number',
        'description',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
}
