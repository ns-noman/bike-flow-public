@extends('layouts.admin.master')
@section('content')
    @php
        $roleType = Auth::guard('admin')->user()->type;
        $investor_id = Auth::guard('admin')->user()->investor_id;
    @endphp
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
                                  <strong>Report Name: </strong>Bike Profit Share Report<br>
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
                                                <th>Bike Details</th>
                                                <th>Investor</th>
                                                @if($investor_id==1)
                                                    <th>Total Profit</th>
                                                @endif
                                                <th>Profit Shared</th>
                                                <th>Sale Date</th>
                                                <th>WithdrawalHistory</th>
                                                <th>Status</th>
                                            </tr>  
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalProfitShared = 0;
                                            @endphp
                                            @foreach ($data['lists'] as $list)
                                                @php
                                                    $totalProfitShared += $list['profit_share_amount'];
                                                @endphp
                                                <tr style="text-align: center">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{!! $list['model_name'] . ' ' . $list['color_name'] .'<br>Ch#' . $list['chassis_no'] . '<br>Reg#' . $list['registration_no'] !!}</td>
                                                    <td>{{ $list['investor_name'] }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['profit_amount'], 2) }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['profit_share_amount'], 2) }}</td>
                                                    <td>{{ $list['profit_entry_date'] }}</td>
                                                    <td>
                                                        @if(count($list['paymenthistory']))
                                                            <table class="table table-sm table-striped table-info table-center rounded m-0" style="vertical-align: middle;">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center" style="width: auto;">Date</th>
                                                                        <th class="text-center" style="width: auto;">Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach ($list['paymenthistory'] as $key=> $paymenthistory)
                                                                    <tr>
                                                                        <td style="vertical-align: middle;width: auto;">{{ $paymenthistory['date'] }}</td>
                                                                        <td style="vertical-align: middle;width: auto;">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($paymenthistory['amount'], 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        @else
                                                            Pending
                                                        @endif
                                                    </td>
                                                    <td>{{ $list['status'] == 0 ? 'Open' : 'Closed' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4"><b>Total:</b></td>
                                                <td @style('text-align: right')><b class="totalProfitShared">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totalProfitShared, 2) }}</b></td>
                                                <td></td>
                                                <td @style('text-align: right')><b class="totalProfitShared">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($totalProfitShared, 2) }}</b></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
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
