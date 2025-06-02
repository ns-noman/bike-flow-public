<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = 
    [
        'name',
        'email',
        'phone',
        'address',
        'organization',
        'current_balance',
        'customer_type',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
}
