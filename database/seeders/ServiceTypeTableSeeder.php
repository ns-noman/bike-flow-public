<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class ServiceTypeTableSeeder extends Seeder
{
    public function run()
    {
        $data = 
        [
            ['id'=>1,'name'=>'Food','icon'=>'<i class="fas fa-utensils" style="font-size: 65px;"></i>'],
            ['id'=>2,'name'=>'Cloth','icon'=>'<i class="fas fa-tshirt" style="font-size: 65px;"></i>'],
            ['id'=>3,'name'=>'Shelter','icon'=>'<i class="fas fa-home" style="font-size: 65px;"></i>'],
            ['id'=>4,'name'=>'Education','icon'=>'<i class="fas fa-graduation-cap" style="font-size: 65px;"'],
            ['id'=>5,'name'=>'Medicare','icon'=>'<i class="fas fa-medkit" style="font-size: 65px;"></i>'],
            ['id'=>6,'name'=>'Charity','icon'=>'<i class="fas fa-donate" style="font-size: 65px;"></i>'],
        ];
        ServiceType::insert($data);
    }
}
