@extends('layouts.admin.master')
@section('content')
    <style>
        td:nth-child(3),
        td:nth-child(4),
        td:nth-child(5),
        td:nth-child(6){
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
                                        <table id="dataTable" class="table table-sm table-striped table-bordered table-centre text-center">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Bike Info</th>
                                                    <th>Purchase Price</th>
                                                    <th>Repair Cost</th>
                                                    <th>Total Cost</th>
                                                    <th>Sold Price</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td @style('text-align: left') colspan="2"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b id="totalPurchasePrice"></b></td>
                                                    <td @style('text-align: right')><b id="totolServiceCost"></b></td>
                                                    <td @style('text-align: right')><b id="totalCost"></b></td>
                                                    <td @style('text-align: right')><b id="totalSalePrice"></b></td>
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
    });
    function loadAssetData() {
        var table = $('#dataTable').DataTable({initComplete: function () {
            const filterContainer = $('.dataTables_filter').parent();
            const colmd = 4; 
            $('#dataTable_length').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);
            $('#dataTable_filter').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);
            filterContainer.before(`
                <div class="col-sm-12 col-md-${colmd}">
                    <div class="dataTables_filter" style="display: flex; align-items: center; justify-content: center;">
                        <label style="font-weight: normal; white-space: nowrap; display: flex; align-items: center;margin-bottom: .5rem;">
                            Status:
                            <select data-column="1" class="form-control form-control-sm filter" id="selling_status" name="selling_status" style="margin-left: 10px;">
                                <option value="0">Available</option>
                                <option value="1">Sold</option>
                            </select>
                        </label>
                    </div>
                </div>
            `);
        },
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route("reports.my-bikes") }}',
            type: 'post',
            data: function(d) {
                d.selling_status = $('#selling_status').val() || 0;
                d._token = $('meta[name="csrf-token"]').attr('content');
            },
            dataSrc: function(json){
                 let totalPurchasePrice = 0;
                 let totolServiceCost = 0;
                 let totalCost = 0;
                 let totalSalePrice = 0;
                if (json.data.length) {
                    for (let index = 0; index < json.data.length; index++) {
                        totalPurchasePrice += parseFloat(json.data[index].purchase_price || 0);
                        totolServiceCost += parseFloat(json.data[index].servicing_cost || 0);
                        totalCost += parseFloat(json.data[index].total_cost || 0);
                        totalSalePrice += parseFloat(json.data[index].sale_price || 0);
                    }
                }
                document.getElementById('totalPurchasePrice').innerHTML = formatNumber(totalPurchasePrice);
                document.getElementById('totolServiceCost').innerHTML = formatNumber(totolServiceCost);
                document.getElementById('totalCost').innerHTML = formatNumber(totalCost);
                document.getElementById('totalSalePrice').innerHTML = formatNumber(totalSalePrice);

                return json.data;
            }
        },
        columns: [
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
                    { data: 'purchase_price', name: 'bike_purchases.purchase_price'},
                    { data: 'servicing_cost', name: 'bike_purchases.servicing_cost'},
                    { data: 'total_cost', name: 'bike_purchases.total_cost'},
                    { data: 'sale_price', name: 'bike_sales.sale_price'},
                    {
                        data: null, 
                        name: 'bike_purchases.selling_status', 
                        orderable: true, 
                        searchable: false, 
                        render: function(data, type, row, meta) {
                            return `${(row.selling_status == '0') ? 'Available' : 'Sold'}`;
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
        let selling_status = $('#selling_status').val() || 0;
        window.open(`?&print=true&selling_status=${selling_status}`, '_blank');
    }
</script>
@endsection