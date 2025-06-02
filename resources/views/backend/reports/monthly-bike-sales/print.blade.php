@extends('layouts.admin.master')
@section('content')
<style>
    td:nth-child(5){
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
                                            <th>Date</th>
                                            <th>Bike Model</th>
                                            <th>Buyer Name</th>
                                            <th>Sold Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalSales = 0;
                                                $totalCollection = 0;
                                                $pendingInstallment = 0;
                                            @endphp
                                            @foreach ($data['lists'] as $list)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $list['sale_date'] }}</td>
                                                    <td>{!! $list['model_name'] . ' ' . $list['color_name'] .'<br>Ch#' . $list['chassis_no'] . '<br>Reg#' . $list['registration_no'] !!}</td>
                                                    <td>{{ $list['buyer_name'] }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['sale_price'], 2) }}</td>
                                                </tr>
                                                @php
                                                    $totalSales += $list['sale_price'];
                                                    $totalCollection += $list['sale_price'];
                                                    $pendingInstallment += ($totalSales - $totalCollection);
                                                @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td @style('text-align: left;') colspan="4"><b>Total: </b></td>
                                                <td @style('text-align: right;') style="text-align: end"><b>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totalSales, 2) }}</b></td>
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
