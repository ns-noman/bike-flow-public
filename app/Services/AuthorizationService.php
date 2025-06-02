<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\Menu;
use App\Models\Privilege;
use Illuminate\Support\Facades\Cache;

class AuthorizationService
{
    public function isAuthorized()
    {
        $user = Auth::guard('admin')->user();
        $is_superadmin = Role::find($user->type)->is_superadmin;
        if (!$is_superadmin) {
            $currentRoute = \Route::current()->getName();
            $routes = Menu::whereNotNull('route')->pluck('route')->toArray();
            
            if (in_array($currentRoute, $routes)) {
                $privileges = Privilege::join('menus', 'menus.id', '=', 'privileges.menu_id')
                    ->where('privileges.role_id', $user->type)
                    ->whereNotNull('menus.route')
                    ->pluck('menus.route')
                    ->toArray();
                return in_array($currentRoute, $privileges);
            }
        }
        return true;
    }


    public function hasMenuAccess($menu_id)
    {
        
        // Get the currently authenticated admin user
        $user_role_id = Auth::guard('admin')->user()->type;
        
        \Log::info(Cache::get("role_{$user_role_id}_privileges"));
        // Cache the role's is_superadmin status
        $is_superadmin = Cache::remember("role_{$user_role_id}_superadmin", 60, function () use ($user_role_id) {
            return Role::find($user_role_id)->is_superadmin;
        });

        // Cache the privileges for the user's role
        $privileges = Cache::remember("role_{$user_role_id}_privileges", 60, function () use ($user_role_id) {
            return Privilege::join('menus', 'menus.id', '=', 'privileges.menu_id')
                            ->where('privileges.role_id', $user_role_id)
                            ->pluck('menu_id')->toArray();
        });


        // Check if the user is a superadmin or if the menu ID is in the user's privileges
        return $is_superadmin || in_array($menu_id, $privileges);
    }
}
