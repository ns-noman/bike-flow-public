@inject('authorization', 'App\Services\AuthorizationService')
@php
   $menus = App\Models\Menu::with('childrenmenu.childrenmenu.childrenmenu.childrenmenu')
                            ->where(['parent_id'=>0,'status'=>1])
                            ->orderBy('srln','asc')
                            ->get()
                            ->toArray();
@endphp
<aside class="main-sidebar sidebar-dark-primary elevation-0 bg-warning">
    <a href="{{ route('profile.update-details') }}" class="brand-link">
        <img src="{{ $userImage }}" alt="{{ $basicInfo->title }} Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8" height="30" width="30">
        <span class="brand-text font-weight-dark text-dark">{{ Auth::guard('admin')->user()->name }}</span>
    </a>
    <style>
        .cust-bg-info {
            background-color: #0dcaf0 !important;
            color: white;
        }
        .bg-success-hover:hover {
            background-color: #198754 !important;
        }

    </style>
    <div class="sidebar" style="background-color: #000a05">
        @php
            $startTime = microtime(true);
        @endphp
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @php
                    if (!function_exists('childrenRouteList')) {
                        function childrenRouteList($childrenMenus, $childrenMenuListArray = []) {
                            foreach ($childrenMenus as $childMenu) {
                                if ($childMenu['route']) {
                                    $childrenMenuListArray[] = $childMenu['route'];
                                }
                                if ($childMenu['create_route']) {
                                    $childrenMenuListArray[] = $childMenu['create_route'];
                                }
                                if (count($childMenu['childrenmenu'])) {
                                    $childrenMenuListArray = array_merge($childrenMenuListArray, childrenRouteList($childMenu['childrenmenu']));
                                }
                            }
                            return $childrenMenuListArray;
                        }
                    }

                    if(!function_exists('displaySidebarMenuList'))
                    {
                        function displaySidebarMenuList($menus,$authorization)
                        {
                            $output = '';
                            foreach ($menus as $menu) {

                                if($menu['is_side_menu'] && $authorization->hasMenuAccess($menu['id'])){


                                    $route = $menu['route'] && Route::has($menu['route']) ? route($menu['route']) : '#';
                                    $createRoute = $menu['create_route'] && Route::has($menu['create_route']) ? route($menu['create_route']) : '#';
                                    

                                    $navicon = $menu['navicon'];
                                    $menuName = $menu['menu_name'];
                                    $hasChildMenu = count($menu['childrenmenu']);
                                    $currentRoute = Route::currentRouteName();
                                    $hasDropDown  = $hasChildMenu ? '<i class="fas fa-angle-left right"></i>' : '';
                                    $btnCreate = $menu['create_route'] ? '<span class="badge cust-bg-info float-right shadow-lg shadow-light bg-success-hover"><i class="fas right fa-solid fa-plus add-new" add-new="' . $createRoute . '"></i></span>' : '';
                                    $rightSideIcon = $menu['route'] ? $btnCreate : $hasDropDown;
                                    $routeList = childrenRouteList($menu['childrenmenu']);
                                    if ($menu['route']) $routeList[] = $menu['route'];
                                    if ($menu['create_route']) $routeList[] = $menu['create_route'];
                                    $active = in_array($currentRoute, $routeList) ? 'active' : '';
                                    $open = in_array($currentRoute, $routeList) ? 'menu-open' : '';



                                    $output .= '<li class="nav-item '. $open .'">';
                                    $output .=      '<a href="'. $route .'" class="nav-link '. $active . '">';
                                    $output .=           $navicon;
                                    $output .=          '<p>' . $menuName . $rightSideIcon . '</p>';
                                    $output .=      '</a>';
                                    if($hasChildMenu && in_array(1,array_column($menu['childrenmenu'], 'is_side_menu'))){
                                        $output .=  '<ul class="nav nav-treeview">';
                                        $output .=      displaySidebarMenuList($menu['childrenmenu'],$authorization);
                                        $output .=  '</ul>';
                                    }
                                    $output .= '</li>';


                                }
                            }
                            return $output;
                        }
                    }
                @endphp
                {!! displaySidebarMenuList($menus,$authorization) !!}
            </ul>
        </nav>
        @php
            echo microtime(true) - $startTime;
        @endphp
    </div>
</aside>
<aside class="control-sidebar control-sidebar-dark"></aside>