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
                            <form action="{{ isset($data['item']) ? route('fundtransfers.update',$data['item']->id) : route('fundtransfers.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Transfer From *</label>
                                            <select class="form-control" name="from_account_id" id="from_account_id" required>
                                                <option disabled selected value=''>Select Payment Methods</option>
                                                @foreach ($data['from_accounts'] as $from_account)
                                                    <option account-bal="{{ $from_account['balance'] }}" @selected(isset($data['item']) && $data['item']['from_account_id'] == $from_account['id']) value="{{ $from_account['id'] }}">{{ $from_account['name'] .' : '. $from_account['account_no'] . ' (Bal: ' . $from_account['balance'] }} &#2547;)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Transfer To *</label>
                                            <select class="form-control" name="to_account_id" id="to_account_id" required>
                                                <option disabled selected value=''>Select Payment Methods</option>
                                                @foreach ($data['from_accounts'] as $from_account)
                                                    <option account-bal="{{ $from_account['balance'] }}" @selected(isset($data['item']) && $data['item']['to_account_id'] == $from_account['id']) value="{{ $from_account['id'] }}">{{ $from_account['name'] .' : '. $from_account['account_no'] . ' (Bal: ' . $from_account['balance'] }} &#2547;)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Transaction Date *</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->transfer_date : (old('transfer_date') ? old('transfer_date') : date('Y-m-d')) }}" required type="date" class="form-control" name="transfer_date" id="transfer_date" required>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Amount *</label>
                                            <input value="{{ $data['item']->amount ?? null }}" min='0' type="number" class="form-control" name="amount" id="amount" placeholder="0.00" required>
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
        $('#from_account_id').on('change', function (e) {
            const from_account_id = $(this).val();
            if (from_account_id) {
                $('#to_account_id').prop('disabled', false);
                $('#to_account_id option').prop('hidden', false);
                $(`#to_account_id option[value="${from_account_id}"]`).prop('hidden', true);
            } else {
                $('#to_account_id').prop('disabled', true);
            }
            $('#to_account_id').val('');
        });

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


    </script>
@endsection
