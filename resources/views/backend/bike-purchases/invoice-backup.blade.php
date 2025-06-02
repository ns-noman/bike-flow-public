@extends('layouts.admin.master')
@section('content')
  <div class="content-wrapper">
      @include('layouts.admin.content-header')
      <section class="content">
          <div class="container-fluid">
              <div class="row">
                  <div class="col-12">
                      <div class="invoice p-3 mb-3" id="my-invoice">
                          <div class="row">
                              <div class="col-12">
                                  <h4>
                                    <img style="height: 50px;width: px;" src="{{ asset('public/uploads/basic-info/' . $data['basicInfo']['logo']) }}" alt="Logo" />
                                      {{ $data['basicInfo']['title'] }}
                                      <small class="float-right">Date: {{ date('dS M Y', strtotime($data['master']['purchase_date'])) }}</small>
                                  </h4>
                              </div>
                          </div>
                          <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    From
                                    <address>
                                        <strong>{{ $data['master']['seller_name'] }}</strong><br>
                                        Phone: {{ $data['master']['seller_contact'] }}<br>
                                        NID: {{ $data['master']['seller_nid'] }} <br>
                                        DL No: {{ $data['master']['seller_dl_no'] }} <br>
                                        Passport No: {{ $data['master']['seller_passport_no'] }} <br>
                                        BCN No: {{ $data['master']['seller_bcn_no'] }} <br>
                                    </address>
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    To
                                    <address>
                                        <strong>{{ $data['basicInfo']['title'] }}</strong><br>
                                        {{ $data['basicInfo']['address'] }}<br>
                                        Phone-1: {{ $data['basicInfo']['phone'] }}<br>
                                        Phone-2: {{ $data['basicInfo']['telephone'] }}<br>
                                        Email: {{ $data['basicInfo']['email'] }}
                                    </address>
                                </div>
                              <div class="col-sm-4 invoice-col">
                                  <b>Invoice #{{ $data['master']['invoice_no'] }}</b><br>
                                  <br>
                                  <p><span><svg class="barcode"></svg></span></p>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-12 table-responsive">
                                  <table class="table table-striped">
                                      <thead>
                                          <tr>
                                              <th>Bike Model</th>
                                              <th>Registration No.</th>
                                              <th>Chasis No.</th>
                                              <th>Engine No.</th>
                                              <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $data['master']['model_name'] }} <span class="badge" style="background-color: {{ $data['master']['hex_code'] }};color: black; text-shadow: 2px 0px 8px white;">{{ $data['master']['color_name'] }}</span></td>
                                                <td>{{ $data['master']['registration_no'] }}</td>
                                                <td>{{ $data['master']['chassis_no'] }}</td>
                                                <td>{{ $data['master']['engine_no'] }}</td>
                                                <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['purchase_price'], 2) }}</td>
                                            </tr>
                                        </tbody>
                                  </table>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-6">
                                  <p class="lead">Payment Methods: {{ $data['master']['payment_method'] }}</p>
                              </div>
                              <div class="col-6">
                                  {{-- <p class="lead">Amount Due 2/22/2014</p> --}}

                                  {{-- <div class="table-responsive">
                                      <table class="table">
                                          <tr>
                                              <th style="width:50%">Subtotal:</th>
                                              <td>$250.30</td>
                                          </tr>
                                          <tr>
                                              <th>Tax (9.3%)</th>
                                              <td>$10.34</td>
                                          </tr>
                                          <tr>
                                              <th>Shipping:</th>
                                              <td>$5.80</td>
                                          </tr>
                                          <tr>
                                              <th>Total:</th>
                                              <td>$265.24</td>
                                          </tr>
                                      </table>
                                  </div> --}}
                              </div>
                          </div>
                          <div class="row no-print">
                              <div class="col-12">
                                  <a href="javascript:void(0)" onclick="customPrint()" rel="noopener" class="btn btn-default">
                                    <i class="fas fa-print"></i> Print</a>
                              </div>
                          </div>
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
            if ("{{ $data['print'] }}" == 'print') {
              customPrint();
            }
            $('.pay-now').on('click', function(e) {
                $('#purchase_id').val($(this).attr('purchase-id'));
                $('#amount').val(parseFloat($(this).attr('due')).toFixed(2));
            });
        });
        JsBarcode(".barcode", "{{ $data['master']['invoice_no'] }}", {
            width: 1,
            height: 30,
            displayValue: false
        });
        function customPrint(){
          var printContents = document.getElementById('my-invoice').innerHTML;
          var originalContents = document.body.innerHTML;
          document.body.innerHTML = printContents;
          window.print();
          document.body.innerHTML = originalContents;
        }
    </script>
@endsection
