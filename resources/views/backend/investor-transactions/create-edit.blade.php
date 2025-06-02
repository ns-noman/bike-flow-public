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
                            <form action="{{ isset($data['item']) ? route('investor-transactions.update',$data['item']->id) : route('investor-transactions.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Investors *</label>
                                            <select class="form-control" name="investor_id" id="investor_id" required>
                                                <option disabled selected value=''>Select Investors</option>
                                                @foreach ($data['investors'] as $investors)
                                                    <option @selected(isset($data['item']) && $data['item']['investor_id'] == $investors['id']) value="{{ $investors['id'] }}">{{ $investors['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Payment Methods *</label>
                                            <select class="form-control" name="account_id" id="account_id" required>
                                                <option disabled selected value=''>Select Payment Methods</option>
                                                @foreach ($data['paymentMethods'] as $paymentMethod)
                                                    <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] . ' (Bal: ' . $paymentMethod['balance'] }} &#2547;)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Transaction Type *</label>
                                            <select class="form-control" name="transaction_type" id="transaction_type" required>
                                                <option disabled selected value=''>Select Transaction Type</option>
                                                <option {{ isset($data['item']) && $data['item']->credit_amount ? 'selected' : null }} value='credit_amount'>Deposit</option>
                                                <option {{ isset($data['item']) && $data['item']->debit_amount ? 'selected' : null }} value='debit_amount'>Withdrawal</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Transaction Date *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->transaction_date : (old('transaction_date') ? old('transaction_date') : date('Y-m-d')) }}" required type="date" class="form-control" name="transaction_date" id="transaction_date" required>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Amount *</label>
                                            <input value="{{ isset($data['item']) ? ($data['item']->credit_amount + $data['item']->debit_amount) : old('amount') }}" min='0' type="number" class="form-control" name="amount" id="amount" placeholder="0.00" required>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Reference Number</label>
                                            <input value="{{ $data['item']->reference_number ?? null }}" type="text" class="form-control" name="reference_number" id="reference_number" placeholder="XXXXXXXXXX">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Description</label>
                                            <input value="{{ $data['item']->description ?? null }}" type="text" class="form-control" name="description" id="description" placeholder="Description">
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
    $(document).ready(function(){
        $('form').on('submit', function(e) {
            const transaction_type = $('#transaction_type option:selected').val();
            if(transaction_type=='debit_amount'){
                const account_bal = parseFloat($('#account_id option:selected').attr('account-bal'));
                const amount = parseFloat($('#amount').val());
                if(amount>account_bal){
                    message({success:false, message: 'Account Balance Exceeded!'});
                    e.preventDefault();
                }
            }
        });
    });
</script>
@endsection