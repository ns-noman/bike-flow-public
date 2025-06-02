@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Expense Report</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    
                                    <div class="form-group col-sm-12 col-md-10 col-lg-10">
                                        <div class="row">
                                            <div class="form-group col-sm col-md col-lg">
                                                <label>Expense Category</label>
                                                <select name="expense_cat_id" id="expense_cat_id" class="form-control">
                                                    <option report_type="" value="0">All Category</option>
                                                    <option report_type="Category wise expense report" value="-1">Group By Category</option>
                                                    @foreach($data['expense_categories'] as $expense_category)
                                                        <option report_type="{{ $expense_category->cat_name }} expense report" value="{{ $expense_category->id }}">{{ $expense_category->cat_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm col-md col-lg">
                                                <label>Expense Head</label>
                                                <select name="expense_head_id" id="expense_head_id" class="form-control">
                                                    <option value="0">All Head</option>
                                                    <option report_type="Head wise expense report" value="-1">Group By Head</option>
                                                    @foreach($data['expense_heads'] as $expense_head)
                                                        <option report_type="{{ $expense_head->title }} expense report" value="{{ $expense_head->id }}">{{ $expense_head->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm col-md col-lg">
                                                <label>Month</label>
                                                <input name="date" id="date" type="month" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-2 col-lg-2">
                                        <label>&nbsp;</label>
                                        <button name="print" id="print" type="button" class="form-control btn btn-primary">Print</button>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12" id="printable">
                                        <div id="print_header" hidden>
                                            <div class="row justify-content-center">
                                                <div class="col-12 text-center">
                                                    <h1>Expense Report</h1>
                                                </div>
                                                <div class="col-12">
                                                    <h4>Description: <span id="report_type"></span><span id="report_date"></span>.</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bootstrap-data-table-panel text-center">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-centre">
                                                    <thead id="thead">
                                                    </thead>
                                                    <tbody id="tbody">
                                                    </tbody>
                                                    <tfoot id="tfoot">
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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
        $(document).ready(function(){
            $('#print').click(function() {
                let expense_cat_id = $('#expense_cat_id').val();
                let expense_head_id = $('#expense_head_id').val();
                let date = $('#date').val();
                    date = date ?  ' for the month of' + nsMMYYYY(date): " of all time";
                let report_type = 'Expense Report';

                if(expense_cat_id!=0) report_type = $('#expense_cat_id option:selected').attr('report_type');
                if(expense_head_id!=0) report_type = $('#expense_head_id option:selected').attr('report_type');

                $('#report_type').html(report_type);
                $('#report_date').html(date);

                // Prepare for printing by expanding the table and showing hidden elements
                let originalOverflow = $('.table-responsive').css('overflow');
                let originalMaxHeight = $('.table-responsive').css('max-height');
                
                $('.table-responsive').css({
                    'overflow': 'visible',
                    'max-height': 'none'
                });

                $('#print_header').prop('hidden', false);

                var printContents = $('#printable').html();
                $('#print_header').prop('hidden', true);
                var originalContents = $('body').html();

                $('body').html(printContents);
                window.print();
                $('body').html(originalContents);

                // Restore the original state
                $('.table-responsive').css({
                    'overflow': originalOverflow,
                    'max-height': originalMaxHeight
                });
            });
        });


        $(document).ready(function(){
            initialize();
            $('#expense_cat_id, #expense_head_id, #date').on('change', function (event) {
                updateData(event.target);
            });
        });

        function initialize() {
            const defaultData = {expense_cat_id: 0,expense_head_id: 0,date: "{{ date('Y-m') }}"};
            const data = nsGetItem("expenseSearchBy") || defaultData;
            $('#expense_cat_id').val(data.expense_cat_id);
            $('#expense_head_id').val(data.expense_head_id);
            $('#date').val(data.date);
            nsSetItem("expenseSearchBy",data);
            getData(data);
        }
        function updateData(triggeredElement) {
            let data = getFormData();
            if (triggeredElement.id === 'expense_cat_id' && data.expense_cat_id != 0) {
                $('#expense_head_id').val(0);
                data.expense_head_id = 0;
            } else if (triggeredElement.id === 'expense_head_id' && data.expense_head_id != 0) {
                $('#expense_cat_id').val(0);
                data.expense_cat_id = 0;
            }
            nsSetItem("expenseSearchBy",data);
            getData(data);
        }

        async function getData(data){
            res = await nsAjaxPost("{{ route('expenses.reports') }}",data);
            
            const isGroupBy = data.expense_cat_id == '-1' || data.expense_head_id == '-1';
            const catView = (data.expense_cat_id == '0' && data.expense_head_id != '-1') || data.expense_cat_id == '-1';
            const headView = (data.expense_head_id == '0' && data.expense_cat_id != '-1') || data.expense_head_id == '-1';
            const filterByAll = catView && headView;
            const colspan = isGroupBy ? 2 : filterByAll? 6 : 5;
            let tbody = '';
            let thead = '';
            let total_expense = 0;

            // Build table header
            thead += `<tr style="text-align: center;">`;
            thead += `<th width="5%">SN</th>`;
            if (catView) thead += `<th>Expense Category</th>`;
            if (headView) thead += `<th>Expense Head</th>`;
            if (!isGroupBy) {
                thead += `<th>Date</th>`;
                thead += `<th>Amount</th>`;
                thead += `<th>Quantity</th>`;
            }
            thead += `<th>Grand Total: </th>`;
            thead += `</tr>`;

            // Build table body
            res.expenses.forEach((item, index) => {
                tbody += `<tr>`;
                tbody += `<td>${index + 1}</td>`;
                if (catView) tbody += `<td style="text-align: center;">${item.cat_name}</td>`;
                if (headView) tbody += `<td style="text-align: center;">${item.title}</td>`;
                if (!isGroupBy) {
                    tbody += `<td style="text-align: center;">${item.date}</td>`;
                    tbody += `<td style="text-align: right;">${res.currency_symbol} ${nsFormatNumber(item.amount)}</td>`;
                    tbody += `<td>${item.quantity}</td>`;
                }
                tbody += `<td style="text-align: right;">${res.currency_symbol} ${nsFormatNumber(item.sub_total)}</td>`;
                tbody += `</tr>`;
                total_expense += item.sub_total;
            });

            // Add total row
            tbody += `<tr>`;
            tbody += `<th style="text-align: left;" colspan="${colspan}">Total: </th>`;
            tbody += `<th style="text-align: right;">${res.currency_symbol} ${nsFormatNumber(total_expense)}</th>`;
            tbody += `</tr>`;

            // Render table header and body
            $('#thead').html(thead);
            $('#tbody').html(tbody);
        }
        function getFormData() {
            return {
                expense_cat_id: $('#expense_cat_id').val(),
                expense_head_id: $('#expense_head_id').val(),
                date: $('#date').val()
            }
        }
    </script>
@endsection