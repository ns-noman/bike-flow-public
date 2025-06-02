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
                                  <strong>Report Name: </strong>Investor Ledger Report<br>
                                  <strong>Investor Info: </strong>{{ $data['investor_info']['name'] }}<br>
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
                                                <th>SN</th>
                                                <th>Date</th>
                                                <th>Particular</th>
                                                <th>Reference No</th>
                                                <th>Deposit</th>
                                                <th>Withdrawal</th>
                                                <th>Available Balance</th>
                                            </tr>  
                                        </thead>
                                        <tbody>
                                            @php
                                                if (count($data['lists'])) {
                                                    $brought_forword = $data['lists'][0]['current_balance'] + $data['lists'][0]['debit_amount'] - $data['lists'][0]['credit_amount'];
                                                    $bfRow =
                                                    [ 
                                                        'transaction_date'=> '',
                                                        'particular'=> 'B/F',
                                                        'reference_number'=> '',
                                                        'credit_amount'=> '',
                                                        'debit_amount'=> '',
                                                        'current_balance'=> $brought_forword,
                                                    ];
                                                    array_unshift($data['lists'], $bfRow);
                                                }
                                            @endphp
                                            @foreach ($data['lists'] as $list)
                                                <tr style="text-align: center">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $list['transaction_date'] }}</td>
                                                    <td>{{ $list['particular'] }}</td>
                                                    <td>{{ $list['reference_number'] }}</td>
                                                    <td>
                                                        @if($list['credit_amount'])
                                                            {{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['credit_amount'], 2) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($list['debit_amount'])
                                                            {{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['debit_amount'], 2) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['current_balance'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
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
