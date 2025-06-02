@extends('layouts.admin.master')
@section('content')
<style>
    table td, table th{
        padding: 3px!important;
        text-align: center;
    }
    input[type="number"]{
        text-align: right;
    }
    .item{
        text-align: left;
    }
    .form-group{
        padding: 2px;
        margin: 0px;
    }
    label{
        margin-bottom: 0px;
    }
</style>
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
                            <form id="form-submit" action="{{ isset($data['item']) ? route('loans.update',$data['item']->id) : route('loans.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Party *</label>
                                            <select name="party_id" id="party_id" class="form-control select2" required>
                                                <option value="" selected>Select Party</option>
                                                @foreach ($data['parties'] as $party)
                                                    <option
                                                        @isset($data['item']) @selected($party->id == $data['item']->party_id) @endisset
                                                        value="{{ $party->id }}">
                                                        {{ $party->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Loan Date *</label>
                                            <input name="loan_date" id="loan_date" type="date"
                                                value="{{ isset($data['item']) ? $data['item']->loan_date : date('Y-m-d') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Due Date *</label>
                                            <input name="due_date" id="due_date" type="date"
                                                value="{{ isset($data['item']) ? $data['item']->due_date : date('Y-m-d') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Loan Type *</label>
                                            <select name="loan_type" id="loan_type" class="form-control" required>
                                                <option selected>Select Loan Type</option>
                                                <option @selected(isset($data['item']) && $data['item']['loan_type'] == 0) value="0" >Loan Given</option>
                                                <option @selected(isset($data['item']) && $data['item']['loan_type'] == 1) value="1">Loan Taken</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Amount</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->amount : null }}" type="number" class="form-control" name="amount" id="amount" placeholder="0.00">
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Payment Methods *</label>
                                            <select class="form-control" name="account_id" id="account_id">
                                                <option selected value=''>Select Payment Methods</option>
                                                @foreach ($data['paymentMethods'] as $paymentMethod)
                                                    <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] . ' (Bal: ' . $paymentMethod['balance'] }} &#2547;)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Refference Number</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->reference_number : null }}"
                                                class="form-control" type="text" name="reference_number" id="reference_number"
                                                placeholder="Reference Number">
                                        </div>
                                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                            <label>Note</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->note : null }}"
                                                class="form-control" type="text" name="note" id="note"
                                                placeholder="Note">
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
        $('#form-submit').submit(function(e) {
            let loan_type = $('#loan_type').val();
            let amount = $('#amount').val();
            if(parseFloat($('#amount').val())>0 && !$('#account_id option:selected').val()){
                e.preventDefault();
                Swal.fire("Please Select Payment Method");
            }
            if(loan_type==0 && amount > parseFloat($('#account_id option:selected').attr('account-bal'))){
                e.preventDefault();
                Swal.fire("Account Balance Exceed!");
            }
        });
        
     
    </script>
@endsection
