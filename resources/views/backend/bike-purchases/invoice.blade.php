@extends('layouts.admin.master')
@section('content')
  <div class="content-wrapper">
      @include('layouts.admin.content-header')
      <section class="content">
          <div class="container-fluid">
              <div class="row">
                  <div class="col-12">
                      <div class="invoice p-3 mb-3" id="my-invoice">
                        {{-- Header --}}
                        <div class="row mt-3">
                            <div class="col-4">
                                <h4>
                                    <img style="height: 150px;"
                                        src="{{ asset('public/uploads/basic-info/' . $data['basicInfo']['logo']) }}"
                                        alt="Logo" />
                                </h4>
                            </div>
                            <div class="col-4 text-center">
                                <h1>Purchase Receipt</h1>
                            </div>
                            <div class="col-4 text-right">

                                <address>
                                    {{-- <strong>{{ $data['basicInfo']['title'] }}</strong> --}}
                                    {{-- <br> --}}
                                    {{ $data['basicInfo']['address'] }}<br>
                                    মোবাইল: {{ $data['basicInfo']['phone'] }}<br>
                                    ই-মেইল: {{ $data['basicInfo']['email'] }}
                                </address>
                                {{-- <h4>
                                
                                  
                              </h4> --}}
                            </div>
                        </div>


                          {{-- <div class="row">
                              <div class="col-12">
                                  <h4>
                                    <img style="height: 50px;width: px;" src="{{ asset('public/uploads/basic-info/' . $data['basicInfo']['logo']) }}" alt="Logo" />
                                      {{ $data['basicInfo']['title'] }}
                                      <small class="float-right">Date: {{ date('dS M Y', strtotime($data['master']['purchase_date'])) }}</small>
                                  </h4>
                              </div>
                          </div> --}}
                          {{-- <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    From
                                    <address>
                                        <strong>{{ $data['master']['seller_name'] }}</strong><br>
                                        Phone: {{ $data['master']['seller_contact'] }}<br>
                                        NID: {{ $data['master']['seller_nid'] }} <br>
                                        DL No: {{ $data['master']['seller_dl_no'] }} <br>
                                        Passport No: {{ $data['master']['seller_passport_no'] }} <br>
                                        BCN No: {{ $data['master']['seller_bcn_no'] }} <br>
                                    </address>
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    To
                                    <address>
                                        <strong>{{ $data['basicInfo']['title'] }}</strong><br>
                                        {{ $data['basicInfo']['address'] }}<br>
                                        Phone-1: {{ $data['basicInfo']['phone'] }}<br>
                                        Phone-2: {{ $data['basicInfo']['telephone'] }}<br>
                                        Email: {{ $data['basicInfo']['email'] }}
                                    </address>
                                </div>
                              <div class="col-sm-4 invoice-col">
                                  <b>Invoice #{{ $data['master']['invoice_no'] }}</b><br>
                                  <br>
                                  <p><span><svg class="barcode"></svg></span></p>
                              </div>
                          </div> --}}
                          {{-- Customer Info --}}
                            <div class="row invoice-info mt-4">
                                <div class="col-sm-6 invoice-col">
                                    <strong>Seller Info</strong>
                                    <address>
                                        {{ $data['master']['seller_name'] }}<br>
                                        Phone: {{ $data['master']['seller_contact'] }}, NID: {{ $data['master']['seller_nid'] }}<br>
                                        DL No: {{ $data['master']['seller_dl_no'] }}, Passport: {{ $data['master']['seller_passport_no'] }}<br>
                                        BCN No: {{ $data['master']['seller_bcn_no'] }}
                                    </address>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <p>
                                        Date: {{ date('dS M Y', strtotime($data['master']['purchase_date'])) }} <br>
                                        <b>Vouchar #{{ $data['master']['invoice_no'] }}</b> <br>
                                        <span><svg class="barcode"></svg></span>
                                    </p>
                                </div>
                            </div>
                          {{-- Table --}}
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered mt-3">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Bike Model</th>
                                                <th>Registration No.</th>
                                                <th>Chasis No.</th>
                                                <th>Engine No.</th>
                                                <th class="text-right">Sale Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $data['master']['model_name'] }}
                                                    <span class="badge" style="background-color: {{ $data['master']['hex_code'] }};color: black; text-shadow: 2px 0px 8px white;">
                                                        {{ $data['master']['color_name'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $data['master']['registration_no'] }}</td>
                                                <td>{{ $data['master']['chassis_no'] }}</td>
                                                <td>{{ $data['master']['engine_no'] }}</td>
                                                <td class="text-right">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['purchase_price'], 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"><b>Total:</b></td>
                                                <td class="text-right"><b>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['purchase_price'], 2) }}</b></td>
                                            </tr>
                                            <tr>
                                                <td><b>In Word:</b></td>
                                                <td colspan="4"><b id="amount-in-word">Loading...</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Payment method --}}
                            <div class="row">
                                <div class="col-6">
                                    <p class="lead">Payment Method: {{ $data['master']['payment_method'] }}</p>
                                </div>
                            </div>

                           {{-- Terms --}}
                            <div class="container mt-4" style="font-size: 16px; line-height: 2;">
                                <p>
                                    সেই সাথে একনোলেজমেন্ট, স্মার্ট কার্ড, ট্যাক্স-টোকেন, কাগজপত্র বুঝাইয়া দিয়া বিক্রয় করিলাম-
                                </p>
                                <p>
                                    অদ্য <strong>&nbsp;&nbsp;{{ date('dS M Y', strtotime($data['master']['purchase_date'])) }}&nbsp;&nbsp;</strong> ইং তারিখের পূর্বে গাড়ী সম্বন্ধে কোন প্রকার মামলা থাকিলে তাহা <strong>&nbsp;&nbsp;{{ $data['basicInfo']['title'] }}&nbsp;&nbsp;</strong> বহন করিবেন
                                </p>
                                <p>
                                    এবং <strong>&nbsp;&nbsp;{{ date('dS M Y', strtotime($data['master']['purchase_date'])) }}&nbsp;&nbsp;</strong> ইং তারিখের পরে গাড়ী সংক্রান্ত কোন মামলা থাকিলে তাহা <strong>&nbsp;&nbsp;{{ $data['master']['seller_name'] }}&nbsp;&nbsp;</strong> বহন করিবেন।
                                </p>
                                <p>
                                    অদ্য <strong>&nbsp;&nbsp;{{ date('dS M Y', strtotime($data['master']['purchase_date'])) }}&nbsp;&nbsp;</strong> ইং তারিখ হইতে আগামী ১৫ দিনের মধ্যে গাড়ীর কাগজপত্রের নাম পরিবর্তন করা না হইলে সংশ্লিষ্ট বিষয়ে নাম পরিবর্তনে যে কোন জটিলতার জন্য <strong>&nbsp;&nbsp;{{ $data['basicInfo']['title'] }}&nbsp;&nbsp;</strong> সকল প্রকার দায়দায়িত্ব বহন করিবেন।
                                </p>
                                <p>
                                    উপরোক্ত নিয়ম-কানুন সমূহ আমরা উভয় পক্ষ সঠিক ভাবে পড়ে উহার মর্ম ভালোভাবে বুঝে অন্যের বিনা প্ররোচনায় স্বজ্ঞানে নিজ নিজ সহি সম্পাদন করিলাম।
                                </p>
                                <p>ইতি তাং <strong>&nbsp;&nbsp;{{ date('dS M Y', strtotime($data['master']['purchase_date'])) }}&nbsp;&nbsp;</strong>।</p>
                            </div>

                            {{-- Signatures --}}
                            <div class="row mt-5">
                                <div class="col text-left">
                                    <div
                                        style="border-top:1px solid #000; width:300px; text-align:center; margin-top:60px;">
                                        প্রদানকারী ({{ $data['master']['seller_name'] }})-এর স্বাক্ষর
                                    </div>
                                </div>
                                <div class="col text-right">
                                    <div
                                        style="border-top:1px solid #000; width:300px; text-align:center; margin-top:60px; margin-left:auto;">
                                        গ্রহণকারী ({{ $data['basicInfo']['title'] }})এর স্বাক্ষর
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="row no-print mt-4">
                                <div class="col">
                                    <a href="javascript:void(0)" onclick="customPrint()" class="btn btn-info"><i class="fas fa-print"></i> Print</a>
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
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
    // Barcode
    JsBarcode(".barcode", "{{ $data['master']['invoice_no'] }}", {
        width: 1,
        height: 30,
        displayValue: false
    });

    // Print handler
    function customPrint() {
        const printContents = document.getElementById('my-invoice').innerHTML;
        const originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    // PDF Export
    function downloadPDF() {
        const element = document.getElementById('my-invoice');
        html2pdf().from(element).save("invoice_{{ $data['master']['invoice_no'] }}.pdf");
    }

    // Convert number to Bangla words
    function numberToWords(num) {
        const a = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
        const b = ['','', 'Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

        if (num < 20) return a[num];
        if (num < 100) return b[Math.floor(num/10)] + (num % 10 ? " " + a[num % 10] : "");
        if (num < 1000) return a[Math.floor(num/100)] + " Hundred " + (num % 100 ? numberToWords(num % 100) : "");
        if (num < 100000) return numberToWords(Math.floor(num/1000)) + " Thousand " + (num % 1000 ? numberToWords(num % 1000) : "");
        if (num < 10000000) return numberToWords(Math.floor(num/100000)) + " Lakh " + (num % 100000 ? numberToWords(num % 100000) : "");
        return "Amount too large";
    }

    document.addEventListener('DOMContentLoaded', function () {
        let amount = {{ $data['master']['purchase_price'] }};
        let words = numberToWords(amount);
        document.getElementById('amount-in-word').textContent = words + " Taka Only";

        @if ($data['print'] == 'print')
            setTimeout(function () {
                customPrint();
                window.history.replaceState({}, document.title, window.location.pathname); // prevent print loop
            }, 500);
        @endif
    });


</script>
@endsection



{{-- @section('script')
    <script>
        $(document).ready(function() {
            if ("{{ $data['print'] }}" == 'print') {
              customPrint();
            }
            $('.pay-now').on('click', function(e) {
                $('#purchase_id').val($(this).attr('purchase-id'));
                $('#amount').val(parseFloat($(this).attr('due')).toFixed(2));
            });
        });
        JsBarcode(".barcode", "{{ $data['master']['invoice_no'] }}", {
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
@endsection --}}
