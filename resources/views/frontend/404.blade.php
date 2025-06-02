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
		<div class="error-404 section-bg pt-100 pb-100">
			<div class="container">
				<div class="row">
					<div class="col-lg-8 offset-lg-2 text-center">
						<div class="error-404-content">
							<img src="{{ asset("public/frontend-assets/img/404.png") }}" alt="img">
							<h2>Oops! Page not found.</h2>
							<p> The page you are looking for is not available or doesn’t belong to this website! </p>
							<a class="button-1" href="{{ route('home.index') }}"><i class="far fa-share-square"></i> Go To Home </a>
						</div>
					</div>
				</div>
			</div>
		</div>
        @include('layouts.frontend.scripts')
        @yield('script')
    </body>
</html>

      
