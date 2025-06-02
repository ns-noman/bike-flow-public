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
                            <form action="{{ isset($data['item']) ? route('frontend-menus.update',$data['item']['id']) : route('frontend-menus.store'); }}" method="POST">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Parent Menu</label>
                                            <select name="parent_id" id="parent_id" class="form-control" required>
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
                                                            $options .= '<option ' . $selected . ' value="' . $menu['id'] . '">' . $arrow . $menu['title'] . '</option>';
                                                            if (isset($menu['children_create'])) {
                                                                $options .= $displayMenuOption($menu['children_create'], $level + 1, $data, $srl);
                                                            }
                                                        }
                                                        return $options;
                                                    };
                                                    return $displayMenuOption($menus, 0, $data);
                                                })($data['menus'], $data) !!}
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Menu Name *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']['title'] : null }}" type="text" class="form-control" name="title" placeholder="Menu Name" required>
                                            @error('title')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Serial No.</label>
                                            <input value="{{ isset($data['item']) ? $data['item']['srln'] : null }}" type="number" class="form-control" name="srln" placeholder="0.00" required>
                                            @error('srln')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>&nbsp;</label>
                                            <div class="form-group clearfix">
                                                <div class="icheck-success d-inline">
                                                  <input value="1" name="is_in_menus" type="checkbox" {{ isset($data['item']) ? $data['item']['is_in_menus'] ? 'checked' : '' : 'checked' }} id="is_in_menus">
                                                  <label for="is_in_menus" >Is In Menu List?</label>
                                                </div>
                                                <div class="icheck-danger d-inline ml-3">
                                                  <input value="1" name="is_in_pages" type="checkbox" {{ isset($data['item']) ? $data['item']['is_in_pages'] ? 'checked' : '' : '' }} id="is_in_pages">
                                                  <label for="is_in_pages">Is In Page List?</label>
                                                </div>
                                                <div class="icheck-info d-inline ml-3">
                                                  <input value="1" name="status" type="checkbox" {{ isset($data['item']) ? $data['item']['status'] ? 'checked' : '' : 'checked' }} id="status">
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
