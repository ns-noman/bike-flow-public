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
                            <form action="{{ isset($data['item']) ? route('accounts.update',$data['item']->id) : route('accounts.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Payment Methods *</label>
                                            <select class="form-control" name="payment_method_id" id="payment_method_id" required>
                                                <option disabled selected value=''>Select Payment Methods</option>
                                                @foreach ($data['paymentMethods'] as $paymentMethod)
                                                    <option @selected(isset($data['item']) && $data['item']['payment_method_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Account No/Description *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->account_no : null }}" type="text" class="form-control" name="account_no" placeholder="Account No/Description" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Account Holder</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->holder_name : null }}" type="text" class="form-control" name="holder_name" placeholder="Account Holder">
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