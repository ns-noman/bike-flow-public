<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'name',
        'email',
        'phone',
        'address',
        'organization',
        'opening_payable',
        'opening_receivable',
        'current_balance',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
}