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
                            <form action="{{ isset($data['item']) ? route('expense-heads.update',$data['item']->id) : route('expense-heads.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Expense Category *</label>
                                            <select class="form-control" name="expense_category_id" id="expense_category_id" required>
                                                <option disabled selected value=''>Select Expense Category</option>
                                                @foreach ($data['expenseCategories'] as $expcat)
                                                    <option @selected(isset($data['item']) && $data['item']['expense_category_id'] == $expcat['id']) value="{{ $expcat['id'] }}">{{ $expcat['cat_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                            <label>Expense Head *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->title : null }}" type="text" class="form-control" name="title" placeholder="Expense Head" required>
                                        </div>
                                        {{-- <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                            <label>Code</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->code : null }}" type="text" class="form-control" name="code" placeholder="Code">
                                        </div> --}}
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