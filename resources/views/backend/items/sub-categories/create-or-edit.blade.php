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
                            <form action="{{ isset($data['item']) ? route('sub-categories.update',$data['item']->id) : route('sub-categories.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                            <label>Sub Category Name *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->title : null }}" type="text" class="form-control" name="title" placeholder="Category Name" required>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                            <label>Category *</label>
                                            <select name="parent_cat_id" id="parent_cat_id" class="form-control" required>
                                                <option value="">Select Category Type</option>
                                                @foreach($data['categories'] as $key => $category)
                                                    <option  @isset($data['item']) @if( $data['item']->parent_cat_id == $category->id ) selected @endif @endisset value="{{ $category->id }}">{{ $category->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                            <label>Status *</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{ isset($data['item']) ? $data['item']->status == 1 ? 'selected' : null : null }} value="1">Active</option>
                                                <option {{ isset($data['item']) ? $data['item']->status == 0 ? 'selected' : null : null }} value="0">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                            <label>Image (1:1)</label>
                                            <label class="col-md-3" style="cursor:pointer">
                                                @php
                                                    $imgUrl = asset('public/uploads/category/placeholder.png');
                                                    if((isset($data['item']) && $data['item']->image!=null)) $imgUrl = asset('public/uploads/category/'.$data['item']->image) 
                                                @endphp
                                                <img id="image_view" style="max-width:100%" class="img-thumbnail" src="{{ $imgUrl }}">
                                                <input id="image" name="image" style="display:none" onchange="profile(this);" type="file" accept="image/*">
                                            </label>
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