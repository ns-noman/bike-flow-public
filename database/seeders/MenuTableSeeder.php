<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuTableSeeder extends Seeder
{
    public function run()
    {
        $menus =
        [
            ['id' => 1, 'parent_id' => 0, 'srln' => 1, 'menu_name' => 'Dashboard', 'navicon' => '<i class="nav-icon fas fa-tachometer-alt"></i>', 'is_side_menu' => 1, 'create_route' => null, 'route' => 'dashboard.index', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'parent_id' => 0, 'srln' => 2, 'menu_name' => 'Basic Info', 'navicon' => '<i class="nav-icon fa-solid fa-gear"></i>', 'is_side_menu' => 1, 'create_route' => null, 'route' => 'basic-infos.index', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'parent_id' => 0, 'srln' => 3, 'menu_name' => 'Admin', 'navicon' => '<i class="nav-icon fa-solid fa-users-line"></i>', 'is_side_menu' => 1, 'create_route' => null, 'route' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'parent_id' => 3, 'srln' => 1, 'menu_name' => 'Roles', 'navicon' => '<i class="far fa-dot-circle nav-icon"></i>', 'is_side_menu' => 1, 'create_route' => 'roles.create', 'route' => 'roles.index', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'parent_id' => 3, 'srln' => 2, 'menu_name' => 'Admins', 'navicon' => '<i class="far fa-dot-circle nav-icon"></i>', 'is_side_menu' => 1, 'create_route' => 'admins.create', 'route' => 'admins.index', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'parent_id' => 4, 'srln' => 1, 'menu_name' => 'Add', 'navicon' => null, 'is_side_menu' => 0, 'create_route' => null, 'route' => 'roles.create', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'parent_id' => 4, 'srln' => 2, 'menu_name' => 'Edit', 'navicon' => null, 'is_side_menu' => 0, 'create_route' => null, 'route' => 'roles.edit', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'parent_id' => 4, 'srln' => 3, 'menu_name' => 'Delete', 'navicon' => null, 'is_side_menu' => 0, 'create_route' => null, 'route' => 'roles.destroy', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'parent_id' => 5, 'srln' => 1, 'menu_name' => 'Add', 'navicon' => null, 'is_side_menu' => 0, 'create_route' => null, 'route' => 'admins.create', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'parent_id' => 5, 'srln' => 2, 'menu_name' => 'Edit', 'navicon' => null, 'is_side_menu' => 0, 'create_route' => null, 'route' => 'admins.edit', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'parent_id' => 5, 'srln' => 3, 'menu_name' => 'Delete', 'navicon' => null, 'is_side_menu' => 0, 'create_route' => null, 'route' => 'admins.destroy', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'parent_id' => 0, 'srln' => 4, 'menu_name' => 'Frontend Menus', 'navicon' => '<i class="nav-icon fa-solid fa-list-check"></i>', 'is_side_menu' => 1, 'create_route' => 'frontend-menus.create', 'route' => 'frontend-menus.index', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'parent_id' => 0, 'srln' => 5, 'menu_name' => 'Backend Menus', 'navicon' => '<i class="nav-icon fa-solid fa-list"></i>', 'is_side_menu' => 1, 'create_route' => 'menus.create', 'route' => 'menus.index', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'parent_id' => 0, 'srln' => 6, 'menu_name' => 'Slider Manage', 'navicon' => '<i class="nav-icon fa fa-list"></i>', 'is_side_menu' => 1, 'create_route' => null, 'route' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'parent_id' => 0, 'srln' => 7, 'menu_name' => 'Gallery Manage', 'navicon' => '<i class="nav-icon fa fa-images"></i>', 'is_side_menu' => 1, 'create_route' => null, 'route' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'parent_id' => 0, 'srln' => 8, 'menu_name' => 'Services', 'navicon' => '<i class="nav-icon fa fa-credit-card"></i>', 'is_side_menu' => 1, 'create_route' => null, 'route' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'parent_id' => 0, 'srln' => 10, 'menu_name' => 'Products', 'navicon' => '<i class="nav-icon fa fa-products"></i>', 'is_side_menu' => 1, 'create_route' => null, 'route' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];
        Menu::insert($menus);
    }
}
