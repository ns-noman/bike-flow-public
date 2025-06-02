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
                            <form id="form-submit" action="{{ isset($data['item']) ? route('bike-service-records.update',$data['item']->id) : route('bike-service-records.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-5 col-md-5 col-lg-5">
                                            <label>Bike *</label>
                                            <select name="bike_purchase_id" id="bike_purchase_id" class="form-control select2" required>
                                                <option value="" selected disabled>Select Bikes</option>
                                                @foreach ($data['bikes'] as $bike)
                                                    <option value="{{ $bike['bike_purchase_id'] }}"
                                                        @isset($data['item'])
                                                            @selected($bike['bike_purchase_id'] == $data['item']->bike_purchase_id)
                                                        @endisset
                                                    >
                                                        {{ $bike['model_name'] }} {{ $bike['color_name'] }} Ch#${{ $bike['chassis_no'] }} Reg#{{ $bike['registration_no'] }}
                                                    </option>
                                                @endforeach 
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-5 col-md-5 col-lg-5">
                                            <label>Services</label>
                                            <select class="form-control select2" id="service_id_temp">
                                                <option value="" selected disabled>Select Services</option>
                                                @foreach ($data['bike_services'] as $key => $service)
                                                    <option value="{{ $service->id }}" service_name="{{ $service->name }}"
                                                        price="{{ $service->price }}"
                                                        > {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-2 col-md-2 col-lg-2">
                                            <label>Date *</label>
                                            <input name="date" id="date" type="date"
                                                value="{{ isset($data['item']) ? $data['item']->date : date('Y-m-d') }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                            <div class="table-responsive">
                                                <table id="table"
                                                    class="table table-striped table-bordered table-centre p-0 m-0">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">SN</th>
                                                            <th>Service Name</th>
                                                            <th>Unit Price</th>
                                                            <th>Quantity</th>
                                                            <th>Sub Total</th>
                                                            <th width="5%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">
                                                        @isset($data['item'])
                                                            @foreach ($data['bike_service_record_details'] as $value)
                                                                <tr>
                                                                    <td class="serial">{{ $loop->iteration }}</td>
                                                                    <td class="text-left"><input type="hidden" value="{{ $value['service_id'] }}" name="service_id[]">{{ $value['service_name'] }}</td>
                                                                    <td><input type="number" value="{{ $value['price'] }}" class="form-control form-control-sm calculate" name="price[]" placeholder="0.00" required></td>
                                                                    <td><input type="number" value="{{ $value['quantity'] }}" class="form-control form-control-sm calculate" name="quantity[]" placeholder="0.00" required></td>
                                                                    <td><input type="number" value="{{ $value['price'] * $value['quantity'] }}" class="form-control form-control-sm" name="sub_total[]" placeholder="0.00" disabled></td>
                                                                    <td><button class="btn btn-sm btn-danger btn-del" type="button"><i class="fa-solid fa-trash btn-del"></i></button></td>
                                                                </tr>
                                                            @endforeach
                                                        @endisset
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-5 col-md-5 col-lg-5">
                                            <label>Note</label>
                                            <input value="{{ isset($data['item']) ? $data['item']->note : null }}"
                                                class="form-control" type="text" name="note" id="note"
                                                placeholder="Note">
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
                                        <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                            <label>Total Amount</label>
                                            <input
                                                value="{{ isset($data['item']) ? $data['item']->total_amount : null }}"
                                                type="number" class="form-control" name="total_amount" id="total_amount"
                                                placeholder="0.00" readonly>
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
            $('#service_id_temp').on('change', function(e) {
                let service_id = $('#service_id_temp').val();
                let service_name = $('#service_id_temp option:selected').attr('service_name');
                let item_price = $('#service_id_temp option:selected').attr('price');

                if (checkDuplicate()) return duplicateAlert();
              

                let unit_price_temp = $('#service_id_temp option:selected').attr('price');
                let quantity_temp = 1;
                let total_temp = unit_price_temp * quantity_temp;
                let tbody = ``;

                tbody += `<tr>
                            <td class="serial"></td>
                            <td class="text-left"><input type="hidden" value="${service_id}" name="service_id[]">${service_name}</td>
                            <td><input type="number" value="${unit_price_temp}" class="form-control form-control-sm calculate" name="price[]" placeholder="0.00" required></td>
                            <td><input type="number" value="${quantity_temp}" class="form-control form-control-sm calculate" name="quantity[]" placeholder="0.00" required></td>
                            <td><input type="number" value="${total_temp}" class="form-control form-control-sm" name="sub_total[]" placeholder="0.00" disabled></td>
                            <td><button class="btn btn-sm btn-danger btn-del" type="button"><i class="fa-solid fa-trash btn-del"></i></button></td>
                        </tr>`;

                $('#tbody').append(tbody);
                $(".serial").each(function(index) { $(this).html(index + 1);});
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
            if (!$('input[name="service_id[]"]').length) {
                e.preventDefault();
                Swal.fire("Please Insert Item!");
            }
        });

        function calculate() {
            let service_id = $('input[name="service_id[]"]');
            let total_amount = 0;
            for (let i = 0; i < service_id.length; i++) {
                $('input[name="sub_total[]"]')[i].value = ($('input[name="price[]"]')[i].value * $('input[name="quantity[]"]')[i].value);
                total_amount += $('input[name="price[]"]')[i].value * $('input[name="quantity[]"]')[i].value;
            }
            $('#total_amount').val(total_amount.toFixed(2));
        }
        function checkDuplicate() {
            let service_id = $('#service_id_temp').val();
            let isDuplicate = false;
            $('#tbody tr').each(function() {
                let existingItemId = $(this).find('input[name="service_id[]"]').val();
                if (existingItemId == service_id) {
                    isDuplicate = true;
                    return false;
                }
            });
            return isDuplicate;
        }
        function duplicateAlert() {
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Service',
                text: 'This service has already been added!'
            });
        }



    </script>
@endsection
