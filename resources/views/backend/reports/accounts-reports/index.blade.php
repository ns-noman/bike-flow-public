@extends('layouts.admin.master')
@section('content')
    <style>
        td:nth-child(5){
            text-align: right !important;
        }
    </style>
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
                                                    <th>Payment Method</th>
                                                    <th>Account No</th>
                                                    <th>Account Holder</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b id="allAccountsBalance"></b></td>
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
        $(document).on('change', '#account_id', function () {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            loadAssetData();
        } else {
            $('#dataTable').DataTable().draw();
        }
    });
    });
    function loadAssetData() {
        var table = $('#dataTable').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route("reports.accounts-reports") }}',
            type: 'POST',
            data: function(d) {
                d._token = $('meta[name="csrf-token"]').attr('content');
            },
            dataSrc: function(json) {
                document.getElementById('allAccountsBalance').innerHTML = formatNumber(json.totalAllAccountBalance);
                return json.data;
            }
        },
        columns: [
                    { data: null, orderable: false, searchable: false },
                    { data: 'payment_method', name: 'payment_methods.name'},
                    { data: 'account_no', name: 'accounts.account_no'},
                    { data: 'holder_name', name: 'accounts.holder_name'},
                    {
                        data: null, 
                        name: 'accounts.balance', 
                        orderable: false, 
                        searchable: false, 
                        render: function(data, type, row, meta) {
                            return formatNumber(row.balance);
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
        let account_id = $('#account_id option:selected').val() || '';
        window.open(`?&print=true`, '_blank');
    }
</script>
@endsection