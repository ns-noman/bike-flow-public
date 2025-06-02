@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        <style>
            td{
                text-align: right;
                padding-left: 20px!important;
            }
        </style>
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-info p-1 d-flex justify-content-end justify-align-center">
                                <h3 class="card-title">
                                    <a href="javascript:void(0)"class="btn btn-dark shadow rounded" onclick="print()"><i
                                            class="fas fa-print"></i>
                                        <span>Print</span></i></a>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-6">
                                        <div style="text-align: center;">
                                            <label>
                                                Date: <input id="date" value="{{ date('Y-m') }}" type="month" class="form-control form-control-sm filter">
                                            </label>
                                        </div>
                                        <div class="bootstrap-data-table-panel">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped table-bordered table-centre">
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
                                                            <td id="bike_sale_profit"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Regular Sale Profit</th>
                                                            <td id="regular_sale"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Service Sales</th>
                                                            <td id="service_sales"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Total Profit:</th>
                                                            <td><b id="total_sale_revenew"></b></td>
                                                        </tr>
                                                        <tr id="expenses_title">
                                                            <th colspan="2" style="text-align: center;"><h5><b>Expenses</b></h5></th>
                                                        </tr>
                                                        <tr>
                                                            <th><b>Total Expenses : </b></th>
                                                            <td><b id="total_expenses"></b></td>
                                                        </tr>
                                                        <tr>
                                                            <th><b>Net Profit : </b></th>
                                                            <td><b id="net_profit"></b></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        loadAssetData();
        $('#date').on('change', function(){
            $('.expense').remove();
            loadAssetData();
        })
    });
    function loadAssetData() {
        $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: `{{ route('reports.profit-loss-statement') }}`,
                data: { date: $('#date').val()},
                type: 'POST',
                dataType: 'JSON',
                success: function(res){

                    $('#bike_sale_profit').html(formatNumber(res.bike_sales || 0));
                    $('#regular_sale').html(formatNumber(res.regular_sales || 0));
                    $('#service_sales').html(formatNumber(res.service_sales || 0));
                    $('#total_sale_revenew').html(formatNumber(res.total_incomes || 0));
                    let tr = ``;
                    res.expenses.forEach(item => {
                        tr +=
                        `<tr class="expense">
                            <th>${item.expense_name}</th>
                            <td>${formatNumber(item.expense_sub_total)}</td>
                        </tr>`
                    }); 
                    document.getElementById('expenses_title').insertAdjacentHTML('afterend',tr);
                    $('#total_expenses').html(formatNumber(res.total_expenses));
                    $('#net_profit').html(formatNumber(res.net_profit));
                }
            });
    }
    function print() {
        let date = $('#date').val() || '';
        window.open(`?&print=true&date=${date}`, '_blank');
    }
</script>
@endsection