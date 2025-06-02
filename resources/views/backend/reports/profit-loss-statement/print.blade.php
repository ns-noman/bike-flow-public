@extends('layouts.admin.master')
@section('content')
  <div class="content-wrapper">
      @include('layouts.admin.content-header')
      <style>
        td{
            text-align: right;
            padding-left: 20px!important;
        }
    </style>
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
                                  <strong>Report Name: </strong>Profit And Loss Statement<br>
                                  <strong>Period: </strong>{{ \Carbon\Carbon::parse($data['date'])->format('F Y') }}
                              </div>
                              <div class="col-sm-4 invoice-col">
                              </div>
                              <div class="col-sm-4 invoice-col">
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-12 table-responsive">
                                  <table class="table table-striped">
                                    <thead>
                                        <tr>
                                           <th>Description</th>
                                           <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th colspan="2" style="text-align: center;"><h5><b>Income</b></h5></th>
                                        </tr>
                                        <tr>
                                            <th>Bike Sale Profit</th>
                                            <td id="bike_sale_profit">{{ $data['basicInfo']['currency_symbol'] }} {{ $data['lists']['bike_sales'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <th>Regular Sales</th>
                                            <td id="regular_sale">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format(($data['lists']['regular_sales'] ?? 0), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Service Sales</th>
                                            <td id="service_sales">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format(($data['lists']['service_sales'] ?? 0), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Sale Revenew:</th>
                                            <td><b id="total_sale_revenew">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format(($data['lists']['total_incomes'] ?? 0), 2) }}</b></td>
                                        </tr>
                                        <tr id="expenses_title">
                                            <th colspan="2" style="text-align: center;"><h5><b>Expenses</b></h5></th>
                                        </tr>
                                        @foreach ($data['lists']['expenses'] as $key => $value)
                                           <tr>
                                                <th>{{ $value->expense_name }}</th>
                                                <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($value['expense_sub_total'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th><b>Total Expenses : </b></th>
                                            <td><b id="total_expenses">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['lists']['total_expenses'], 2) }}</b></td>
                                        </tr>
                                        <tr>
                                            <th><b>Net Profit : </b></th>
                                            <td><b id="net_profit">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['lists']['net_profit'], 2) }}</b></td>
                                        </tr>
                                    </tbody>
                                  </table>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-6">
                              </div>
                              <div class="col-6">
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
