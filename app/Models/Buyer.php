<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'name',
        'contact',
        'nid',
        'dob',
        'dl_no',
        'passport_no',
        'bcn_no',
        'status',
    ];
}
