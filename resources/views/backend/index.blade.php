@inject('authorization', 'App\Services\AuthorizationService')
@extends('layouts.admin.master')
@section('content')
    @php
        $investor_id = Auth::guard('admin')->user()->investor_id;
    @endphp
    <style>
        td:nth-child(6),
        td:nth-child(7),
        td:nth-child(8) {
            text-align: right !important;
        }

        /* table tr td:nth-child(2),
            table tr th:nth-child(2) {
                text-align: left !important;
            } */

        .cursor-pointer {
            cursor: pointer;
        }

        .active-cust {
            border: 2px solid #03d6ba;
            background-color: #dcebe9;
        }

        .bikefilter {
            font-size: 16px;
        }

        .bikefilter>.info-box-content {
            padding: 0 2px;
        }

        .bikefilter>.info-box-icon {
            width: 60px !important;
        }
    </style>
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @if ($authorization->hasMenuAccess(190))
                        <div class="col-lg-12">
                            <h5 class="mb-2">Financial Status</h5>
                            <div class="row">
                                <div class="col" {{ !$authorization->hasMenuAccess(191) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('investors.index') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Investment Capital</span>
                                            <span class="info-box-number">
                                                @php
                                                    $totalInvestment = $data['investors_capital'] + $data['my_capital'];
                                                @endphp
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($totalInvestment, 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(192) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('reports.stock-reports') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Item Stock Value</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['stockValueItem'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(193) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('reports.bike-inventory') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Bike Stock Value</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['stockValueBike'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(194) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('accounts.index') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Cash Balance</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['allAccountBalance'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(195) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('expenses.index') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Expense</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['totalExpenses_exp'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(196) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('purchases.index') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Purchase</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['totalPurchase_exp'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(197) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('sales.index') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sale</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['totalSale_inc'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(201) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('bike-service-records.index') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Bike Service Expense</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['total_bike_service_expense'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($authorization->hasMenuAccess(189))
                        <div class="col-lg-12">
                            <h5 class="mb-2">Loan Status</h5>
                            <div class="row">
                                <div class="col" {{ !$authorization->hasMenuAccess(198) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('loans.index') }}">
                                        <span class="info-box-icon bg-success elevation-1">
                                            <i class="fa fa-wallet"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Loan Amount Receiveable</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['totalLoanReceiveable'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(199) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('loans.index') }}">
                                        <span class="info-box-icon bg-warning elevation-1">
                                            <i class="fa fa-wallet"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Loan Amount Payable</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['totalLoanPayable'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($authorization->hasMenuAccess(182))
                        <div class="col-lg-12">
                            <div class="row d-flex justify-content-center">
                                <div class="col-6">
                                    <div class="form-group d-flex align-items-center">
                                        <label for="reservation" class="mr-2 mb-0"
                                            style="white-space: nowrap;">Filter:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control float-right" id="reservation">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h5 class="mb-2">Purchase, Sales & Expense Info</h5>
                            <div class="row">
                                <div class="col" {{ !$authorization->hasMenuAccess(170) ? 'hidden' : null }}>
                                    <div class="info-box mb-3 activeable2" data-filteron="" href="javascript:void()">
                                        <span class="info-box-icon bg-success elevation-1">
                                            <i class="fa fa-shopping-cart"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Accessories Sales</span>
                                            <span class="info-box-number" id="accessoriesSale">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(171) ? 'hidden' : null }}>
                                    <div class="info-box mb-3 activeable2" data-filteron="" href="javascript:void()">
                                        <span class="info-box-icon bg-primary elevation-1">
                                            <i class="fa fa-tools"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Service Sales</span>
                                            <span class="info-box-number" id="serviceSale">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(172) ? 'hidden' : null }}>
                                    <div class="info-box mb-3 activeable2" data-filteron="" href="javascript:void()">
                                        <span class="info-box-icon bg-warning elevation-1">
                                            <i class="fa fa-cogs"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Spare Parts Sales</span>
                                            <span class="info-box-number" id="sparePartsSale">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(178) ? 'hidden' : null }}>
                                    <div class="info-box mb-3 activeable2" data-filteron="" href="javascript:void()">
                                        <span class="info-box-icon bg-danger elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Item & Service Sales</span>
                                            <span class="info-box-number" id="total_service_and_item_sales">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(179) ? 'hidden' : null }}>
                                    <div class="info-box mb-3 activeable2" data-filteron="">
                                        <span class="info-box-icon bg-secondary elevation-1">
                                            <i class="fa fa-wallet"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Purchase</span>
                                            <span class="info-box-number" id="purchase">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(169) ? 'hidden' : null }}>
                                    <div class="info-box mb-3 expenseFilter activeable2" data-filteron=""
                                        href="javascript:void()">
                                        <span class="info-box-icon bg-dark elevation-1">
                                            <i class="fa fa-money-bill-wave"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Expense</span>
                                            <span class="info-box-number" id="expenses">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($authorization->hasMenuAccess(183))
                        <div class="col-lg-12">
                            <h5 class="mb-2">Investment Info</h5>
                            <div class="row">
                                <div class="col" {{ !$authorization->hasMenuAccess(173) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('reports.bike-profit') }}">
                                        <span class="info-box-icon bg-danger elevation-1">
                                            <i class="fa fa-credit-card"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Today's Investor's Profit Payment</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['investorProfitPayment'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(174) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('investor-transactions.index') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-piggy-bank"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Today's New Investment</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['newinvestments'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(175) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('investor-transactions.index') }}">
                                        <span class="info-box-icon bg-secondary elevation-1">
                                            <i class="fa fa-sign-out-alt"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Today's Investment Withdraw</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['investmentwithdrawal'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(180) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('investors.index') }}">
                                        <span class="info-box-icon bg-success elevation-1">
                                            <i class="fa fa-wallet"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Investors Investment Capital</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['investors_capital'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(181) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('reports.investment') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-wallet"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">My Investment Capital</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['my_capital'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(200) ? 'hidden' : null }}>
                                    <a class="info-box cursor-pointer mb-3 investorsfilter activeable2" data-filteron=""
                                        href="{{ route('reports.investor-ledger') }}">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-wallet"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">My Available Balance</span>
                                            <span class="info-box-number">
                                                {{ $data['basicInfo']['currency_symbol'] }}
                                                {{ number_format($data['my_available_balance'], 2) }}
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($authorization->hasMenuAccess(184))
                        <div class="col-lg-12">
                            <h5 class="mb-2">Bike Info</h5>
                            <div class="row">
                                <div class="col" {{ !$authorization->hasMenuAccess(46) ? 'hidden' : null }}>
                                    <div class="info-box cursor-pointer bikefilter activeable drawable {{ $investor_id == 1 ? 'active-cust' : null }}"
                                        data-filteron="bikestock">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fa fa-motorcycle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bike Stock</span>
                                            <span class="info-box-number">{{ $data['bike_stock'] }}</i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(47) ? 'hidden' : null }}>
                                    <div class="info-box cursor-pointer mb-3 bikefilter activeable drawable"
                                        data-filteron="investorbikes">
                                        <span class="info-box-icon bg-warning elevation-1">
                                            <i class="fa fa-motorcycle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Investors Bike</span>
                                            <span class="info-box-number">{{ $data['investor_bike'] }}</i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(48) ? 'hidden' : null }}>
                                    <div class="info-box cursor-pointer mb-3 bikefilter activeable drawable {{ $investor_id != 1 ? 'active-cust' : null }}"
                                        data-filteron="mybikes">
                                        <span class="info-box-icon bg-success elevation-1">
                                            <i class="fa fa-motorcycle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">My Bikes</span>
                                            <span class="info-box-number">{{ $data['my_bike'] }}</i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(49) ? 'hidden' : null }}>
                                    <div class="info-box cursor-pointer mb-3 bikefilter activeable"
                                        data-filteron="soldbikes">
                                        <span class="info-box-icon bg-danger elevation-1">
                                            <i class="fa fa-motorcycle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sold</span>
                                            <span class="info-box-number">{{ $data['total_sold'] }}</i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(50) ? 'hidden' : null }}>
                                    <div class="info-box cursor-pointer mb-3 bikefilter activeable"
                                        data-filteron="todayspurchases">
                                        <span class="info-box-icon bg-primary elevation-1">
                                            <i class="fa fa-motorcycle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Today's Purchase</span>
                                            <span class="info-box-number">{{ $data['todayspurchase'] }}</i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col" {{ !$authorization->hasMenuAccess(168) ? 'hidden' : null }}>
                                    <div class="info-box cursor-pointer mb-3 bikefilter activeable"
                                        data-filteron="todayssales">
                                        <span class="info-box-icon bg-dark elevation-1">
                                            <i class="fa fa-motorcycle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Today's Sale</span>
                                            <span class="info-box-number">{{ $data['todayssales'] }}</i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark p-1">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <h1 class="card-title text-info">Bike List</h1>
                                        </div>
                                        <div class="col-md-10">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bootstrap-data-table-panel">
                                        <div class="table-responsive" id="table-holder">
                                            <table id="dataTable"
                                                class="table table-sm table-striped table-bordered table-centre text-center">
                                                <thead>
                                                    <tr>
                                                        <th>SN</th>
                                                        <th>Bike Info</th>
                                                        <th>Investor Name</th>
                                                        <th>Purchase Date</th>
                                                        <th>Seller</th>
                                                        <th>Purchase Price</th>
                                                        <th>Service Cost</th>
                                                        <th>Net Cost</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td @style('text-align: left') colspan="5"><b>Total:</b></td>
                                                        <td @style('text-align: right')><b id="total_purchase_price"></b></td>
                                                        <td @style('text-align: right')><b id="total_servicing_cost"></b></td>
                                                        <td @style('text-align: right')><b id="grand_total_cost"></b></td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
              
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        let isAssetTableInitialized = false;
        $(document).ready(function() {
            loadBikeTableData();
            loadSummeryData();
        });

        function loadBikeTableData() {
            var table = $('#dataTable').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: `{{ route('dashboard.bike-list') }}`,
                    type: 'GET',
                    data: function(d) {
                        let activeElement = $('.bikefilter.active-cust');
                        let filteron = 'mybikes';
                        if (activeElement.length > 0) filteron = activeElement.data('filteron');
                        d.filteron = filteron;
                    },
                    dataSrc: function(json) {
                        $('#grand_total_cost').html(formatNumber(json.summery_data.grand_total_cost));
                        $('#total_purchase_price').html(formatNumber(json.summery_data.total_purchase_price));
                        $('#total_servicing_cost').html(formatNumber(json.summery_data.total_servicing_cost));
                        return json.data;
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'bike_models.name',
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return `${row.model_name} <span class="badge" style="background-color: ${row.hex_code};color: black; text-shadow: 2px 0px 8px white;">${row.color_name}</span><br>Ch#${row.chassis_no}<br>Reg#${row.registration_no}`;
                        }
                    },
                    {
                        data: 'investor_name',
                        name: 'investors.name'
                    },
                    {
                        data: 'purchase_date',
                        name: 'bike_purchases.purchase_date'
                    },
                    {
                        data: 'seller_name',
                        name: 'sellers.name'
                    },
                    {
                        data: 'purchase_price',
                        name: 'bike_purchases.purchase_price'
                    },
                    {
                        data: 'servicing_cost',
                        name: 'bike_purchases.servicing_cost'
                    },
                    {
                        data: 'total_cost',
                        name: 'bike_purchases.total_cost'
                    },
                ],
                rowCallback: function(row, data, index) {
                    var pageInfo = table.page.info();
                    var serialNumber = pageInfo.start + index + 1;
                    $('td:eq(0)', row).html(serialNumber);
                },
                order: [],
                search: {
                    return: false
                }
            });
            $(document).on('click', '.bikefilter', function() {
                table.draw();
            });
        }

        $('.activeable').on('click', function() {
            $('.activeable').removeClass('active-cust');
            $(this).toggleClass('active-cust');
        });
        $('#reservation').daterangepicker();


        $('#reservation').on('change', function(e) {
            loadSummeryData();
        });

        function loadSummeryData() {
            if ($('#reservation').length) {
                let dateRange = $('#reservation').val().replace(/\//g, '_');
                const url = `{{ route('dashboard.summery-data', [':dateRange']) }}`.replace(':dateRange', dateRange);
                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'JSON',
                    success: function(res) {
                        let accessories = parseFloat(res.accessories || 0);
                        let spareparts = parseFloat(res.spareparts || 0);
                        let services = parseFloat(res.services || 0);
                        let expenses = parseFloat(res.expenses || 0);
                        let purchase = parseFloat(res.purchase || 0);
                        let total_service_and_item_sales = accessories + spareparts + services;

                        $('#accessoriesSale').html(formatNumber(accessories));
                        $('#sparePartsSale').html(formatNumber(spareparts));
                        $('#serviceSale').html(formatNumber(services));
                        $('#expenses').html(formatNumber(expenses));
                        $('#total_service_and_item_sales').html(formatNumber(total_service_and_item_sales));
                        $('#purchase').html(formatNumber(purchase));
                    }
                });
            }
        }
    </script>
@endsection
