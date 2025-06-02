@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ $data['title'] }} Form</h3>
                            </div>
                            <form action="{{ isset($data['items']) ? route('roles.update',$data['items']->id) : route('roles.store'); }}" method="POST">
                                @csrf()
                                @if(isset($data['items']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-10 col-md-10 col-lg-10">
                                            <label for="role">Role Name *</label>
                                            <input value="{{ isset($data['items']) ? $data['items']->role : null }}" type="text" class="form-control" id="role" placeholder="Role Name" name="role" required>
                                        </div>
                                        <div class="form-group col-sm-2 col-md-2 col-lg-2">
                                            <label for="toggle-select">&nbsp;</label>
                                            <button type="button" id="toggle-select" class="form-control btn btn-info">Select All</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @php
                                            if (!function_exists('displayMenuList')) {
                                                function displayMenuList($menus, $level = 0, $levelbg, $data) {
                                                    $html = '<ol>';
                                                    foreach ($menus as $menu) {
                                                        $colorClass = 'level-' . $level;
                                                        $menuName = '<div class="custom-control custom-switch '.$levelbg[$level].' custom-switch-on-success '.$colorClass.'">
                                                                        <input value="'.$menu['id'].'" '.(isset($data['items']) ? in_array($menu['id'], $data['items']->menu_ids)?'checked' : null : null).' name="menu_id[]" type="checkbox" class="custom-control-input menu-checkbox" id="menu-id-'.$menu['id'].'" data-level="'.$level.'">
                                                                        <label class="custom-control-label" for="menu-id-'.$menu['id'].'">'.$menu['menu_name'].'</label>
                                                                    </div>';
                                                        $html .= '<li>' . $menuName;
                                                        if (!empty($menu['children'])) {
                                                            $html .= displayMenuList($menu['children'], $level + 1, $levelbg, $data);
                                                        }
                                                        $html .= '</li>';
                                                    }
                                                    $html .= '</ol>';
                                                    return $html;
                                                }
                                            }

                                            $levelbg = ['custom-switch-off-primary','custom-switch-off-info','custom-switch-off-danger','custom-switch-off-dark'];
                                            $menusArray = $data['menus']->toArray();
                                            $partSize = ceil(count($menusArray) / 4);
                                            $firstPart = array_slice($menusArray, 0, $partSize);
                                            $secondPart = array_slice($menusArray, $partSize, $partSize);
                                            $thirdPart = array_slice($menusArray, 2 * $partSize, $partSize);
                                            $fourthPart = array_slice($menusArray, 3 * $partSize);
                                        @endphp
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            {!! displayMenuList($firstPart, 0, $levelbg, $data) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            {!! displayMenuList($secondPart, 0, $levelbg, $data) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            {!! displayMenuList($thirdPart, 0, $levelbg, $data) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            {!! displayMenuList($fourthPart, 0, $levelbg, $data) !!}
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
@section('script')
<script>
    $(document).ready(function(){
        // Track the selection state
        let isAllSelected = false;

        // Toggle all checkboxes
        $('#toggle-select').on('click', function() {
            isAllSelected = !isAllSelected;
            $('.menu-checkbox').prop('checked', isAllSelected).trigger('change');
            $(this).text(isAllSelected ? 'Deselect All' : 'Select All');
        });

        // When a checkbox is clicked
        $(document).on('change', '.menu-checkbox', function() {
            let isChecked = $(this).is(':checked');
            let level = $(this).data('level');

            // Find all checkboxes below the current level and check/uncheck them
            $(this).closest('li').find('input.menu-checkbox').each(function() {
                $(this).prop('checked', isChecked);
            });

            // Ensure parent checkboxes are checked when child is checked
            if (isChecked) {
                $(this).parents('li').each(function() {
                    $(this).find('> .custom-control > .menu-checkbox').prop('checked', true);
                });
            }
        });

        // Validate form before submitting
        $('form').on('submit', function(event) {
            if ($('.menu-checkbox:checked').length === 0) {
                event.preventDefault();
                alert('Please select at least one menu.');
            }
        });
    });
</script>
@endsection
