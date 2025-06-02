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
                            <form action="{{ isset($data['item']) ? route('bike-models.update',$data['item']->id) : route('bike-models.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Brands *</label>
                                            <select class="form-control" name="brand_id" id="brand_id" required>
                                                <option disabled selected value=''>Select Brands</option>
                                                @foreach ($data['brands'] as $brand)
                                                    <option @selected(isset($data['item']) && $data['item']['brand_id'] == $brand['id']) value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Model Name *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->name : null }}" type="text" class="form-control" name="name" placeholder="Model Name">
                                        </div>

                                        {{-- <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Manufacture Year *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->manufacture_year : null }}" type="text" class="form-control" name="manufacture_year" placeholder="0.00" required>
                                        </div> --}}
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Engine Capaciy *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->engine_capacity : null }}" type="text" class="form-control" name="engine_capacity" placeholder="0.00" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Status *</label>
                                            <select name="status" id="status" class="form-control">
                                                <option @selected(($data['item']['status'] ?? null) === 1) value="1">Active</option>
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