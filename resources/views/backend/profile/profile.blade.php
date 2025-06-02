@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="form-group col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Edit Form</h3>
                            </div>
                            <form action="{{ route('profile.update-details', Auth::guard('admin')->user()->id)}}" method="POST" enctype="multipart/form-data">
                              @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Type</label>
                                            <input disabled type="text" class="form-control" id="type" name="type"
                                                value="{{ $data['adminType'] }}" placeholder="Type" required>
                                            </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Email</label>
                                            <input readonly type="email" class="form-control" id="email" name="email"
                                                value="{{ Auth::guard('admin')->user()->email }}" placeholder="Email" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ Auth::guard('admin')->user()->name }}" placeholder="Name" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Mobile</label>
                                            <input type="text" class="form-control" id="mobile" name="mobile"
                                                value="{{ Auth::guard('admin')->user()->mobile }}" placeholder="Mobile" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="mpbtn col-md-3" style="cursor:pointer">
                                                <img id="image_view" style="max-width:100%" class="img-thumbnail" src="{{ asset('public/uploads/admin/'. (Auth::guard('admin')->user()->image? Auth::guard('admin')->user()->image : 'placeholder.png')) }}">
                                                <input id="image" name="image" style="display:none" onchange="profile(this);" type="file" accept="image/*">
                                            </label>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
function profile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#image_view').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection