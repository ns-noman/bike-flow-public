<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Hash;

class AdminTableSeeder extends Seeder
{
    public function run()
    {
        $admins = [
                [
                    'id'=>1,
                    'name'=>'Super Admin',
                    'type'=>1,
                    'mobile'=>'01800000000',
                    'email'=>'admin@gmail.com',
                    'password'=>Hash::make('4444'),
                    'image'=>null,
                    'status'=>1,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
        ];
        Admin::insert($admins);
    }
}
