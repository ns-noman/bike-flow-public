<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'payment_method_id',
        'account_no',
        'holder_name',
        'balance',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
}
