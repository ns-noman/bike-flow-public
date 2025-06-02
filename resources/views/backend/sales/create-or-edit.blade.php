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
                            <form id="form-submit" action="{{ isset($data['item']) ? route('sales.update',$data['item']->id) : route('sales.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif 
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Customer *</label>
                                            <select name="customer_id" id="customer_id" class="form-control select2">
                                                @foreach ($data['customers'] as $customer)
                                                    <option
                                                        {{ 
                                                            isset($data['item'])
                                                            ?
                                                            ($customer->id == $data['item']->customer_id ? 'selected' : null)
                                                            :
                                                            ($customer->customer_type == 1 ? 'selected' : null)                                                                
                                                        }} 
                                                        value="{{ $customer->id }}"
                                                        >
                                                        {{ $customer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Customer Name</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->name : null }}" type="text" class="form-control" name="name" placeholder="Customer Name">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Phone</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->phone : null }}" type="number" class="form-control" name="phone" placeholder="+8801XXXXXXXXX">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Address</label>
                                            <textarea class="form-control" name="address" placeholder="Address" cols="30" rows="1">{{ isset($data['item']) ? $data['item']->address : null }}</textarea>
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Bike Reg No</label>
                                            <input name="bike_reg_no" id="bike_reg_no" type="text" value="{{ isset($data['item']) ? $data['item']->bike_reg_no : null }}" class="form-control" placeholder="Bike Reg No">
                                        </div>
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Date *</label>
                                            <input name="date" id="date" type="date"
                                                value="{{ isset($data['item']) ? $data['item']->date : date('Y-m-d') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                            <label>Item</label>
                                            <select class="form-control select2" id="item_id_temp">
                                                <option value="" selected disabled>Select Item</option>
                                                @foreach ($data['items'] as $key => $item)
                                                    <option value="{{ $item['id'] }}"
                                                        item_name="{{ $item['name'] }}"
                                                        item_purchase_price="{{ $item['purchase_price'] }}"
                                                        item_price="{{ $item['price'] }}"
                                                        unit_name="{{ $item['unit_name'] }}"
                                                        stock_qty="{{ $item['stock_quantity'] }}"
                                                        item_type="{{ $item['item_type'] }}"
                                                        > {{ $item['name'] }} @if($item['item_type'] == 'item') (stk: {{ $item['stock_quantity'] }}) @endif
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
                                                            <th width="30%">Item</th>
                                                            <th width="10%">Unit</th>
                                                            <th width="10%">StockQTY</th>
                                                            <th width="10%">Quantity</th>
                                                            <th width="10%">TP</th>
                                                            <th width="10%">Sales Price</th>
                                                            <th width="10%">Sub Total</th>
                                                            <th width="5%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">
                                                        @isset($data['saleDetails'])
                                                            @foreach ($data['saleDetails'] as $sd)
                                                                <tr>
                                                                    <td class="serial">{{ $loop->iteration }}</td>
                                                                    <td class="text-left">
                                                                        @if ($sd['item_type']==0)
                                                                            {{ $sd['item_name'] }}
                                                                        @else
                                                                            {{ $sd['service_name'] }}
                                                                        @endif
                                                                        <input type="hidden" value="{{ $sd['item_id'] ? $sd['item_id'] : $sd['service_id'] }}" name="item_id[]">
                                                                        <input type="hidden" value="{{ $sd['item_type'] }}" name="item_type[]">    
                                                                    </td>
                                                                    <td>{{ $sd['unit_name'] ?? "Service" }}</td>
                                                                    <td><input type="number" value="{{ $sd['current_stock'] }}" class="form-control form-control-sm" name="stock_quantity[]" placeholder="0.00" readonly></td>
                                                                    <td><input type="number" value="{{ $sd['quantity'] }}" class="form-control form-control-sm calculate" name="quantity[]" placeholder="0.00" required></td>
                                                                    <td><input type="number" value="{{ $sd['purchase_price'] }}" class="form-control form-control-sm" placeholder="0.00" readonly></td>
                                                                    <td><input type="number" value="{{ $sd['unit_price'] }}" class="form-control form-control-sm calculate" name="unit_price[]" placeholder="0.00" required></td>
                                                                    <td><input type="number" value="{{ $sd['unit_price'] * $sd['quantity'] }}" class="form-control form-control-sm" name="sub_total[]" placeholder="0.00" disabled></td>
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
                let item_name = $('#item_id_temp option:selected').attr('item_name');
                let unit_name = $('#item_id_temp option:selected').attr('unit_name');
                let item_purchase_price = $('#item_id_temp option:selected').attr('item_purchase_price');
                let item_price = $('#item_id_temp option:selected').attr('item_price');
                let stock_qty = $('#item_id_temp option:selected').attr('stock_qty');
                let item_type = $('#item_id_temp option:selected').attr('item_type') == 'item' ? 0 : 1;
                if (checkDuplicate(item_id, item_type)) return duplicateAlert();

                let unit_price_temp = $('#item_id_temp option:selected').attr('item_price');
                let quantity_temp = 1;
                let total_temp = unit_price_temp * quantity_temp;
                let tbody =  ``;
                if (isLowStock(item_type, stock_qty, quantity_temp)) return stockAlert();

                tbody += `<tr>
                            <td class="serial"></td>
                            <td class="text-left">
                                ${item_name}
                                <input type="hidden" value="${item_id}" name="item_id[]">
                                <input type="hidden" value="${item_type}" name="item_type[]">    
                            </td>
                            <td>${unit_name}</td>
                            <td><input type="number" value="${stock_qty}" class="form-control form-control-sm" name="stock_quantity[]" placeholder="0.00" readonly></td>
                            <td><input type="number" value="${quantity_temp}" class="form-control form-control-sm calculate" name="quantity[]" placeholder="0.00" required></td>
                            <td><input type="number" value="${item_purchase_price}" class="form-control form-control-sm" placeholder="0.00" readonly></td>
                            <td><input type="number" value="${unit_price_temp}" class="form-control form-control-sm calculate" name="unit_price[]" placeholder="0.00" required></td>
                            <td><input type="number" value="${total_temp}" class="form-control form-control-sm" name="sub_total[]" placeholder="0.00" disabled></td>
                            <td><button class="btn btn-sm btn-danger btn-del" type="button"><i class="fa-solid fa-trash btn-del"></i></button></td>
                        </tr>`;

                $('#tbody').append(tbody);
                $(".serial").each(function(index) { $(this).html(index + 1);});
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
                let item_type = $('input[name="item_type[]"]')[i].value;
                let stock_quantity = $('input[name="stock_quantity[]"]')[i].value;
                let quantity = $('input[name="quantity[]"]')[i].value;
                let unit_price = $('input[name="unit_price[]"]')[i].value;
                let sub_total = $('input[name="sub_total[]"]')[i].value;
                if (isLowStock(parseInt(item_type),parseFloat(stock_quantity), parseFloat(quantity))){
                    stockAlert();
                    quantity = stock_quantity;
                    $('input[name="quantity[]"]')[i].value = stock_quantity;
                }
                sub_total = unit_price * quantity;
                total_price += sub_total;
                $('input[name="sub_total[]"]')[i].value = sub_total;
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
            $('#discount').val(discount.toFixed(2));
            $('#total_payable').val(total_payable.toFixed(2));
        }
        function checkDuplicate(item_id, item_type) {
            let isDuplicate = false;
            $('#tbody tr').each(function() {
                let existingItemId = $(this).find('input[name="item_id[]"]').val();
                let existingTypeId = $(this).find('input[name="item_type[]"]').val();
                if ((existingItemId == item_id) && (existingTypeId == item_type)) {
                    isDuplicate = true;
                    return false;
                }
            });
            return isDuplicate;
        }
        function duplicateAlert() {
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Item',
                text: 'This Item has already been added!'
            });
        }
        function isLowStock(itemType, stockQty, insertQty) {
            return itemType == '0' && insertQty > stockQty; 
        }
        function stockAlert() {
            Swal.fire({
                icon: 'error',
                title: 'Warning!',
                text: 'Exceed Product Stock!'
            });
        }

    </script>
@endsection
