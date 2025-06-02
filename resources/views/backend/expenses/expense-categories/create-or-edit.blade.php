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
                            <form action="{{ isset($data['expense_category']) ? route('expense-categories.update',$data['expense_category']->id) : route('expense-categories.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['expense_category']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                            <label>Category Name</label>
                                            <input value="{{ isset($data['expense_category']) ? $data['expense_category']->cat_name : null }}" type="text" class="form-control" name="cat_name" placeholder="Category Name" required>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                            <label>Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option {{ isset($data['expense_category']) ? $data['expense_category']->status == 1 ? 'selected' : null : null }} value="1">Active</option>
                                                <option {{ isset($data['expense_category']) ? $data['expense_category']->status == 0 ? 'selected' : null : null }} value="0">Inactive</option>
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