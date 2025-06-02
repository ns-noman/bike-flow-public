<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'model_id',
        'color_id',
        'manufacture_year',
        'registration_no',
        'chassis_no',
        'engine_no',
    ];
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bike) {
            if (empty($bike->registration_no)) {
                $bike->registration_no = 'On Test';
            }
        });
    }


}
