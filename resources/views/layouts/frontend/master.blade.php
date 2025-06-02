@php
    $basicInfo = Cache::remember('basic_info', 60, function () {
        return \App\Models\BasicInfo::first();
    });
    $frontendmenus = \App\Models\FrontendMenu::with('frontendsubmenus.frontendsubmenus')->where(['parent_id'=>0 ,'is_in_menus'=>1, 'status'=>1])->select(['id', 'parent_id', 'title', 'slug'])->orderBy('srln')->get()->toArray();
    $pages = \App\Models\FrontendMenu::where(['is_in_pages'=>1, 'status'=>1])->select(['title', 'slug'])->orderBy('srln')->get()->toArray();
@endphp
<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ $basicInfo->title }}</title>
		@include('layouts.frontend.links')

</head>
    <body>
        @include('layouts.frontend.header')
        @yield('content')
        @include('layouts.frontend.footer')
        @include('layouts.frontend.scripts')
        @yield('script')
    </body>
</html>

      
