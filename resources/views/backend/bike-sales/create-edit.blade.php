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
                            <form action="{{ isset($data['item']) ? route('bike-sales.update', $data['item']->id) : route('bike-sales.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Buyer Info -->
                                        <div class="col-12">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Buyer Info</h5>
                                            <input value="{{ isset($data['item']) ? $data['buyer']->id : null }}" type="hidden" name="buyer_id" id="buyer_id">
                                            <div class="row">
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Buyer Name *</label>
                                                    <input value="{{ isset($data['item']) ? $data['buyer']->name : old('buyer_name') }}" type="text" class="form-control" name="buyer_name" id="buyer_name" placeholder="Buyer Name" required>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Contact</label>
                                                    <input value="{{ isset($data['item']) ? $data['buyer']->contact : old('contact') }}" type="text" class="form-control" name="contact" id="contact" placeholder="+8801XXXXXXXXX" required>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Date Of Birth</label>
                                                    <input value="{{ isset($data['item']) ? $data['buyer']->dob : old('dob') }}" type="date" class="form-control" name="dob" id="dob" required>
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>NID</label>
                                                    <input value="{{ isset($data['item']) ? $data['buyer']->nid : old('nid') }}" type="text" class="form-control" name="nid" id="nid" placeholder="NID">
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>DL No</label>
                                                    <input value="{{ isset($data['item']) ? $data['buyer']->dl_no : old('dl_no') }}" type="text" class="form-control" name="dl_no" id="dl_no" placeholder="DL No">
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Passport No</label>
                                                    <input value="{{ isset($data['item']) ? $data['buyer']->passport_no : old('passport_no') }}" type="text" class="form-control" name="passport_no" id="passport_no" placeholder="Passport No">
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>BCN No</label>
                                                    <input value="{{ isset($data['item']) ? $data['buyer']->bcn_no : old('bcn_no') }}" type="text" class="form-control" name="bcn_no" id="bcn_no" placeholder="BCN No">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Bike Info -->
                                        <div class="col-12 mt-4">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Bike Info</h5>
                                            <div class="row">
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Bike *</label>
                                                    <select name="bike_purchase_id" id="bike_purchase_id" class="form-control select2" required>
                                                        <option value="" selected disabled>Select Bikes</option>
                                                        @foreach ($data['bikes'] as $bike)
                                                            <option value="{{ $bike['bike_purchase_id'] }}"
                                                                data-model="{{ $bike['model_name'] }}"
                                                                data-color="{{ $bike['color_name'] }}"
                                                                data-chassis-no="{{ $bike['chassis_no'] }}"
                                                                data-engine-no="{{ $bike['engine_no'] }}"
                                                                data-registration-no="{{ $bike['registration_no'] }}"
                                                                data-total-cost="{{ $bike['total_cost'] }}"
                                                                @isset($data['item'])
                                                                    @selected($bike['bike_purchase_id'] == $data['item']->bike_purchase_id)
                                                                @endisset
                                                            >
                                                                {{ $bike['model_name'] }} {{ $bike['color_name'] }} <br> Ch#{{ $bike['chassis_no'] }} <br> Reg#{{ $bike['registration_no'] }}
                                                            </option>
                                                        @endforeach 
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Models</label>
                                                    <input value="{{ isset($data['item']) ? $data['bike_info']['model_name'] : null }}" type="text" class="form-control" id="model_id" placeholder="Model" readonly>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Colors</label>
                                                    <input value="{{ isset($data['item']) ? $data['bike_info']['color_name'] : null }}" type="text" class="form-control" id="color_id" placeholder="Color" readonly>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Chassis No</label>
                                                    <input value="{{ isset($data['item']) ? $data['bike_info']['chassis_no'] : null }}" type="text" class="form-control" id="chassis_no" placeholder="Chassis No" readonly>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Engine No</label>
                                                    <input value="{{ isset($data['item']) ? $data['bike_info']['engine_no'] : null }}" type="text" class="form-control" id="engine_no" placeholder="Engine No" readonly>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Registration No</label>
                                                    <input value="{{ isset($data['item']) ? $data['bike_info']['registration_no'] : null }}" type="text" class="form-control" id="registration_no" placeholder="Registration No" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Sales Info -->
                                        <div class="col-12 mt-4">
                                            <h5 class="mb-3 border-bottom pb-2 d-flex justify-content-center">Sales Info</h5>
                                            <div class="row">
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Sale Date *</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->sale_date : old('sale_date') ?? date('Y-m-d') }}" required type="date" class="form-control" name="sale_date" id="sale_date" required>
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Payment Methods *</label>
                                                    <select class="form-control" name="account_id" id="account_id" required>
                                                        <option disabled selected value=''>Select Payment Methods</option>
                                                        @foreach ($data['paymentMethods'] as $paymentMethod)
                                                            <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Total Bike Cost *</label>
                                                    <input value="{{ isset($data['item']) ? $data['bike_info']['total_cost'] : null }}" type="text" class="form-control" id="total_cost" placeholder="0.00" readonly>
                                                </div>
                                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                                    <label>Sales Price *</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->sale_price : old('sale_price') }}" min='0' type="number" class="form-control" name="sale_price" id="sale_price" placeholder="0.00" required>
                                                </div>
                                                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                                                    <label>Reference Number</label>
                                                    <input value="{{ $data['item']->reference_number ?? null }}" type="text" class="form-control" name="reference_number" id="reference_number" placeholder="XXXXXXXXXX">
                                                </div>
                                                <div class="form-group col-sm-8 col-md-8 col-lg-8">
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
                                                            src="{{ asset('public/uploads/bike-sales/' . (isset($data['item']) && $data['item']->doc_nid !=null ? $data['item']->doc_nid : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_nid" name="doc_nid" style="display:none" type="file" accept="image/*">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Registration Card
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_reg_card_view"
                                                            src="{{ asset('public/uploads/bike-sales/' . (isset($data['item']) && $data['item']->doc_reg_card !=null ? $data['item']->doc_reg_card : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_reg_card" name="doc_reg_card" style="display:none" type="file" accept="image/*">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Photo
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_image_view"
                                                            src="{{ asset('public/uploads/bike-sales/' . (isset($data['item']) && $data['item']->doc_image !=null ? $data['item']->doc_image : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_image" name="doc_image" style="display:none" type="file">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Deed
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_deed_view"
                                                            src="{{ asset('public/uploads/bike-sales/' . (isset($data['item']) && $data['item']->doc_deed !=null ? $data['item']->doc_deed : 'placeholder.png')) }}">
                                                        <input class="preview-image" id="doc_deed" name="doc_deed" style="display:none" type="file" accept="image/*">
                                                    </label>
                                                </div>
                                                <div class="form-group col-sm-2 col-md-2 col-lg-2 text-center">
                                                    <label style="cursor:pointer;">
                                                        Tax Token
                                                        <img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" id="doc_tax_token_view"
                                                            src="{{ asset('public/uploads/bike-sales/' . (isset($data['item']) && $data['item']->doc_tax_token !=null ? $data['item']->doc_tax_token : 'placeholder.png')) }}">
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

    $(document).ready(function () {

        $('#bike_purchase_id').on('change',function(){
            let bike_info = $('#bike_purchase_id option:selected');
            $('#model_id').val(bike_info.data('model'));
            $('#color_id').val(bike_info.data('color'));
            $('#chassis_no').val(bike_info.data('chassis-no'));
            $('#engine_no').val(bike_info.data('engine-no'));
            $('#registration_no').val(bike_info.data('registration-no'));
            $('#total_cost').val(bike_info.data('total-cost'));
        });
        $("#dl_no, #nid, #passport_no, #bcn_no").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "{{ route('bike-sales.buyer-search') }}",
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
                $("#buyer_id").val(ui.item.id);
                $("#buyer_name").val(ui.item.name);
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
                    $("#buyer_id").val('');
                    event.currentTarget.focus();
                }
            }
    });
    
    });
</script>
@endsection