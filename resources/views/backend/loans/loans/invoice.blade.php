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
                                      <small class="float-right">Date: {{ date('dS M Y', strtotime($data['master']['loan_date'])) }}</small>
                                  </h4>
                              </div>
                          </div>
                          <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    {{-- From --}}
                                    <address>
                                        <strong>{{ $data['basicInfo']['title'] }}</strong><br>
                                        {{ $data['basicInfo']['address'] }}<br>
                                        Phone-1: {{ $data['basicInfo']['phone'] }}<br>
                                        Phone-2: {{ $data['basicInfo']['telephone'] }}<br>
                                        Email: {{ $data['basicInfo']['email'] }}
                                    </address>
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    {{ $data['master']['loan_type'] == 1 ? "Loan Taken From" : "Loan Given To" }}
                                    <address>
                                        <strong>{{ $data['master']['party_name'] }}</strong><br>
                                        {{ $data['master']['party_address'] }}<br>
                                        Phone: {{ $data['master']['party_contact_no'] }}<br>
                                        Email: {{ $data['master']['party_email'] }}<br>
                                    </address>
                                </div>
                              <div class="col-sm-4 invoice-col">
                                  <b>Loan No #{{ $data['master']['loan_no'] }}</b><br>
                                  <br>
                                  <p><span><svg class="barcode"></svg></span></p>
                              </div>
                          </div>
                          <div class="row">
                            <div class="col-12">
                                <h4>Loan Info</h4>
                            </div>
                              <div class="col-12 table-responsive">
                                  <table class="table table-striped">
                                      <thead>
                                        <tr>
                                            <th>Loan Date</th>
                                            <th>Due Date</th>
                                            <th>Last Payment Date</th>
                                            <th>Loan Amount</th>
                                            <th>Paid</th>
                                            <th>Due</th>
                                            <th>Payemnt Method</th>
                                            <th>Reference Number</th>
                                            <th>Note</th>
                                            <th>Payment Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $data['master']['loan_date'] }}</td>
                                                <td>{{ $data['master']['due_date'] }}</td>
                                                <td>{{ $data['master']['last_payment_date'] }}</td>
                                                <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['amount'], 2) }}</td>
                                                <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['paid_amount'], 2) }}</td>
                                                <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['amount'] - $data['master']['paid_amount'], 2) }}</td>
                                                <td>{{ $data['master']['payment_method'] }}</td>
                                                <td>{{ $data['master']['reference_number'] }}</td>
                                                <td>{{ $data['master']['note'] }}</td>
                                                <td>
                                                    @if ($data['master']['payment_status'] == 1)
                                                        Paid
                                                    @elseif ($data['master']['payment_status'] == -1)
                                                        Partial
                                                    @else
                                                        Pending
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                  </table>
                              </div>
                          </div>
                          <div class="row">
                            <div class="col-12">
                                <h4>{{ $data['master']['loan_type'] == 1 ? "Payment" : "Collection" }} History</h4>
                            </div>
                            <div class="col-12">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th>Payment Date</th>
                                                <th>Amount</th>
                                                <th>Payment Method</th>
                                                <th>Reference No</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($data['details']))
                                                @foreach ($data['details'] as $details)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $details['date'] }}</td>
                                                        <td>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($details['amount'], 2) }}</td>
                                                        <td>{{ $details['payment_method'] }}</td>
                                                        <td>{{ $details['reference_number'] }}</td>
                                                        <td>{{ $details['note'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center"> 
                                                        <h5>No Payment Found!</h5>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
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
  @endsection

@section('script')
    <script>
        $(document).ready(function() {
            if ("{{ $data['print'] }}" == 'print') {
              customPrint();
            }
        });
        JsBarcode(".barcode", "{{ $data['master']['loan_no'] }}", {
            width: 1,
            height: 30,
            displayValue: false
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
