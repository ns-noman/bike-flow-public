@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary p-1">
                                <h3 class="card-title">
                                    <a href="{{ route('menus.create') }}"class="btn btn-light shadow rounded m-0"><i
                                            class="fas fa-plus"></i>
                                        <span>Add New</span></i></a>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="bootstrap-data-table-panel">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-sm table-striped table-bordered table-centre">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Menu Name</th>
                                                    <th>Routes</th>
                                                    <th>Side Menu</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                if (!function_exists('displayMenuTable')) {
                                                    function displayMenuTable($menus, $level = 0, &$sn = 1) {
                                                            $html = '';
                                                            foreach ($menus as $key => $menu) {
                                                                $colors = ['success', 'info', 'warning', 'danger', 'dark','secondary'];
                                                                $menuID = $menu['id'];
                                                                $srln = '<span class="badge badge-'.$colors[$level].'">'. $menu['srln'] .'</span>';
                                                                $prefix = makePrefix($level);
                                                                $icon = $menu['navicon'] ? $menu['navicon'] : '';
                                                                $createRoute = $menu['create_route'] ? ' [create-route:- '. $menu['create_route'] . ']' : '';

                                                                $menuName = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $prefix . $srln . ' ' .  $icon . '&nbsp;' . $menu['menu_name'];
                                                                $allRoutes = $menu['route'] . $createRoute;
                                                                $sideMenus = '<span class="badge badge-'.($menu['is_side_menu'] ? 'info' : 'danger').'">' . ($menu['is_side_menu'] ? 'Yes' : 'No') . '</span>';
                                                                $status = '<span class="badge badge-'.($menu['status'] ? 'success' : 'warning').'">' . ($menu['status'] ? 'Active' : 'Inactive') . '</span>';
                                                                $addRoute = route("menus.edit", [$menuID, "addmenu"]);
                                                                $editRoute = route("menus.edit", $menuID);
                                                                $destroyRoute = route("menus.destroy", $menuID);


                                                                $html .= '<tr>';
                                                                $html .= '<td>' . $sn++ . '</td>';
                                                                $html .= '<td>' . $menuName . '</td>';
                                                                $html .= '<td>' . $allRoutes . '</td>';
                                                                $html .= '<td>' . $sideMenus . '</td>';
                                                                $html .= '<td>'. $status .'</td>';
                                                                $html .= '
                                                                    <td>
                                                                        <div class="d-flex justify-content-center">
                                                                            <a href="' . $addRoute . '" class="btn btn-sm btn-success mx-1">
                                                                                <i class="fas right fa-solid fa-plus"></i>
                                                                            </a>
                                                                            <a href="' . $editRoute . '" class="btn btn-sm btn-info">
                                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                                            </a>
                                                                             <form class="delete" action="'. $destroyRoute .'" method="post" hidden>
                                                                                <input type="hidden" name="_token" value="'.csrf_token().'">
                                                                                <input type="hidden" name="_method" value="DELETE">
                                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                ';
                                                                $html .= '</tr>';
                                                                if ($menu['childrenforcreatemenu']) {
                                                                    $html .= displayMenuTable($menu['childrenforcreatemenu'], $level + 1,$sn);
                                                                }
                                                            }

                                                            return $html;
                                                            
                                                        }
                                                    }
                                                    function makePrefix($level)
                                                    {
                                                        return str_repeat(str_repeat('&nbsp;', 14), $level);
                                                    }

                                                @endphp
                                                {!! displayMenuTable($data['menus']) !!}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection
