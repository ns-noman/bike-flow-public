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
                                      <small class="float-right">Date: {{ date('dS M Y', strtotime($data['master']['sale_date'])) }}</small>
                                  </h4>
                              </div>
                          </div>
                          <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    From
                                    <address>
                                        <strong>{{ $data['basicInfo']['title'] }}</strong><br>
                                        {{ $data['basicInfo']['address'] }}<br>
                                        Phone-1: {{ $data['basicInfo']['phone'] }}<br>
                                        Phone-2: {{ $data['basicInfo']['telephone'] }}<br>
                                        Email: {{ $data['basicInfo']['email'] }}
                                    </address>
                                </div>
                                <div class="col-sm-4 invoice-col">
                                   
                                    To
                                    <address>
                                        <strong>{{ $data['master']['customer_name'] }}{{ $data['master']['bike_reg_no'] ? ' /Reg#' . $data['master']['bike_reg_no'] : null }}</strong><br>
                                        {{ $data['master']['customer_address'] }}<br>
                                        Phone: {{ $data['master']['customer_contact'] }}<br>
                                        Email: {{ $data['master']['customer_email'] }}<br>
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
                                            <th>SN</th>
                                            <th>Item Name</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Sub Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['details'] as $sd)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($sd['item_type']==0)
                                                            {{ $sd['item_name'] }}
                                                        @else
                                                            {{ $sd['service_name'] }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $sd['quantity'] }} {{ $sd['unit_name'] ?? "Service" }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($sd['unit_price'], 2) }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($sd['unit_price'] * $sd['quantity'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                  </table>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-6">
                                  <p class="lead">Payment Methods: {{ $data['master']['payment_method'] }}</p>
                              </div>
                              <div class="col-6">
                                  <div class="table-responsive">
                                      <table class="table">
                                          <tr>
                                              <th style="width:50%">Subtotal:</th>
                                              <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['total_price'], 2) }}</td>
                                          </tr>
                                          <tr>
                                              <th>Tax</th>
                                              <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['vat_tax'], 2) }}</td>
                                          </tr>
                                          <tr>
                                              <th>Discount:</th>
                                              <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['discount'], 2) }}</td>
                                          </tr>
                                          <tr>
                                              <th>Total:</th>
                                              <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['total_payable'], 2) }}</td>
                                          </tr>
                                          <tr>
                                              <th>Paid Amount:</th>
                                              <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['paid_amount'], 2) }}</td>
                                          </tr>
                                          <tr>
                                              <th>Due Amount:</th>
                                              <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['total_payable'] - $data['master']['paid_amount'], 2) }}</td>
                                          </tr>
                                      </table>
                                  </div>
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
  @endsection

@section('script')
    <script>
        $(document).ready(function() {
            if ("{{ $data['print'] }}" == 'print') {
              customPrint();
            }
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
