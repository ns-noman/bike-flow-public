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
                            <form action="{{ route('basic-infos.update', $data['basicInfo']->id)}}" method="POST" enctype="multipart/form-data">
                              @csrf
                              @method('put')
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                            <label class="form-label">Title *</label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ $data['basicInfo']->title }}" placeholder="Title" required>
                                        </div>
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Meta Keywords</label>
                                            <textarea class="form-control" id="meta_keywords" cols="30" rows="1" name="meta_keywords" placeholder="Meta Keywords" required>{{ $data['basicInfo']->meta_keywords }}</textarea>
                                        </div> 
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Meta Description</label>
                                            <textarea class="form-control" id="meta_description" cols="30" rows="1" name="meta_description"  placeholder="Meta Description" required>{{ $data['basicInfo']->meta_description }}</textarea>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label for="logo" class="form-label">Logo (181 X 121)*</label>
                                            <input type="file" name="logo" class="form-control">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label for="favicon" class="form-label">Favicon (32 X 32)*</label>
                                            <input type="file" name="favicon" class="form-control">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="phone" class="form-label">Phone *</label>
                                            <input type="text"  value="{{ $data['basicInfo']->phone }}" name="phone" class="form-control" placeholder="+88 01*********" required>
                                        </div>
                                        <div hidden class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="telephone" class="form-label">Telephone</label>
                                            <input type="text"  value="{{ $data['basicInfo']->telephone }}" name="telephone" class="form-control" placeholder="+88 02-******, ******," required>
                                        </div>
                                        <div hidden class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="fax" class="form-label">Fax</label>
                                            <input type="text"  value="{{ $data['basicInfo']->fax }}" name="fax" class="form-control" placeholder="+88 02-******" required>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email"  value="{{ $data['basicInfo']->email }}" name="email" class="form-control" placeholder="example@gmail.com" required>
                                        </div>

 
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Location</label>
                                            <textarea class="form-control" id="location" cols="30" rows="1" name="location" placeholder="Location" required>{{ $data['basicInfo']->location }}</textarea>
                                        </div> 
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" id="address" cols="30" rows="1" name="address"  placeholder="Address" required>{{ $data['basicInfo']->address }}</textarea>
                                        </div>





                                        
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label for="web_link" class="form-label">Web Link</label>
                                            <input type="text" value="{{ $data['basicInfo']->web_link }}" name="web_link" class="form-control" placeholder="https://site-name.com/account-name">
                                        </div>
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label for="facebook_link" class="form-label">Facebook Link</label>
                                            <input type="text" value="{{ $data['basicInfo']->facebook_link }}" name="facebook_link" class="form-control" placeholder="https://site-name.com/account-name">
                                        </div>
                                        <div hidden class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label for="twitter_link" class="form-label">Twitter Link</label>
                                            <input type="text" value="{{ $data['basicInfo']->twitter_link }}" name="twitter_link" class="form-control" placeholder="https://site-name.com/account-name">
                                        </div>
                                        <div hidden class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label for="linkedin_link" class="form-label">Linkedin Link</label>
                                            <input type="text" value="{{ $data['basicInfo']->linkedin_link }}" name="linkedin_link" class="form-control" placeholder="https://site-name.com/account-name">
                                        </div>
                                        <div hidden class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label for="youtube_link" class="form-label">Youtube Link</label>
                                            <input type="text" value="{{ $data['basicInfo']->youtube_link }}" name="youtube_link" class="form-control" placeholder="https://site-name.com/account-name">
                                        </div>



                                        <div hidden class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="assets_value" class="form-label">Assets Value ($ Million)*</label>
                                            <input type="number" placeholder="0.00" name="assets_value" value="{{ $data['basicInfo']->assets_value }}" class="form-control" required>
                                        </div>
                                        <div hidden class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="total_employees" class="form-label">Total Employees *</label>
                                            <input type="number" placeholder="0.00" name="total_employees" value="{{ $data['basicInfo']->total_employees }}" class="form-control" required>
                                        </div>
                                        <div hidden class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="total_companies" class="form-label">Total Companies *</label>
                                            <input type="number" placeholder="0.00" name="total_companies" value="{{ $data['basicInfo']->total_companies }}" class="form-control" required>
                                        </div>
                                        <div hidden class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label for="start_year" class="form-label">Start Year *</label>
                                            <input type="number" placeholder="0.00" name="start_year" value="{{ $data['basicInfo']->start_year }}" class="form-control" required>
                                        </div>

                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Map *</label>
                                            <textarea class="form-control" id="map_embed" cols="30" rows="2" name="map_embed" placeholder="Meta Keywords" required>{{ $data['basicInfo']->map_embed }}</textarea>
                                        </div> 
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Video One *</label>
                                            <textarea class="form-control" id="video_embed_1" cols="30" rows="2" name="video_embed_1" placeholder="Meta Keywords" required>{{ $data['basicInfo']->video_embed_1 }}</textarea>
                                        </div> 
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Video Two *</label>
                                            <textarea class="form-control" id="video_embed_2" cols="30" rows="2" name="video_embed_2" placeholder="Meta Keywords" required>{{ $data['basicInfo']->video_embed_2 }}</textarea>
                                        </div> 
                                        <div hidden class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label class="form-label">Video Three *</label>
                                            <textarea class="form-control" id="video_embed_3" cols="30" rows="2" name="video_embed_3" placeholder="Meta Keywords" required>{{ $data['basicInfo']->video_embed_3 }}</textarea>
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
    $('#MetaDescription').summernote({
        placeholder: 'Meta Description',
        tabsize: 2,
        height: 100
    });
    $('#GoogleMap').summernote({
        placeholder: 'Google Map',
        tabsize: 2,
        height: 100
    });
    $('#GoogleAnalytics').summernote({
        placeholder: 'Google Analytics',
        tabsize: 2,
        height: 100
    });
    $('#Address').summernote({
        placeholder: 'Address',
        tabsize: 2,
        height: 100
    });
</script>
@endsection