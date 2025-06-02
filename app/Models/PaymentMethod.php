<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'name',
        'is_virtual',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
}
