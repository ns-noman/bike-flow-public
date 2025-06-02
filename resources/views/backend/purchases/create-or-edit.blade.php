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
                            <form id="form-submit" action="{{ isset($data['item']) ? route('purchases.update',$data['item']->id) : route('purchases.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif 
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Supplier *</label>
                                            <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                                <option value="" selected disabled>Select Supplier</option>
                                                @foreach ($data['suppliers'] as $supplier)
                                                    <option
                                                        @isset($data['item']) @selected($supplier->id == $data['item']->supplier_id) @endisset
                                                        value="{{ $supplier->id }}">
                                                        {{ $supplier->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Date *</label>
                                            <input name="date" id="date" type="date"
                                                value="{{ isset($data['item']) ? $data['item']->date : date('Y-m-d') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Item</label>
                                            <select class="form-control select2" id="item_id_temp">
                                                <option value="" selected>Select Item</option>
                                                @foreach ($data['items'] as $key => $item)
                                                    <option value="{{ $item->id }}" item-name="{{ $item->name }}"
                                                        item-price="{{ $item->purchase_price }}"
                                                        unit_name="{{ $item->unit->title }}"
                                                        > {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                            <div class="table-responsive">
                                                <table id="table"
                                                    class="table table-striped table-bordered table-centre p-0 m-0">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">SN</th>
                                                            <th>Item</th>
                                                            <th>Unit</th>
                                                            <th>Unit Price</th>
                                                            <th>Quantity</th>
                                                            <th>Sub Total</th>
                                                            <th width="5%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">
                                                        @isset($data['purchaseDetails'])
                                                            @foreach ($data['purchaseDetails'] as $pd)
                                                                <tr>
                                                                    <td class="serial">{{ $loop->iteration }}</td>
                                                                    <td class="text-left"><input type="hidden" value="{{ $pd['item_id'] }}" name="item_id[]">{{ $pd['item_name'] }}</td>
                                                                    <td>{{ $pd['unit_name'] }}</td>
                                                                    <td><input type="number" value="{{ $pd['unit_price'] }}" class="form-control form-control-sm calculate" name="unit_price[]" placeholder="0.00" required></td>
                                                                    <td><input type="number" value="{{ $pd['quantity'] }}" class="form-control form-control-sm calculate" name="quantity[]" placeholder="0.00" required></td>
                                                                    <td><input type="number" value="{{ $pd['unit_price'] * $pd['quantity'] }}" class="form-control form-control-sm" name="sub_total[]" placeholder="0.00" disabled></td>
                                                                    <td><button class="btn btn-sm btn-danger btn-del" type="button"><i class="fa-solid fa-trash btn-del"></i></button></td>
                                                                </tr>
                                                            @endforeach
                                                        @endisset
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Discount Method</label>
                                            <select name="discount_method" id="discount_method" class="form-control">
                                                <option @selected(isset($data['item']) && $data['item']['discount_method'] == 0) selected value="0" >In Percentage</option>
                                                <option @selected(isset($data['item']) && $data['item']['discount_method'] == 1) value="1">Solid Amount</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Discount Rate</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->discount_rate : null }}" step="0.01" type="number" class="form-control" name="discount_rate" id="discount_rate" placeholder="0.00">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Discount Amount</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->discount : null }}" readonly type="number" class="form-control" name="discount" id="discount" placeholder="0.00">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Total</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->total_price : null }}" type="number" class="form-control" name="total_price" id="total_price" placeholder="0.00" readonly>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Vat</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->vat_tax : null }}" readonly value="0.00" type="number" class="form-control" name="vat_tax" id="vat_tax" placeholder="0.00">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Total Payable</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->total_payable : null }}" readonly type="number" class="form-control" name="total_payable" id="total_payable" placeholder="0.00">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Payment Methods *</label>
                                            <select class="form-control" name="account_id" id="account_id">
                                                <option selected value=''>Select Payment Methods</option>
                                                @foreach ($data['paymentMethods'] as $paymentMethod)
                                                    <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] . ' (Bal: ' . $paymentMethod['balance'] }} &#2547;)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Paid Amount</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->paid_amount : null }}" value="0.00" step="0.01" type="number" 
                                                class="form-control" name="paid_amount"
                                                id="paid_amount" placeholder="0.00">
                                        </div>
                                        <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                            <label>Refference Number</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->reference_number : null }}"
                                                class="form-control" type="text" name="reference_number" id="reference_number"
                                                placeholder="Reference Number">
                                        </div>
                                        <div class="form-group col-sm-8 col-md-8 col-lg-8">
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
        $(document).ready(function() {
            $('#item_id_temp').on('change', function(e) {
                let item_id = $('#item_id_temp').val();
                let item_name = $('#item_id_temp option:selected').attr('item-name');
                let unit_name = $('#item_id_temp option:selected').attr('unit_name');
                let item_price = $('#item_id_temp option:selected').attr('item-price');

                let unit_price_temp = $('#item_id_temp option:selected').attr('item-price');
                let quantity_temp = 1;
                let total_temp = unit_price_temp * quantity_temp;
                let tbody =  ``;

                tbody += `<tr>
                            <td class="serial"></td>
                            <td class="text-left"><input type="hidden" value="${item_id}" name="item_id[]">${item_name}</td>
                            <td>${unit_name}</td>
                            <td><input type="number" value="${unit_price_temp}" class="form-control form-control-sm calculate" name="unit_price[]" placeholder="0.00" required></td>
                            <td><input type="number" value="${quantity_temp}" class="form-control form-control-sm calculate" name="quantity[]" placeholder="0.00" required></td>
                            <td><input type="number" value="${total_temp}" class="form-control form-control-sm" name="sub_total[]" placeholder="0.00" disabled></td>
                            <td><button class="btn btn-sm btn-danger btn-del" type="button"><i class="fa-solid fa-trash btn-del"></i></button></td>
                        </tr>`;

                $('#tbody').append(tbody);
                $(".serial").each(function(index){$(this).html(index + 1);});
                $('#item_id_temp').val('');
                calculate();
            });

            $('#table').bind('keyup, input', function(e) {
                if ($(e.target).is('.calculate')) {
                    calculate();
                }
            });
            $('#tbody').bind('click', function(e) {
                $(e.target).is('.btn-del') && e.target.closest('tr').remove();
                $(".serial").each(function(index) {
                    $(this).html(index + 1);
                });
                calculate();
            });
        });
        $('#form-submit').submit(function(e) {
            if (!$('input[name="item_id[]"]').length) {
                e.preventDefault();
                Swal.fire("Please Insert Item!");
            }
            if(parseFloat($('#paid_amount').val())>parseFloat($('#total_payable').val())){
                e.preventDefault();
                Swal.fire("Couldn't be pay more then payable!");
            }
            if(parseFloat($('#paid_amount').val())>0 && !$('#account_id option:selected').val()){
                e.preventDefault();
                Swal.fire("Please Select Payment Method");
            }
        });
        $('#discount_rate').on('keyup', function(e) {
            calculate();
        });
        $('#discount_method').on('change', function(e) {
            calculate();
        });
        function calculate() {
            let item_id = $('input[name="item_id[]"]');
            let total_price = 0;
            for (let i = 0; i < item_id.length; i++) {
                $('input[name="sub_total[]"]')[i].value = ($('input[name="unit_price[]"]')[i].value * $(
                    'input[name="quantity[]"]')[i].value);
                total_price += $('input[name="unit_price[]"]')[i].value * $('input[name="quantity[]"]')[i].value;
            }
            $('#total_price').val(total_price.toFixed(2));
            let discount_method = $('#discount_method').val();
            let discount_rate = parseFloat($('#discount_rate').val()) || 0;
            let vat_tax = parseFloat($('#vat_tax').val()) || 0;
            let discount = 0;
            let total_payable = 0;
            if (discount_method == 0){
                discount = total_price * (discount_rate / 100);
            }else{
                discount = discount_rate;
            }
            total_payable = total_price + vat_tax - discount;
            $('#paid_amount').val(total_payable.toFixed(2));
            $('#discount').val(discount);
            $('#total_payable').val(total_payable.toFixed(2));


            // let item_id = $('input[name="item_id[]"]');
            // let total_price = 0;
            // for (let i = 0; i < item_id.length; i++) {
            //     $('input[name="sub_total[]"]')[i].value = ($('input[name="unit_price[]"]')[i].value * $(
            //         'input[name="quantity[]"]')[i].value);
            //     total_price += $('input[name="unit_price[]"]')[i].value * $('input[name="quantity[]"]')[i].value;
            // }


            // $('#total_price').val(total_price.toFixed(2));
            // let discount_method = $('#discount_method').val();
            // let discount_rate = parseFloat($('#discount_rate').val()) || 0;
            // let vat_tax = parseFloat($('#vat_tax').val()) || 0;
            // let discount = 0;
            // let total_payable = 0;
            // if (discount_method == 0){
            //     discount = total_price * (discount_rate / 100);
            // }else{
            //     discount = discount_rate;
            // }
            // total_payable = total_price + vat_tax - discount;
            
            // $('#paid_amount').val(total_payable.toFixed(2));
            // $('#discount').val(discount.toFixed(2));
            // $('#total_payable').val(total_payable.toFixed(2));
        }

    </script>
@endsection
