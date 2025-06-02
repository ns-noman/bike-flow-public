@extends('layouts.admin.master')
@section('content')
<style>
td:nth-child(8),
td:nth-child(7) ,
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
                              <div class="col-sm-4 invoice-col">
                                  <strong>Report Name: </strong>Item Stock Report <br>
                                  {{-- <strong>Period: </strong>{{ \Carbon\Carbon::parse(date('Y'))->format('F Y') }} --}}

                              </div>
                              <div class="col-sm-4 invoice-col">
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
                                                <th width="40%" @style('text-align:center;')>Title</th>
                                                <th>CategoryType</th>
                                                <th>Category</th>
                                                <th>SubCategory</th>
                                                <th>PurchasePrice</th>
                                                <th width="10%">Stock</th>      
                                                <th width="15%">StockValue</th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $stockValue = 0;
                                            @endphp
                                            @foreach ($data['lists'] as $list)
                                                @php
                                                    $subtotal = $list['current_stock'] * $list['purchase_price'];
                                                    $stockValue += $subtotal;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $list['name'] }}</td>
                                                    <td>{{ $list['cat_type_name'] }}</td>
                                                    <td>{{ $list['cat_name'] }}</td>
                                                    <td>{{ $list['sub_cat_name'] }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($list['purchase_price'], 2) }}</td>
                                                    <td>{{ $list['current_stock'] }} {{ $list['unit_name'] }}</td>
                                                    <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($subtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="7" st><b>Total:</b></td>
                                                <td @style('text-align: right')><b id="totalStockValue">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($stockValue, 2) }}</b></td>
                                            </tr>
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
