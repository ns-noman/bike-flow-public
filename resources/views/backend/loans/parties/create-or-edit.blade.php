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
                            <form action="{{ isset($data['item']) ? route('parties.update',$data['item']->id) : route('parties.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Party Name *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->name : null }}" type="text" class="form-control" name="name" placeholder="Party Name" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Phone *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->phone : null }}" type="number" class="form-control" name="phone" placeholder="+8801XXXXXXXXX" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Email</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->email : null }}" type="email" class="form-control" name="email" placeholder="example@gmail.com">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Address</label>
                                            <textarea class="form-control" name="address" placeholder="Address" cols="30" rows="1">{{ isset($data['item']) ? $data['item']->address : null }}</textarea>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>NID</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->nid_number : null }}" type="nid_number" class="form-control" name="nid_number" placeholder="NID">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>DOB</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->date_of_birth : null }}" type="date" class="form-control" name="date_of_birth" placeholder="Date Of Birth">
                                        </div>
                                        {{-- @if(!isset($data['item']))
                                            <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                                <label>Opening Payable</label>
                                                <input value="{{ isset($data['item']) ? $data['item']->opening_payable : null }}" type="number" class="form-control" name="opening_payable" placeholder="0.00">
                                            </div>
                                        @endif
                                        @if(!isset($data['item']))
                                            <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                                <label>Opening Receivable</label>
                                                <input value="{{ isset($data['item']) ? $data['item']->opening_receivable : null }}" type="number" class="form-control" name="opening_receivable" placeholder="0.00">
                                            </div>
                                        @endif --}}
                                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                            <label>Status *</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{ isset($data['item']) ? $data['item']->status == 1 ? 'selected' : null : null }} value="1">Active</option>
                                                <option {{ isset($data['item']) ? $data['item']->status == 0 ? 'selected' : null : null }} value="0">Inactive</option>
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