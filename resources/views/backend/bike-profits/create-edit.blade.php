@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header" style="padding: 5px;">
                                <a href="{{ route('bike-profits.index') }}"class="btn btn-warning shadow rounded m-0 text-dark">
                                    <span>Back to Bike Profit</span></a>
                            </div>
                            <form action="{{ isset($data['item']) ? route('bike-profits.update',$data['item']->id) : route('bike-profits.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <input type="hidden" name="bike_profit_id" value="{{ $data['bike_profit']->id }}">
                                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                            <label>Bike Info *</label>
                                            <input value="{{ $data['bike']['model_name'] }} {{ $data['bike']['color_name'] }}, Ch#${{ $data['bike']['chassis_no'] }}, Reg#{{ $data['bike']['registration_no'] }}" type="text" class="form-control"  readonly>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Payment Methods *</label>
                                            <select class="form-control" name="account_id" id="account_id" required>
                                                <option disabled selected value=''>Select Payment Methods</option>
                                                @foreach ($data['paymentMethods'] as $paymentMethod)
                                                    <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] . ' (Bal: ' . $paymentMethod['balance'] }} &#2547;)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Date *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->date : date('Y-m-d') }}" required type="date" class="form-control" name="date" id="date" required>
                                        </div>
                                        <div class="form-group col-12">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label>Profit Amount</label>
                                                    <input value="{{ $data['bike_profit']->profit_amount }}" min="0" type="number" class="form-control" name="profit_amount" id="profit_amount" placeholder="0.00" readonly>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Total Shared</label>
                                                    <input value="{{ $data['bike_profit']->profit_share_amount }}" class="form-control" readonly>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>Amount *</label>
                                                    <input value="{{  isset($data['item']) ? $data['item']->amount : null }}" min="0" max="{{ $data['bike_profit']->profit_amount - $data['bike_profit']->profit_share_amount }}" type="number" class="form-control" name="amount" id="amount" placeholder="0.00" required>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Reference Number</label>
                                                    <input value="{{  isset($data['item']) ? $data['item']->reference_number : null }}" type="text" class="form-control" name="reference_number" id="reference_number" placeholder="XXXXXXXXXX">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Note</label>
                                                    <input value="{{  isset($data['item']) ? $data['item']->note : null }}" type="text" class="form-control" name="note" id="note" placeholder="Note">
                                                </div>
                                            </div>
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