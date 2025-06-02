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
                                      <small class="float-right">Print Date: {{ date('dS M Y', strtotime(Date('Y-m-d'))) }}</small>
                                  </h4>
                              </div>
                          </div>
                          <div class="row invoice-info" style="margin-top: 100px;">
                              <div class="col-sm-4 invoice-col">
                                  <strong>Report Name: </strong>Accounts Report<br>
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
                                          <tr style="text-align: center;">
                                                <th>SN</th>
                                                <th>Payment Method</th>
                                                <th>Account No</th>
                                                <th>Account Holder</th>
                                                <th>Balance</th>
                                            </tr>  
                                        </thead>
                                        <tbody>
                                        @php
                                            $totalAmount = 0;
                                        @endphp
                                        @foreach ($data['lists'] as $list)
                                            <tr style="text-align: center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $list['payment_method'] }}</td>
                                                <td>{{ $list['account_no'] }}</td>
                                                <td>{{ $list['holder_name'] }}</td>
                                                <td style="text-align: right">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['balance'], 2) }}</td>
                                            </tr>
                                            @php
                                                $totalAmount += $list['balance'];
                                            @endphp
                                        @endforeach
                                            <tr>
                                                <td colspan="4"><b>Total: </b></td>
                                                <td style="text-align: end"><b>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totalAmount, 2) }}</b></td>
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
