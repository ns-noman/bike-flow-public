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
                                        <table id="dataTable" class="table table-sm table-striped table-bordered table-centre text-center">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Date</th>
                                                    <th>Service Name</th>
                                                    <th>Quantity </th>
                                                    <th>Rate</th>
                                                    <th>Amount</th>
                                                    <th>Ref Invoice #</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td @style('text-align: left') colspan="5"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b id="total_service_cost"></b></td>
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
        var table = $('#dataTable').DataTable({
            initComplete: function () {
                const filterContainer = $('.dataTables_filter').parent();
                const colmd = 2;
                filterContainer.before(`
                    <div class="col-sm-12 col-md-${7}">
                        <div class="dataTables_filter" style="display: flex; align-items: center; justify-content: center;">
                            <label style="font-weight: normal; white-space: nowrap; display: flex; align-items: center;margin-bottom: .5rem;">
                                Bikes:
                                    <select name="bike_purchase_id" id="bike_purchase_id" class="form-control select2 form-control-sm filter">
                                        <option value="" selected disabled>Select Bikes</option>
                                        @foreach ($data['bikes'] as $bike)
                                            <option value="{{ $bike['bike_purchase_id'] }}">
                                                {{ $bike['model_name'] }} {{ $bike['color_name'] }} <br>
                                                 Ch#{{ $bike['chassis_no'] }} <br>
                                                 Reg#{{ $bike['registration_no'] }} <br>
                                                PurchaseDate:{{ $bike['purchase_date'] }}
                                            </option>
                                        @endforeach 
                                    </select>
                            </label>
                        </div>
                    </div>
                `);
                
                const dataTable_length = $('#dataTable_length').parent().removeClass('col-md-6').addClass(`col-md-${2}`);
                const dataTable_filter = $('#dataTable_filter').parent().removeClass('col-md-6').addClass(`col-md-${2}`);
                $('.select2').select2();
            },
            processing: false,
            serverSide: true,
            ajax: {
                url: '{{ route("reports.bike-service-reports") }}',
                type: 'POST', 
                data: function(d) { 
                    d.bike_purchase_id = $('#bike_purchase_id').val() || 'no_id';
                    d._token = $('meta[name="csrf-token"]').attr('content');
                },
                dataSrc: function(json) {
                    let total_service_cost = parseFloat(json.total_service_cost || 0);
                    $('#total_service_cost').html(formatNumber(total_service_cost));
                    return json.data;
                }
            },
            
            columns: [
                { data: null, orderable: false, searchable: false },
                { data: 'date', name: 'bike_service_records.date'},
                { data: 'bike_service_name', name: 'bike_service_records.bike_service_name'},
                { data: 'quantity', name: 'bike_service_record_details.quantity'},
                { 
                    data: null, 
                    name: null, 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                       return `${formatNumber(parseFloat(row.price || 0))}`;
                    }
                },
                { 
                    data: null, 
                    name: null, 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                       return `${formatNumber(parseFloat(row.quantity || 0) * parseFloat(row.price || 0))}`;
                    }
                },
                {
                    data: null, 
                    name: 'bike_service_records.invoice_no', 
                    orderable: true, 
                    searchable: true, 
                    render: function(data, type, row, meta) {
                        let view = `{{ route('bike-service-records.view', ":id") }}`.replace(':id', row.bike_service_records_id);
                        return `<a target="_blank" href="${view}" class=""><b>${row.invoice_no}</b></a>`;
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
        let bike_purchase_id = $('#bike_purchase_id').val() || '';
        if(bike_purchase_id){
            window.open(`?&print=true&bike_purchase_id=${bike_purchase_id}`, '_blank');
        }
    }
</script>
@endsection