<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BikeServiceRecordDetails extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'bike_service_record_id',
        'service_id',
        'quantity',
        'price',
    ];
}
