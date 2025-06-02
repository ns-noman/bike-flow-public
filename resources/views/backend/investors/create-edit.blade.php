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
                            <form action="{{ isset($data['item']) ? route('investors.update',$data['item']->id) : route('investors.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Name *</label>
                                            <input value="{{ $data['item']->name ?? null }}" required type="text" class="form-control" name="name" id="name" placeholder="Name">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Email *</label>
                                            <input value="{{ $data['item']->email ?? null }}" required type="text" class="form-control" name="email" id="email" placeholder="Email">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-4 col-lg-4">
                                            <label>Contact *</label>
                                            <input value="{{ $data['item']->contact ?? null }}" required type="number" class="form-control" name="contact" id="contact" placeholder="Contact">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-4 col-lg-4">
                                            <label>Date Of Birth *</label>
                                            <input value="{{ $data['item']->dob ?? null }}" required type="date" class="form-control" name="dob" id="dob">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-4 col-lg-4">
                                            <label>NID *</label>
                                            <input value="{{ $data['item']->nid ?? null }}" required type="number" class="form-control" name="nid" id="nid" placeholder="NID">
                                        </div>
                                        
                                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
                                            <label>Address</label>
                                            <textarea required type="text" class="form-control" name="address" id="address"
                                                placeholder="Address" cols="30" rows="1">{{ $data['item']->address ?? null }}</textarea>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option @selected(($data['item']->status ?? null) === 1) value="1">Active</option>
                                                <option @selected(($data['item']->status ?? null) === 0) value="0">Inactive</option>
                                            </select>
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