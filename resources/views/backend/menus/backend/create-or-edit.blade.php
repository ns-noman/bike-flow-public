@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ $data['title'] }} Form</h3>
                            </div>
                            <form action="{{ isset($data['item']) ? route('menus.update',$data['item']['id']) : route('menus.store'); }}" method="POST">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-3 col-lg-3">
                                            <label>Parent Menu</label>
                                            <select name="parent_id" id="parent_id" class="form-control select2" required>
                                                <option value="0">Select Parent Menu</option>
                                                {!! (function($menus, $data) {
                                                    $displayMenuOption = function($menus, $level = 0, $data, &$srl = 1) use (&$displayMenuOption) {
                                                        $options = '';
                                                        foreach ($menus as $key => $menu) {
                                                            if ($level == 0) {
                                                                $arrow = '&#11042; ';
                                                            } elseif ($level == 1) {
                                                                $arrow = '&nbsp;&nbsp;&rArr;';
                                                            } elseif ($level == 2) {
                                                                $arrow = '&nbsp;&nbsp;&nbsp;&rarr;';
                                                            } elseif ($level == 3) {
                                                                $arrow = '&nbsp;&nbsp;&nbsp;&nbsp;&rarr;';
                                                            }
                                                            $selected = (isset($data['addmenu']) && $data['addmenu']['id'] == $menu['id']) || (isset($data['item']) && $data['item']['parent_id'] == $menu['id']) ? 'selected' : null;
                                                            $options .= '<option ' . $selected . ' value="' . $menu['id'] . '">' . $arrow . $menu['menu_name'] . '</option>';
                                                            if (isset($menu['children'])) {
                                                                $options .= $displayMenuOption($menu['children'], $level + 1, $data, $srl);
                                                            }
                                                        }
                                                        return $options;
                                                    };
                                                    return $displayMenuOption($menus, 0, $data);
                                                })($data['menus'], $data) !!}
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-3 col-lg-3">
                                            <label>Menu Name *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']['menu_name'] : null }}" type="text" class="form-control" name="menu_name" placeholder="Menu Name" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-3 col-lg-3">
                                            <label>Route</label>
                                            <input value="{{ isset($data['item']) ? $data['item']['route'] : null }}" type="text" class="form-control" name="route" placeholder="route.index">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-3 col-lg-3">
                                            <label>Create Route</label>
                                            <input value="{{ isset($data['item']) ? $data['item']['create_route'] : null }}" type="text" class="form-control" name="create_route" placeholder="route.index">
                                        </div>

                                        
                                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                            <label>Serial No.</label>
                                            <input value="{{ isset($data['item']) ? $data['item']['srln'] : null }}" type="number" class="form-control" name="srln" placeholder="0.00">
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                            <label>Nav Icon</label>
                                            <input value="{{ isset($data['item']) ? $data['item']['navicon'] : null }}" type="text" class="form-control" name="navicon" placeholder='<i class="nav-icon fa-solid fa-list-check m-1"></i>'>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                            <label>&nbsp;</label>
                                            <div class="form-group clearfix">
                                                <div class="icheck-success d-inline">
                                                  <input name="is_side_menu" type="checkbox" {{ isset($data['item']) ? $data['item']['is_side_menu'] ? 'checked' : '' : '' }} id="is_side_menu">
                                                  <label for="is_side_menu" >Is Side Menu?</label>
                                                </div>
                                                <div class="icheck-info d-inline ml-3">
                                                  <input name="status" type="checkbox" {{ isset($data['item']) ? $data['item']['status'] ? 'checked' : '' : 'checked' }} id="status">
                                                  <label for="status">Active?</label>
                                                </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
