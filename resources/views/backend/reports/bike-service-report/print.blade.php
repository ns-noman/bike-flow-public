@extends('layouts.admin.master')
@section('content')
<style>
    td:nth-child(4),
    td:nth-child(5) ,
    td:nth-child(6) {
        text-align: right !important;
    }
    th{
        text-align: center;
    }
</style>
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
                                      <small class="float-right">Print Date: {{ date('dS M Y', strtotime(Date('Y-m-d'))) }}</small>
                                  </h4>
                              </div>
                          </div>
                          <div class="row invoice-info" style="margin-top: 100px;">
                              <div class="col-sm-6 invoice-col">
                                  <strong>Report Name: </strong>Bike Service Report <br>
                              </div>
                              <div class="col-sm-6 invoice-col">
                              </div>
                              <div class="col-sm-4 invoice-col">
                              </div>
                          </div>
                          <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-bordered mt-3">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Bike Model</th>
                                            <th>Registration No.</th>
                                            <th>Chasis No.</th>
                                            <th>Engine No.</th>
                                            <th class="text-right">Purchase Price</th>
                                            <th class="text-right">Total Cost</th>
                                            <th class="text-right">Net Bike Cost</th>
                                            <th class="text-right">Sale Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $data['master']['model_name'] }}
                                                <span class="badge" style="background-color: {{ $data['master']['hex_code'] }};color: black; text-shadow: 2px 0px 8px white;">
                                                    {{ $data['master']['color_name'] }}
                                                </span>
                                            </td>
                                            <td>{{ $data['master']['registration_no'] }}</td>
                                            <td>{{ $data['master']['chassis_no'] }}</td>
                                            <td>{{ $data['master']['engine_no'] }}</td>
                                            <td class="text-right">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['purchase_price'], 2) }}</td>
                                            <td class="text-right">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['servicing_cost'], 2) }}</td>
                                            <td class="text-right">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['total_cost'], 2) }}</td>
                                            <td class="text-right">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['sale_price'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                          <div class="row">
                              <div class="col-12 table-responsive">
                                  <table class="table table-sm table-striped table-bordered table-centre text-center">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Date</th>
                                                <th>Service Name</th>
                                                <th>Quantity </th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                                <th>Ref Invoice #</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $subtotal = 0;
                                                $total = 0;
                                            @endphp
                                            @foreach ($data['lists'] as $list)
                                                @php
                                                    $subtotal = $list['quantity'] * $list['price'];
                                                    $total += $subtotal;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $list['date'] }}</td>
                                                    <td>{{ $list['bike_service_name'] }}</td>
                                                    <td>{{ $list['quantity'] }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ $list['price'] }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ $list['quantity'] * $list['price'] }}</td>
                                                    <td>{{ $list['invoice_no'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td @style('text-align: left') colspan="5"><b>Total:</b></td>
                                                <td @style('text-align: right')><b id="total_service_cost">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($total, 2) }}</b></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                  </table>
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
            if (true) {
              customPrint();
            }
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
