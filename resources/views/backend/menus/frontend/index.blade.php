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
                                    <a href="{{ route('frontend-menus.create') }}"class="btn btn-light shadow rounded m-0"><i
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
                                                    <th>In Menu List</th>
                                                    <th>In Page List</th>
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
                                                                $prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level);


                                                                $menuName = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $prefix . $srln . ' ' . '&nbsp;' . $menu['title'];
                                                                $inMenuList = '<span class="badge badge-'.($menu['is_in_menus'] ? 'info' : 'danger').'">' . ($menu['is_in_menus'] ? 'Yes' : 'No') . '</span>';
                                                                $inPageList = '<span class="badge badge-'.($menu['is_in_pages'] ? 'info' : 'danger').'">' . ($menu['is_in_pages'] ? 'Yes' : 'No') . '</span>';
                                                                $status = '<span class="badge badge-'.($menu['status'] ? 'success' : 'warning').'">' . ($menu['status'] ? 'Active' : 'Inactive') . '</span>';
                                                                $addRoute = route("frontend-menus.edit", [$menuID, "addmenu"]);
                                                                $editRoute = route("frontend-menus.edit", $menuID);


                                                                $html .= '<tr>';
                                                                $html .= '<td>' . $sn++ . '</td>';
                                                                $html .= '<td>' . $menuName . '</td>';
                                                                $html .= '<td>' . $inMenuList . '</td>';
                                                                $html .= '<td>' . $inPageList . '</td>';
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
                                                                        </div>
                                                                    </td>
                                                                ';
                                                                $html .= '</tr>';
                                                                if ($menu['children_index']) {
                                                                    $html .= displayMenuTable($menu['children_index'], $level + 1,$sn);
                                                                }
                                                            }

                                                            return $html;
                                                            
                                                        }
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
