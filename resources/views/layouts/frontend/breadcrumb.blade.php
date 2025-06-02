@php
    $default = ['title'=>'Default'];
    $content = $data['breadcrumb'] ?? $default;
@endphp
<section class="breadcrumb-area" style=" padding: 30px;background-image:url({{ asset("public/frontend-assets/img/breadcrumb.jpg") }});">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-content">
                    <h2>{{ $content['title'] }}</h2>
                    <ul>
                        <li><a href="{{ route('home.index') }}">Home</a></li>
                        <li><i class="fas fa-angle-double-right"></i></li>
                        <li>{{ $content['title'] }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>