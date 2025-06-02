<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        $roles = 
        [
            [
                'id'=>1,
                'is_superadmin'=>1,
                'created_by'=>1,
                'role'=>'Super Admin',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
        ];
        Role::insert($roles);
    }
}
