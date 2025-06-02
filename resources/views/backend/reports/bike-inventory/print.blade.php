@extends('layouts.admin.master')
@section('content')
<style>
    td:nth-child(3),
    td:nth-child(5),
    td:nth-child(4){
        text-align: right !important;
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
                              <div class="col-sm-4 invoice-col">
                                  <strong>Report Name: </strong>Monthly Bike Sales Report <br>
                                  <strong>Period: </strong>{{ \Carbon\Carbon::parse($data['date'])->format('F Y') }}
                              </div>
                              <div class="col-sm-4 invoice-col">
                              </div>
                              <div class="col-sm-4 invoice-col">
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-12 table-responsive">
                                  <table class="table table-striped text-center">
                                      <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Bike Model</th>
                                            <th>Purchase Price</th>
                                            <th>Repair Cost</th>
                                            <th>Total Cost</th>
                                            {{-- <th>Selling Price(BDT)</th>
                                            <th>Status</th> --}}
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $grand_total_cost = 0;
                                                $total_purchase_price = 0;
                                                $total_servicing_cost = 0;
                                            @endphp
                                            @foreach ($data['lists'] as $list)
                                                @php
                                                    $grand_total_cost += $list['total_cost'];
                                                    $total_purchase_price += $list['purchase_price'];
                                                    $total_servicing_cost += $list['servicing_cost'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $list['model_name'] }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['purchase_price'], 2) }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['servicing_cost'], 2) }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['total_cost'], 2) }}</td>
                                                    {{-- <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['sale_price'], 2) }}</td>
                                                    <td>{{ $list['selling_status'] == '0' ? 'Available' : 'Sold' }}</td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"@style('text-align: left')><b>Total:</b></td>
                                                <td @style('text-align: right')><b id="total_servicing_cost">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($total_purchase_price, 2) }}</b></td>
                                                <td @style('text-align: right')><b id="grand_total_cost">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($total_servicing_cost, 2) }}</b></td>
                                                <td @style('text-align: right')><b id="total_purchase_price">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($grand_total_cost, 2) }}</b></td>
                                            </tr>
                                        </tfoot>
                                  </table>
                              </div>
                          </div>
                          {{-- <div class="row">
                              <div class="col-6">
                                <h4>Summary: </h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Total Bikes in Inventory:</th>
                                            <td>{{ $data['totalBikes'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sold Bikes in: </th>
                                            <td>{{ $data['soldBikesInDate'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Unsold Bikes: </th>
                                            <td>{{ $data['unsoldBikes'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                              </div>
                              <div class="col-6">
                              </div>
                          </div> --}}
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
