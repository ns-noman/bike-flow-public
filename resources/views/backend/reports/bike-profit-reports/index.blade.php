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
                                                    <th>Bike Details</th>
                                                    <th>Investor</th>
                                                    @if($investor_id==1)
                                                    <th>Total Profit</th>
                                                    @endif
                                                    <th>Profit Shared</th>
                                                    <th>Sale Date</th>
                                                    <th>WithdrawalHistory</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="{{ $investor_id==1 ? 4 : 3 }}"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b class="totalProfitShared"></b></td>
                                                    <td></td>
                                                    <td @style('text-align: right')><b class="totalProfitShared"></b></td>
                                                    <td></td>
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
                <div class="col-sm-12 col-md-${colmd}" ${rollType !=1 ? 'hidden' : null}>
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
            url: '{{ route("reports.bike-profit") }}',
            type: 'post',
            data: function(d) {
                const investor_id = $('#investor_id').val();
                const date = $('#date').val();
                d.date = date || "{{ date('Y-m') }}";
                d.investor_id = investor_id || "{{ $investor_id }}";
                d._token = $('meta[name="csrf-token"]').attr('content');
            },
            dataSrc: function(json) {
                let totalProfitShared = 0;
                if (json.data.length) {
                    for (let index = 0; index < json.data.length; index++) {
                        totalProfitShared += parseFloat(json.data[index].profit_share_amount || 0);
                        console.log(totalProfitShared);
                        
                    }
                }
                document.getElementsByClassName('totalProfitShared')[0].innerHTML = formatNumber(totalProfitShared);
                document.getElementsByClassName('totalProfitShared')[1].innerHTML = formatNumber(totalProfitShared);

                return json.data;
            }
        },
        columns: 
        
        [
                        { data: null, orderable: false, searchable: false },
                        {
                            data: null, 
                            name: 'bike_models.name', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                return `${row.model_name} <span class="badge" style="background-color: ${row.hex_code};color: black; text-shadow: 2px 0px 8px white;">${row.color_name}</span><br>Ch#${row.chassis_no}<br>Reg#${row.registration_no}`;
                            }
                        },
                        { data: 'investor_name', name: 'investors.name'},
                        @if($investor_id==1)
                            { data: 'profit_amount', name: 'bike_profits.profit_amount'},
                        @endif

                        { data: 'profit_share_amount', name: 'bike_profits.profit_share_amount'},
                        { data: 'profit_share_last_date', name: 'bike_profits.profit_share_last_date'},
                        { 
                            data: null, 
                            name: '', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let tr = ``;
                                let table = `<h6 style="text-align: center;">Pending</h6>`;
                                if (row.paymenthistory.length) {
                                    row.paymenthistory.forEach(paymenthistory => {
                                        tr+=`<tr>
                                                <td style="vertical-align: middle;width: auto;">${paymenthistory.date ?? ''}</td>
                                                <td style="vertical-align: middle;width: auto;">${paymenthistory.amount ?? ''}</td>
                                            </tr>`;
                                    });
                                        table = `
                                            <table class="table table-sm table-striped table-info table-center rounded m-0" style="vertical-align: middle;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: auto;">Date</th>
                                                        <th class="text-center" style="width: auto;">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${tr}
                                                </tbody>
                                            </table>
                                        `;
                                }
                                return table;
                            }
                        },
                        { 
                            data: null, 
                            name: 'bike_profits.status', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let color;
                                let text;
                                let eventClass = '';
                                if(row.status == '0'){
                                    color = 'success';
                                    text = 'Open';
                                }else if(row.status == '1'){
                                    color = 'danger';
                                    text = 'Closed';
                                }else if(row.status == '-1'){
                                    color = 'warning';
                                    text = 'Not Shared';
                                }
                                return `<button type="button" class="btn btn-sm btn-${color}">${text}</button>`;
                            }
                        },
                    ]
                ,
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