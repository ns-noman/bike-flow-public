@extends('layouts.admin.master')
@section('content')
<style>
        td:nth-child(3),
        td:nth-child(4),
        td:nth-child(5),
        td:nth-child(6){
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
                                  <strong>Report Name: </strong>Bike Inventory Report <br>
                                  <strong>Status: </strong>{{ $data['selling_status'] == 0 ? "Available" : "Sold" }} <br>
                                  <strong>Period: </strong>{{ \Carbon\Carbon::parse(Date('Y-m-d'))->format('F Y') }}
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
                                            <th>Bike Info</th>
                                            <th>Purchase Price</th>
                                            <th>Repair Cost</th>
                                            <th>Total Cost</th>
                                            <th>Sold Price</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalPurchasePrice = 0;
                                                $totolServiceCost = 0;
                                                $totalCost = 0;
                                                $totalSalePrice = 0;
                                            @endphp
                                            @foreach ($data['lists'] as $list)
                                                @php
                                                    $totalPurchasePrice += $list['purchase_price'];
                                                    $totolServiceCost += $list['servicing_cost'];
                                                    $totalCost += $list['total_cost'];
                                                    $totalSalePrice += $list['sale_price'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td @style('text-align: left') >{!! $list['model_name'] . ' ' . $list['color_name'] .'<br>Ch#' . $list['chassis_no'] . '<br>Reg#' . $list['registration_no'] !!}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['purchase_price'], 2) }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['servicing_cost'], 2) }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['total_cost'], 2) }}</td>
                                                    <td>
                                                        @if($list['sale_price'])
                                                            {{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['sale_price'], 2) }}
                                                        @else
                                                            --
                                                        @endif
                                                    </td>
                                                    <td>{{ $list['selling_status'] == '0' ? 'Available' : 'Sold' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tr>
                                            <td @style('text-align: left') colspan="2"><b>Total:</b></td>
                                            <td @style('text-align: right')><b id="totalPurchasePrice">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totalPurchasePrice, 2) }}</b></td>
                                            <td @style('text-align: right')><b id="totolServiceCost">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totolServiceCost, 2) }}</b></td>
                                            <td @style('text-align: right')><b id="totalCost">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totalCost, 2) }}</b></td>
                                            <td @style('text-align: right')>
                                                @if($totalSalePrice)
                                                    <b id="totalSalePrice">
                                                        {{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totalSalePrice, 2) }}
                                                    </b>
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td></td>
                                        </tr>
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
