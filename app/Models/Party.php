<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'name',
        'email',
        'phone',
        'address',
        'nid_number',
        'date_of_birth',
        'current_balance',
        'status',
        'created_by_id',
        'updated_by_id',
    ];
}
