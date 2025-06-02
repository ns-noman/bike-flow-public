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
                                <h1>Money Receipt</h1>
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

                        {{-- Customer Info --}}
                        <div class="row invoice-info mt-4">
                            <div class="col-sm-6 invoice-col">
                                <strong>Buyer Info</strong>
                                <address>
                                    {{ $data['master']['buyer_name'] }}<br>
                                    Phone: {{ $data['master']['buyer_contact'] }}, NID: {{ $data['master']['buyer_nid'] }}<br>
                                    DL No: {{ $data['master']['buyer_dl_no'] }}, Passport: {{ $data['master']['buyer_passport_no'] }}<br>
                                    BCN No: {{ $data['master']['buyer_bcn_no'] }}
                                </address>
                            </div>
                            <div class="col-sm-6 text-right">
                                <p>
                                    Date: {{ date('dS M Y', strtotime($data['master']['sale_date'])) }} <br>
                                    <b>Invoice #{{ $data['master']['invoice_no'] }}</b> <br>
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
                                            <td class="text-right">{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['sale_price'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><b>Total:</b></td>
                                            <td class="text-right"><b>{{ $data['basicInfo']['currency_symbol'] }} {{ number_format($data['master']['sale_price'], 2) }}</b></td>
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

                        {{-- Checkboxes --}}
                        <div class="row justify-content-center text-center">
                            <div class="form-row">
                                @php
                                    $checkboxes = [
                                        '২ পিছ ছবি' => 'copy',
                                        'সার্ভিস বুক (যদি থাকে)' => 'serviceBook',
                                        'রেজিস্ট্রেশন পেপার' => 'regPaper',
                                        'স্ট্যাম্প' => 'stamp',
                                    ];
                                @endphp
                                @foreach($checkboxes as $label => $id)
                                    <div class="form-group col-auto p-5">
                                        <div class="form-check">
                                            <input class="form-check-input" style="transform: scale(3);margin-right: 20px;" type="checkbox" id="{{ $id }}">
                                            <label class="form-check-label ml-3" for="{{ $id }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        {{-- Terms --}}
                        <div class="container mt-4" style="font-size: 16px; line-height: 2;">
                            <p>
                                সেই সাথে একনোলেজমেন্ট, স্মার্ট কার্ড, ট্যাক্স-টোকেন, কাগজপত্র যথাযথভাবে বুঝাইয়া দিয়ে বিক্রয় করিলাম -
                            </p>
                            <p>
                                অদ্য <strong>{{ date('dS M Y', strtotime($data['master']['sale_date'])) }}</strong> ইং তারিখের পূর্বে গাড়ী সম্পর্কিত কোন প্রকৃত মামলা থাকিলে তাহা <strong>{{ $data['basicInfo']['title'] }}</strong> বহন করিবেন এবং পরবর্তীতে ক্রেতা বহন করিবেন।
                            </p>
                            <p>
                                অদ্য তারিখ হইতে আগামী ১৫ দিনের মধ্যে কাগজপত্রের মালিকানা পরিবর্তন না হইলে, পরবর্তীতে জটিলতার জন্য <strong>{{ $data['basicInfo']['title'] }}</strong> দায়ী থাকিবেন না।
                            </p>
                            <p>
                                উপরোক্ত নিয়ম-কানুন দুই পক্ষ স্বজ্ঞানে বুঝে সম্মতি দিলাম।
                            </p>
                            <p>ইতি তাং <strong>{{ date('dS M Y', strtotime($data['master']['sale_date'])) }}</strong>।</p>
                        </div>

                        {{-- Signatures --}}
                        <div class="row mt-5">
                            <div class="col text-left">
                                <div
                                    style="border-top:1px solid #000; width:300px; text-align:center; margin-top:60px;">
                                    প্রদানকারী ({{ $data['basicInfo']['title'] }})-এর স্বাক্ষর
                                </div>
                            </div>
                            <div class="col text-right">
                                <div
                                    style="border-top:1px solid #000; width:300px; text-align:center; margin-top:60px; margin-left:auto;">
                                    গ্রহণকারী (ক্রেতা)এর স্বাক্ষর
                                </div>
                            </div>
                        </div>
                        {{-- Note --}}
                        <div class="row">
                            <div class="container mt-5">
                                <div style="border-top: 2px solid #000; padding-top: 10px; font-size: 16px;">
                                    <strong>বিঃদ্রঃ</strong> {{ $data['basicInfo']['title'] }} এর নিকট ক্রয়কৃত বাইক ব্যবহারের পর বিক্রি/ফেরত দিতে চাইলে 
                                    আলোচনার ভিত্তিতে মূল্য নির্ধারণ করা হবে।
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="row no-print mt-4">
                            <div class="col">
                                <a href="javascript:void(0)" onclick="customPrint()" class="btn btn-info"><i class="fas fa-print"></i> Print</a>
                            </div>
                        </div>
                    </div> <!-- /invoice -->
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
        let amount = {{ $data['master']['sale_price'] }};
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
