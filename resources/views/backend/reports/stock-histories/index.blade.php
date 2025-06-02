@extends('layouts.admin.master')
@section('content')
<style>
    td:nth-child(4),
    td:nth-child(5) ,
    td:nth-child(6) {
        text-align: right !important;
    }
    th{
        text-align: center;
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
                                                    <th>Date</th>
                                                    <th>Particular</th>
                                                    <th>InQty</th>
                                                    <th>OutQty</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b id="totalInQty"></b></td>
                                                    <td @style('text-align: right')><b id="totalOutQty"></b></td>
                                                    <td @style('text-align: right')><b id="balanceQty"></b></td>
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
        var table = $('#dataTable').DataTable({
            initComplete: function () {
                const filterContainer = $('.dataTables_filter').parent();
                const colmd = 3;
                const dataTable_length = $('#dataTable_length').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);
                const dataTable_filter = $('#dataTable_filter').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);
                filterContainer.before(`
                    <div class="col-sm-12 col-md-${colmd}">
                        <div class="dataTables_filter" style="display: flex; align-items: center; justify-content: center;">
                            <label style="font-weight: normal; white-space: nowrap; display: flex; align-items: center;margin-bottom: .5rem;">
                                Items:
                                    <select name="item_id" id="item_id" class="form-control select2 form-control-sm filter">
                                        @foreach ($data['items'] as $key => $item)
                                            <option value="{{ $item->id }}" item-name="{{ $item->name }}"
                                                item-price="{{ $item->purchase_price }}"
                                                unit_name="{{ $item->unit->title }}"
                                                > {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                            </label>
                        </div>
                    </div>
                `);
                filterContainer.before(`
                    <div class="col-sm-12 col-md-${colmd}">
                        <div id="dataTable_filter" class="dataTables_filter" style="text-align: center;">
                            <label>
                                Date: <input id="date" value="{{ date('Y-m') }}" type="month" class="form-control form-control-sm filter">
                            </label>
                        </div>
                    </div>
                `);
                $('.select2').select2();
            },
            processing: false,
            serverSide: true,
            ajax: {
                url: '{{ route("reports.stock-histories") }}',
                type: 'POST', 
                data: function(d) {
                    let item_id = $('#item_id').val();
                    if (!item_id) {
                        item_id = "{{ $data['items'][0]['id'] ?? null }}"
                    }
                    d.item_id = item_id;
                    d.date = $('#date').val();
                    d._token = $('meta[name="csrf-token"]').attr('content');
                },
                dataSrc: function(json) {
                    let totalStockValue = 0;
                    let inQty = 0;
                    let outQty = 0;
                    let balance = 0;
                    if (json.data.length) {
                        let brought_forword = parseFloat(json.data[0].current_stock - json.data[0].stock_in_qty + json.data[0].stock_out_qty);
                        const bfRow = {
                            date: '',
                            particular: 'B/F',
                            stock_in_qty: brought_forword,
                            stock_out_qty: '',
                            current_stock: brought_forword,
                            unit_name: json.data[0].unit_name,
                        };
                        json.data.unshift(bfRow);
                    
                        let stock_in_qty = parseFloat(json.summery.stock_in_qty || 0);
                        let stock_out_qty = parseFloat(json.summery.stock_out_qty || 0);
                        $('#totalInQty').html("+" + stock_in_qty + ' ' + json.data[0].unit_name);
                        $('#totalOutQty').html("-" + stock_out_qty + ' ' + json.data[0].unit_name);
                        $('#balanceQty').html((stock_in_qty - stock_out_qty) + ' ' + json.data[0].unit_name);
                    }else{
                        $('#totalInQty').html('');
                        $('#totalOutQty').html('');
                        $('#balanceQty').html('');
                    }
                    return json.data;
                }
            },
            columns: [
                { data: null, orderable: false, searchable: false },
                { data: 'date', name: 'stock_histories.date'},
                { data: 'particular', name: 'stock_histories.particular'},
                { 
                    data: 'stock_in_qty', 
                    name: 'stock_histories.stock_in_qty', 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                        if (row.stock_in_qty) {
                            return `+${row.stock_in_qty} ${row.unit_name}`;
                        }
                        return '';
                    }
                },
                { 
                    data: 'stock_out_qty', 
                    name: 'stock_histories.stock_out_qty', 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                        if (row.stock_out_qty) {
                            return `-${row.stock_out_qty} ${row.unit_name}`;
                        }
                        return '';
                    }
                },
                { 
                    data: 'current_stock', 
                    name: 'stock_histories.current_stock', 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                        return `${row.current_stock} ${row.unit_name}`;
                    }
                },
            ],
            rowCallback: function(row, data, index) {
                var pageInfo = table.page.info();
                var serialNumber = pageInfo.start + index + 1;
                $('td:eq(0)', row).html(serialNumber);
            },
            order: [],
            search: { return: false }
        });

        $(document).on('change','.filter',function() {
            table.draw();
        });
    });

    function print() {
        let item_id = $('#item_id').val() || '';
        let date = $('#date').val() || '';
        window.open(`?&print=true&item_id=${item_id}&date=${date}`, '_blank');
    }
</script>
@endsection