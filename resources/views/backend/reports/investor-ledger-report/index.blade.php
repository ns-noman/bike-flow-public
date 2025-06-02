@extends('layouts.admin.master')
@section('content')
    <style>
        td:nth-child(5),
        td:nth-child(6),
        td:nth-child(7) {
            text-align: right !important;
        }

    </style>
    @php
        $roleType = Auth::guard('admin')->user()->type;
        $investor_id = Auth::guard('admin')->user()->investor_id;
    @endphp
    <div class="content-wrapper">
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
                                <div class="bootstrap-data-table-panel">
                                    <div class="table-responsive">
                                        <table id="dataTable" class="table table-sm table-striped table-bordered table-centre">
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
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4"><b>Total:</b></td>
                                                    <td @style('text-align: right;')><b id="total_deposit"></b></td>
                                                    <td @style('text-align: right;')><b id="total_withdrawal"></b></td>
                                                    <td @style('text-align: right;')><b id="current_balance"></b></td>
                                                </tr>
                                            </tfoot>
                                        </table>
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
        $(document).on('change', '#investor_id', function () {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            loadAssetData();
        } else {
            $('#dataTable').DataTable().draw();
        }
    });
    });
    function loadAssetData() {
        var table = $('#dataTable').DataTable({initComplete: function () {
            const filterContainer = $('.dataTables_filter').parent();
            let rollType = "{{ $roleType }}";
            let colmd = rollType !=1 ? 4 : 3;
            filterContainer.before(`
                <div class="col-sm-12 col-md-${colmd}"${rollType !=1 ? 'hidden' : null}>
                    <div class="dataTables_filter" style="display: flex; align-items: center; justify-content: center;">
                        <label style="font-weight: normal; white-space: nowrap; display: flex; align-items: center;margin-bottom: .5rem;">
                            Investor:
                            <select data-column="1" class="form-control form-control-sm filter select2" id="investor_id" name="investor_id" style="margin-left: 10px;">
                                    @foreach ($data['investors'] as $investors)
                                    <option @selected(Auth::guard('admin')->user()->investor_id == $investors['id']) value="{{ $investors['id'] }}">{{ $investors['name'] }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
            `);
            $('#dataTable_length').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);
            $('#dataTable_filter').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);

            filterContainer.before(`
                <div class="col-sm-12 col-md-${colmd}">
                    <div id="dataTable_filter" class="dataTables_filter" style="text-align: center;">
                        <label>
                            Date: <input id="date" value="{{ date('Y-m') }}" type="month" class="form-control form-control-sm filter">
                        </label>
                    </div>
                </div>
            `);
        },
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route("reports.investor-ledger") }}',
            type: 'post',
            data: function(d) {
                const investor_id = $('#investor_id').val();
                const date = $('#date').val();
                d.date = date || "{{ date('Y-m') }}";
                d.investor_id = investor_id || "{{ $investor_id }}";
                d._token = $('meta[name="csrf-token"]').attr('content');
            },
            dataSrc: function(json) {
                if (json.data.length) {
                    let brought_forword = parseFloat(json.data[0].current_balance || 0) + parseFloat(json.data[0].debit_amount || 0) - parseFloat(json.data[0].credit_amount || 0);
                    const bfRow = {
                        transaction_date: '',
                        particular: 'B/F',
                        reference_number: '',
                        credit_amount: '',
                        debit_amount: '',
                        current_balance: brought_forword,
                    };
                    json.data.unshift(bfRow);

                }
                let total_deposit = parseFloat(json.investorLedgerSummery.total_deposit || 0);
                let total_withdrawal = parseFloat(json.investorLedgerSummery.total_withdrawal || 0);

                $('#total_deposit').html(formatNumber(total_deposit));
                $('#total_withdrawal').html(formatNumber(total_withdrawal));
                $('#current_balance').html(formatNumber(total_deposit - total_withdrawal));
                return json.data;
            }
        },
        columns: [
                    { data: null, orderable: false, searchable: false },
                    { data: 'transaction_date', name: 'investor_ledgers.transaction_date', orderable: false},
                    { data: 'particular', name: 'investor_ledgers.particular', orderable: false},
                    { data: 'reference_number', name: 'investor_ledgers.reference_number', orderable: false},
                    {
                        data: null, 
                        name: 'investor_ledgers.credit_amount', 
                        orderable: false, 
                        searchable: false, 
                        render: function(data, type, row, meta) {
                            return row.credit_amount > 0 ? formatNumber(row.credit_amount) : '';
                        }
                    },
                    {
                        data: null, 
                        name: 'investor_ledgers.debit_amount', 
                        orderable: false, 
                        searchable: false, 
                        render: function(data, type, row, meta) {
                            return row.debit_amount > 0 ? formatNumber(row.debit_amount) : '';
                        }
                    },
                    {
                        data: null, 
                        name: 'investor_ledgers.current_balance', 
                        orderable: false, 
                        searchable: false, 
                        render: function(data, type, row, meta) {
                            return formatNumber(row.current_balance);
                        }
                    },
                ],
            rowCallback: function(row, data, index) {
                var pageInfo = table.page.info();
                var serialNumber = pageInfo.start + index + 1;
                $('td:eq(0)', row).html(serialNumber);
            },
            order: [],
            search: {return: false}
        });
        $(document).on('change','.filter',function() {
            table.draw();
        });
    }
    function print() {
        let date = $('#date').val() || '';
        let investor_id = $('#investor_id option:selected').val() || '';
        window.open(`?&print=true&date=${date}&investor_id=${investor_id}`, '_blank');
    }
</script>
@endsection