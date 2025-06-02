<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BikeService extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'bike_service_category_id',
        'name',
        'price',
        'status',
    ];
}
