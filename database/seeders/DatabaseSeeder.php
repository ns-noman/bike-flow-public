<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->call(AdminTableSeeder::class);
        // $this->call(RoleTableSeeder::class);
        // $this->call(MenuTableSeeder::class);
        $this->call(ServiceTypeTableSeeder::class);
    }
}
