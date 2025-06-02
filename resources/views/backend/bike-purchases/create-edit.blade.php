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
                            <form action="{{ isset($data['item']) ? route('bike-purchases.update', $data['item']->id) : route('bike-purchases.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Seller Info -->
                                        <div class="col-12">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Seller Info</h5>
                                            <input value="{{ isset($data['item']) ? $data['seller']['id'] : null }}" type="hidden" name="seller_id" id="seller_id">
                                            <input value="{{ isset($data['sale_id']) ? $data['seller']['id'] : null }}" type="hidden" name="buyer_id" id="buyer_id">
                                            <input value="{{ isset($data['sale_id']) ? $data['sale_id'] : null }}" type="hidden" name="sale_id" id="sale_id">
                                            <div class="row">
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Seller Name *</label>
                                                    <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['seller']['name'] : old('seller_name') }}" type="text" class="form-control" name="seller_name" id="seller_name" placeholder="Seller Name" required>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Contact</label>
                                                    <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['seller']['contact'] : old('contact') }}" type="text" class="form-control" name="contact" id="contact" placeholder="+8801XXXXXXXXX" required>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Date Of Birth</label>
                                                    <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['seller']['dob'] : old('dob') }}" type="date" class="form-control" name="dob" id="dob" required>
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>NID</label>
                                                    <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['seller']['nid'] : old('nid') }}" type="text" class="form-control" name="nid" id="nid" placeholder="NID">
                                                </div>

                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>DL No</label>
                                                    <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['seller']['dl_no'] : old('dl_no') }}" type="text" class="form-control" name="dl_no" id="dl_no" placeholder="DL No">
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Passport No</label>
                                                    <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['seller']['passport_no'] : old('passport_no') }}" type="text" class="form-control" name="passport_no" id="passport_no" placeholder="Passport No">
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>BCN No</label>
                                                    <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['seller']['bcn_no'] : old('bcn_no') }}" type="text" class="form-control" name="bcn_no" id="bcn_no" placeholder="BCN No">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Broker Info -->
                                        <div class="col-12">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Broker Info</h5>
                                            <input value="{{ isset($data['item']) && isset($data['broker']) ? $data['broker']->id : null }}" type="hidden" name="broker_id" id="broker_id">
                                            <div class="row">
                                                <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                                    <label>Broker Name</label>
                                                    <input value="{{ isset($data['item']) && isset($data['broker']) ? $data['broker']->name : old('broker_name') }}" type="text" class="form-control" name="broker_name" id="broker_name" placeholder="Broker Name">
                                                </div>
                                                <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                                    <label>Broker Contact</label>
                                                    <input value="{{ isset($data['item']) && isset($data['broker']) ? $data['broker']->contact : old('broker_contact') }}" type="number" class="form-control" name="broker_contact" id="broker_contact" placeholder="018XXXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Bike Info -->
                                        <div class="col-12 mt-4">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Bike Info</h5>
                                            <input value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['bike']['id'] : null }}" type="hidden" name="bike_id" id="bike_id">
                                            <div class="row">
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Brands *</label>
                                                    <select class="form-control  @error('brand_id') is-invalid @enderror" name="brand_id" id="brand_id" required @disabled(isset($data['sale_id']))>
                                                        <option disabled selected value=''>Select Brands</option>
                                                        @foreach ($data['brands'] as $brand)
                                                            <option @selected((isset($data['item']) || isset($data['sale_id'])) && $data['bike']['brand_id'] == $brand['id']) value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Models *</label>
                                                    <select class="form-control  @error('model_id') is-invalid @enderror" name="model_id" id="model_id" required @disabled(isset($data['sale_id']))>
                                                        <option disabled selected value=''>Select Models</option>
                                                        @if ((isset($data['item']) || isset($data['sale_id'])))
                                                            @foreach ($data['models'] as $model)
                                                                <option {{ $model['id'] == $data['bike']['model_id'] ? 'selected' : null }} value="{{ $model['id'] }}">{{ $model['name'] }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('model_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Colors *</label>
                                                    <select class="form-control  @error('color_id') is-invalid @enderror" name="color_id" id="color_id" required @disabled(isset($data['sale_id']))>
                                                        <option disabled selected value=''>Select Colors</option>
                                                        @foreach ($data['colors'] as $colors)
                                                            <option @selected((isset($data['item']) || isset($data['sale_id'])) && $data['bike']['color_id'] == $colors['id']) value="{{ $colors['id'] }}">{{ $colors['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('color_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Manufacture Year *</label>
                                                    <select class="form-control  @error('manufacture_year') is-invalid @enderror" name="manufacture_year" id="manufacture_year" required @disabled(isset($data['sale_id']))>
                                                        <option disabled selected value=''>Select Manufacture Year</option>
                                                        @for($i = 1970; $i <= date('Y'); $i++)
                                                            <option @selected((isset($data['item']) || isset($data['sale_id'])) && $data['bike']['manufacture_year'] == $i) value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    @error('manufacture_year')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Chassis No *</label>
                                                    <input @disabled(isset($data['sale_id'])) value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['bike']['chassis_no'] : old('chassis_no') }}" type="text" class="form-control  @error('chassis_no') is-invalid @enderror" name="chassis_no" id="chassis_no" placeholder="Chassis No" required autocomplete="off">
                                                    @error('chassis_no')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Engine No *</label>
                                                    <input @disabled(isset($data['sale_id'])) value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['bike']['engine_no'] : old('engine_no') }}" type="text" class="form-control  @error('engine_no') is-invalid @enderror" name="engine_no" id="engine_no" placeholder="Engine No" required autocomplete="off">
                                                    @error('engine_no')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Registration No</label>
                                                    <input @disabled(isset($data['sale_id'])) value="{{ (isset($data['item']) || isset($data['sale_id'])) ? $data['bike']['registration_no'] : old('registration_no') }}" type="text" class="form-control @error('registration_no') is-invalid @enderror" name="registration_no" id="registration_no" placeholder="Registration No" autocomplete="off">
                                                    @error('registration_no')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Purchase Info -->
                                        <div class="col-12 mt-4">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Purchase Info</h5>
                                            <select class="form-control" name="transaction_type" id="transaction_type" hidden>
                                                <option {{ isset($data['item']) && $data['item']->debit_amount ? 'selected' : null }} value='debit_amount'>Withdrawal</option>
                                            </select>
                                            <div class="row">
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Investors *</label>
                                                    <select class="form-control" name="investor_id" id="investor_id" required>
                                                        @foreach ($data['investors'] as $investors)
                                                            <option @selected(isset($data['item']) && $data['item']['investor_id'] == $investors['id']) value="{{ $investors['id'] }}">{{ $investors['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Purchase Date *</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->purchase_date :  (old('transaction_date') ? old('transaction_date') : date('Y-m-d'))  }}" required type="date" class="form-control" name="purchase_date" id="purchase_date" required>
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
                                                    <label>Purchase Price *</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->purchase_price : old('purchase_price') }}" min='0' type="number" class="form-control" name="purchase_price" id="purchase_price" placeholder="0.00" required>
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Reference Number</label>
                                                    <input value="{{ $data['item']->reference_number ?? null }}" type="text" class="form-control" name="reference_number" id="reference_number" placeholder="XXXXXXXXXX">
                                                </div>
                                                <div class="form-group col-sm-6 col-md-6 col-lg-6">
                                                    <label>Note</label>
                                                    <input value="{{ $data['item']->note ?? null }}" type="text" class="form-control" name="note" id="note" placeholder="Note">
                                                </div>
                                            </div>
                                        </div>
                                         <!-- Documents Upload -->
                                         <div class="col-12 mt-4">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Documents Upload</h5>
                                            <div class="row">
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        NID
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_nid_view"
                                                            src="{{ asset('public/uploads/bike-purchases/' . (isset($data['item']) && $data['item']->doc_nid !=null ? $data['item']->doc_nid : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_nid" name="doc_nid" style="display:none" type="file" accept="image/*">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Registration Card
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_reg_card_view"
                                                            src="{{ asset('public/uploads/bike-purchases/' . (isset($data['item']) && $data['item']->doc_reg_card !=null ? $data['item']->doc_reg_card : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_reg_card" name="doc_reg_card" style="display:none" type="file" accept="image/*">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Photo
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_image_view"
                                                            src="{{ asset('public/uploads/bike-purchases/' . (isset($data['item']) && $data['item']->doc_image !=null ? $data['item']->doc_image : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_image" name="doc_image" style="display:none" type="file">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Deed
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_deed_view"
                                                            src="{{ asset('public/uploads/bike-purchases/' . (isset($data['item']) && $data['item']->doc_deed !=null ? $data['item']->doc_deed : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_deed" name="doc_deed" style="display:none" type="file" accept="image/*">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Tax Token
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_tax_token"
                                                            src="{{ asset('public/uploads/bike-purchases/' . (isset($data['item']) && $data['item']->doc_tax_token !=null ? $data['item']->doc_tax_token : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_tax_token" name="doc_tax_token" style="display:none" type="file" accept="image/*">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Sales Info -->
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
    $(document).on('change', '.preview-image', function () {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(input).siblings('img').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
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
        $('#brand_id').on('change', function(e) {
            const brand_id = $(this).val();
            getModels(brand_id);
        });
    });


    function getModels(brand_id, selected_id=null) {
        const url = `{{ route('bike-purchases.models', [":brand_id"]) }}`.replace(':brand_id', brand_id);
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'JSON',
            success: function(res){
                let options = `<option disabled selected value="">Select Models</option>`;
                res.forEach(function(model) {
                    options += `<option ${model.id == selected_id ? 'selected' : null} value="${model.id}">${model.name}</option>`;
                });
                $('#model_id').html(options);
            }
        });
    }

</script>

<script>
    $(document).ready(function () {
        $("#dl_no, #nid, #passport_no, #bcn_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "{{ route('bike-purchases.seller-search') }}",
                    type: "GET",
                    dataType: "json",
                    data: { search: request.term },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {
                $("#seller_id").val(ui.item.id);
                $("#seller_name").val(ui.item.name);
                $("#contact").val(ui.item.contact);
                $("#dob").val(ui.item.dob);
                $("#nid").val(ui.item.nid);
                $("#dl_no").val(ui.item.dl_no);
                $("#passport_no").val(ui.item.passport_no);
                $("#bcn_no").val(ui.item.bcn_no);
                return false;
            },
            change: function (event, ui) {
                if (!ui.item) {
                    $("#seller_id").val('');
                    event.currentTarget.focus();
                }
            }
    });
        $("#registration_no, #engine_no, #chassis_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "{{ route('bike-purchases.bike-search') }}",
                    type: "GET",
                    dataType: "json",
                    data: { search: request.term },
                    success: function (data) {
                        if (data.length === 0) {
                            response([{ label: "No results found", value: "" }]);
                        } else {
                            response(data);
                        }
                    },
                    error: function () {
                        response([{ label: "Error fetching data", value: "" }]);
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {

                console.log(ui);
                

                $("#bike_id").val(ui.item.id);
                $("#chassis_no").val(ui.item.chassis_no);
                $("#engine_no").val(ui.item.engine_no);
                $("#registration_no").val(ui.item.registration_no);
                $("#color_id").val(ui.item.color_id);
                $("#brand_id").val(ui.item.brand_id);
                $("#manufacture_year").val(ui.item.manufacture_year);
                getModels(ui.item.brand_id, ui.item.model_id);
                return false;
            },
            change: function (event, ui) {
                if (!ui.item) {
                    $("#bike_id").val('');
                    event.currentTarget.focus();
                }
            }
        });
        $("#broker_name, #broker_contact").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "{{ route('bike-purchases.broker-search') }}",
                    type: "GET",
                    dataType: "json",
                    data: { search: request.term },
                    success: function (data) {
                        if (data.length === 0) {
                            response([{ label: "No results found", value: "" }]);
                        } else {
                            response(data);
                        }
                    },
                    error: function () {
                        response([{ label: "Error fetching data", value: "" }]);
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {
                $("#broker_id").val(ui.item.id);
                $("#broker_name").val(ui.item.name);
                $("#broker_contact").val(ui.item.contact);
                return false;
            },
            change: function (event, ui) {
                if (!ui.item) {
                    $("#broker_id").val('');
                    event.currentTarget.focus();
                }
            }
        });

    });
    
</script>
@endsection