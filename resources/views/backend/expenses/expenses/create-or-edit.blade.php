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
                            <form id="form-submit" action="{{ isset($data['item']) ? route('expenses.update',$data['item']->id) : route('expenses.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Date</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->date : date('Y-m-d') }}" type="date" class="form-control" name="date" required>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Category</label>
                                            <select class="form-control select2" id="category_id">
                                                <option value="" selected>Select Category</option>
                                                @foreach($data['expense_categories'] as $key => $expense_category)
                                                    <option value="{{ $expense_category->id }}" cat_name="{{ $expense_category->cat_name }}">{{ $expense_category->cat_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Expense Head</label>
                                            <select class="form-control select2" id="expense_head_id_temp">
                                                <option value="" selected disabled>Select Head</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                            <div class="bootstrap-data-table-panel text-center">
                                                <div class="table-responsive">
                                                <table id="expanse-table" class="table table-striped table-bordered table-centre p-0 m-0">
                                                    <thead>
                                                        <tr>
                                                            <th width="2.5%">SN</th>
                                                            <th width="35%">Expense Head</th>
                                                            <th width="15%">Quantity</th>
                                                            <th width="15%">Amount</th>
                                                            <th width="15%">Total</th>
                                                            <th width="15%">Remarks</th>
                                                            <th width="2.5%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">
                                                        @if(isset($data['item']))
                                                            @foreach($data['expense_detals'] as $key => $value)
                                                                <tr>
                                                                    <td class="serial">{{ $loop->iteration }}</td>
                                                                    <td><input type="hidden" value="{{ $value->expense_head->id  }}" name="expense_head_id[]">{{ $value->expense_head->title  }}</td>
                                                                    <td><input type="number" value="{{ $value->quantity }}" class="form-control form-control-sm calculate" min="" name="quantity[]" placeholder="0.00"></td>
                                                                    <td><input type="number" value="{{ $value->amount }}" class="form-control form-control-sm calculate" name="amount[]" placeholder="0.00"></td>
                                                                    <td><input type="number" value="{{ $value->amount * $value->quantity }}" class="form-control form-control-sm" name="total[]" placeholder="0.00" disabled></td>
                                                                    <td><input type="text"   value="{{ $value->note_temp }}" class="form-control form-control-sm" name="note_[]"></td>
                                                                    <td><button class="btn btn-sm btn-danger btn-del" type="button"><i class="fa-solid fa-trash btn-del"></i></button></td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Payment Methods *</label>
                                            <select class="form-control" name="account_id" id="account_id" required>
                                                <option disabled selected value=''>Select Payment Methods</option>
                                                @foreach ($data['paymentMethods'] as $paymentMethod)
                                                    <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] . ' (Bal: ' . $paymentMethod['balance'] }} &#2547;)</option>
                                                @endforeach
                                                {{-- @dd($data['item']['account_id']) --}}
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-2 col-md-2 col-lg-2">
                                            <label>Reference Number</label>
                                            <input value="{{ $data['item']->reference_number ?? null }}" type="text" class="form-control" name="reference_number" id="reference_number" placeholder="XXXXXXXXXX">
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Note</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->note : null }}" type="text" class="form-control" name="note" placeholder="Note">
                                        </div>
                                        <div class="form-group col-sm-2 col-md-2 col-lg-2">
                                            <label>Grand Total</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->total_amount : null }}" type="number" class="form-control" name="total_amount" id="total_amount" placeholder="0.00" readonly>
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
        $('#category_id').on('change', function (e) {
            let category_id = $(this).val();
            const url = `{{ route('expenses.expense-heads', [":category_id"]) }}`.replace(':category_id', category_id);
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'JSON',
                success: function(res){
                    let options = `<option value="" selected disabled>Select Head</option>`;
                    res.forEach(function(exp_head) {
                        options += `<option expense-head="${exp_head.title}" value="${exp_head.id}">${exp_head.title}</option>`;
                    });
                    $('#expense_head_id_temp').html(options);
                }
            });

        });

        $('#expanse-table').bind('keyup, input', function (e) {
            if($(e.target).is('.calculate')){
                calculate();
            }
        });
        $('#tbody').bind('click', function (e) {
            $(e.target).is('.btn-del') && e.target.closest('tr').remove();
            $( ".serial" ).each(function(index){$(this).html(index+1);});
            calculate();
        });
        $('#expense_head_id_temp').on('change', function (e) {
            let isDuplicate = false;
            let expense_head_id = $('#expense_head_id_temp option:selected').val();
            $('#tbody tr').each(function() {
                let existingItemId = $(this).find('input[name="expense_head_id[]"]').val();
                if (existingItemId == expense_head_id) {
                    isDuplicate = true;
                    return false;
                }
            });
            if (isDuplicate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Service',
                    text: 'This Expesne Head has already been added!'
                });
                return;
            }
            $('#tbody').append(`
                    <tr>
                        <td class="serial"></td>
                        <td><input type="hidden" value="${$('#expense_head_id_temp option:selected').val()}" name="expense_head_id[]">${$('#expense_head_id_temp option:selected').attr('expense-head')}</td>
                        <td><input type="number" value="${1}" class="form-control form-control-sm calculate" step="1" min="1" name="quantity[]" placeholder="0.00"></td>
                        <td><input type="number" value="" class="form-control form-control-sm calculate" step="1" min="1" name="amount[]" placeholder="0.00" required></td>
                        <td><input type="number" value="" class="form-control form-control-sm" name="total[]" placeholder="0.00" disabled></td>
                        <td><input type="text" value="" class="form-control form-control-sm" name="note_[]" placeholder="Remarks"></td>
                        <td><button class="btn btn-sm btn-danger btn-del" type="button"><i class="fa-solid fa-trash btn-del"></i></button></td>
                    </tr>
            `);
            calculate();
        });
    });

    $('#form-submit').submit(function(e){
        if(!$('input[name="expense_head_id[]"]').length){
            e.preventDefault();
            alert('Please Insert Expense!');
        }
    });
    function calculate(){
        $('#total_temp').val($('#quantity_temp').val() * $('#amount_temp').val());
        let expense_head_id = $('input[name="expense_head_id[]"]');
        let total_amount = 0;
        for (let i = 0; i < expense_head_id.length; i++){
            $('input[name="total[]"]')[i].value = ($('input[name="amount[]"]')[i].value * $('input[name="quantity[]"]')[i].value);
            total_amount += $('input[name="amount[]"]')[i].value * $('input[name="quantity[]"]')[i].value;
        }
        $('#total_amount').val(total_amount);
        $( ".serial" ).each(function(index){$(this).html(index+1);});
    }
</script>
@endsection