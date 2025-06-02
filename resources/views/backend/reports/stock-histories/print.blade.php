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
                                  <strong>Report Name: </strong>Item Stock History <br>
                                  <strong>Item Name: </strong>{{ count($data['lists']) > 0 ? $data['lists'][0]['item_name'] : null }}<br>
                                  <strong>Period: </strong>{{ \Carbon\Carbon::parse($data['date'])->format('F Y') }}

                              </div>
                              <div class="col-sm-6 invoice-col">
                              </div>
                              <div class="col-sm-4 invoice-col">
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-12 table-responsive">
                                  <table class="table table-sm table-striped table-bordered table-centre">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th>Date</th>
                                                <th>Particular</th>
                                                <th>InQty</th>
                                                <th>OutQty</th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $inQty = 0;
                                                $outQty = 0;
                                                $balance = 0;
                                                
                                                if (count($data['lists'])) {
                                                    $brought_forword = $data['lists'][0]['current_stock'] - $data['lists'][0]['stock_in_qty'] + $data['lists'][0]['stock_out_qty'];
                                                    $bfRow = [
                                                        'date'=> '',
                                                        'particular'=> 'B/F',
                                                        'stock_in_qty'=> $brought_forword,
                                                        'stock_out_qty'=> null,
                                                        'current_stock'=> $brought_forword,
                                                        'unit_name'=> $data['lists'][0]['unit_name'],
                                                    ];
                                                    array_unshift($data['lists'], $bfRow);
                                                }
                                            @endphp

                                            @foreach ($data['lists'] as $list)
                                                @php
                                                    $inQty += $list['stock_out_qty'] ?? 0;
                                                    $outQty += $list['stock_out_qty'] ?? 0;
                                                    $balance = $inQty - $outQty;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $list['date'] }}</td>
                                                    <td>{{ $list['particular'] }}</td>
                                                    <td>
                                                        @if($list['stock_in_qty'])
                                                            +{{ $list['stock_in_qty'] }} {{ $list['unit_name'] }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($list['stock_out_qty'])
                                                            -{{ $list['stock_out_qty'] }} {{ $list['unit_name'] }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $list['current_stock'] }} {{ $list['unit_name'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if(count($data['lists']))
                                                <tr>
                                                    <td colspan="3"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b id="totalInQty">+{{ $inQty }} {{ $list['unit_name'] }}</b></td>
                                                    <td @style('text-align: right')><b id="totalOutQty">-{{ $outQty }} {{ $list['unit_name'] }}</b></td>
                                                    <td @style('text-align: right')><b id="balanceQty">{{ $balance }} {{ $list['unit_name'] }}</b></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
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
