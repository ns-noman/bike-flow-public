@extends('layouts.admin.master')
@section('content')
<style>
    .swal2-input[type="date"] {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
    }
</style>
<div class="content-wrapper">
    @include('layouts.admin.content-header')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <section class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-primary p-1">
                            <h3 class="card-title">
                                <a href="{{ route('bike-sales.create') }}"class="btn btn-light shadow rounded m-0"><i
                                        class="fas fa-plus"></i>
                                    <span>Add New</span></i></a>
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
                                                <th>Bike Info</th>
                                                <th>Buyer Name</th>
                                                <th>Sale Price</th>
                                                <th>Payment Method</th>
                                                <th>Reference No</th>
                                                <th>NID</th>
                                                <th>Reg Card</th>
                                                <th>Photo</th>
                                                <th>Deed</th>
                                                <th>TaxToken</th>
                                                <th>Note</th>
                                                <th>Created By</th>
                                                <th>Name Transfer Status/Date</th>
                                                <th>Repurchase Status</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>SN</th>
                                                <th>Date</th>
                                                <th>Bike Info</th>
                                                <th>Buyer Name</th>
                                                <th>Sale Price</th>
                                                <th>Payment Method</th>
                                                <th>Reference No</th>
                                                <th>NID</th>
                                                <th>Reg Card</th>
                                                <th>Photo</th>
                                                <th>Deed</th>
                                                <th>Note</th>
                                                <th>Created By</th>
                                                <th>Name Transfer Status</th>
                                                <th>Repurchase Status</th>
                                                <th>Status</th>
                                                <th>Action</th>
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
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("bike-sales.list") }}',
                type: 'GET',
            },
            columns: [
                        { data: null, orderable: false, searchable: false },
                        { data: 'sale_date', name: 'bike_sales.sale_date'},
                        {
                            data: null, 
                            name: 'bike_models.name', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                return `${row.model_name} <span class="badge" style="background-color: ${row.hex_code};color: black; text-shadow: 2px 0px 8px white;">${row.color_name}</span><br>Ch#${row.chassis_no}<br>Reg#${row.registration_no}`;
                            }
                        },
                        { data: 'buyer_name', name: 'buyers.name'},
                        { data: 'sale_price', name: 'bike_sales.sale_price'},
                        { data: 'payment_method', name: 'payment_methods.name'},
                        { data: 'reference_number', name: 'bike_sales.reference_number'},
                        {
                            data: null, 
                            name: 'bike_sales.doc_nid', 
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                if (row.doc_nid) {
                                    let src = `{{ asset('public/uploads/bike-sales/:image') }}`.replace(':image', row.doc_nid);
                                    return `<img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" src="${src}" onclick="downloadImage('${src}')">`;
                                }else{
                                    return '';
                                }
                            }
                        },
                        {
                            data: null, 
                            name: 'bike_sales.doc_reg_card', 
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                if (row.doc_reg_card) {
                                    let src = `{{ asset('public/uploads/bike-sales/:image') }}`.replace(':image', row.doc_reg_card);
                                    return `<img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" src="${src}" onclick="downloadImage('${src}')">`;
                                }else{
                                    return '';
                                }
                            }
                        },
                        {
                            data: null, 
                            name: 'bike_sales.doc_image', 
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                if (row.doc_image) {
                                    let src = `{{ asset('public/uploads/bike-sales/:image') }}`.replace(':image', row.doc_image);
                                    return `<img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" src="${src}" onclick="downloadImage('${src}')">`;
                                }else{
                                    return '';
                                }
                            }
                        },
                        {
                            data: null, 
                            name: 'bike_sales.doc_deed', 
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                if (row.doc_deed) {
                                    let src = `{{ asset('public/uploads/bike-sales/:image') }}`.replace(':image', row.doc_deed);
                                    return `<img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" src="${src}" onclick="downloadImage('${src}')">`;
                                }else{
                                    return '';
                                }
                            }
                        },
                        {
                            data: null, 
                            name: 'bike_sales.doc_tax_token', 
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                if (row.doc_tax_token) {
                                    let src = `{{ asset('public/uploads/bike-sales/:image') }}`.replace(':image', row.doc_tax_token);
                                    return `<img style="height: 75px!important;width: 75px!important;cursor: pointer;" class="rounded mx-auto d-block" src="${src}" onclick="downloadImage('${src}')">`;
                                }else{
                                    return '';
                                }
                            }
                        },
                        { data: 'note', name: 'bike_sales.note'},
                        { data: 'creator_name', name: 'admins.name'},
                        {
                            data: null, 
                            name: 'bike_sales.is_name_transfered', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let color;
                                let text;
                                let eventClass = '';
                                if(row.is_name_transfered == '0'){
                                    color = 'danger';
                                    text = 'Transfer';
                                    eventClass = 'nametransfer';
                                }else if(row.is_name_transfered == '1'){
                                    color = 'primary';
                                    text = row.name_transfer_date;
                                }
                                return `<button transaction_id=${row.id} type="button" class="btn btn-sm btn-${color} ${eventClass}">${text}</button>`;
                            }
                        },
                        {
                            data: null, 
                            name: 'bike_sales.is_repurchased', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let repurchase = `{{ route('bike-purchases.repurchase', [":id", ":sale_id"]) }}`.replace(':id', 0).replace(':sale_id', row.id);

                                let color;
                                let text;
                                let eventClass = '';
                                if(row.is_repurchased == '0'){
                                    color = 'warning';
                                    text = 'Repurchase';
                                }else if(row.is_repurchased == '1'){
                                    color = 'info';
                                    text = 'Repurchased';
                                }
                                return `<a href="${row.is_repurchased == '0' ? repurchase : 'javascript:void(0)'}" class="btn btn-sm btn-${color}">${text}</a>`;
                            }
                        },
                        {
                            data: null, 
                            name: 'bike_sales.status', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let color;
                                let text;
                                let eventClass = '';
                                if(row.status == '0'){
                                    color = 'danger';
                                    text = 'Pending';
                                    eventClass = 'event';
                                }else if(row.status == '1'){
                                    color = 'success';
                                    text = 'Approved';
                                }
                                return `<button transaction_id=${row.id} type="button" class="btn btn-sm btn-${color} ${eventClass}">${text}</button>`;
                            }
                        },
                        { 
                            data: null,
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let view = `{{ route('bike-sales.invoice', ":id") }}`.replace(':id', row.id);
                                let print = `{{ route('bike-sales.invoice.print', [":id","print"]) }}`.replace(':id', row.id);
                                let edit = `{{ route('bike-sales.edit', ":id") }}`.replace(':id', row.id);
                                let destroy = `{{ route('bike-sales.destroy', ":id") }}`.replace(':id', row.id);
                                return (` <div class="d-flex justify-content-center">
                                                <a href="${print}" class="btn btn-sm btn-dark">
                                                    <i class="fa-solid fa-print"></i>
                                                </a>
                                                <a href="${view}" class="btn btn-sm btn-warning">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="${edit}" class="btn btn-sm btn-info ${row.status == '1' ? 'disabled' : null}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form class="delete" action="${destroy}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" ${row.status == '1' ? "disabled" : null}>
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        `);
                            }
                        }
                    ],
                rowCallback: function(row, data, index) {
                    var pageInfo = table.page.info();
                    var serialNumber = pageInfo.start + index + 1;
                    $('td:eq(0)', row).html(serialNumber);
                },
                order: [],
                search: {return: false}
            });

            $(document).on('click', '.delete button', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                let tr = $(this).closest('tr');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then(async (result) => {
                    if (result.isConfirmed){
                        nsAjaxPost(form.attr('action'), form.serialize())
                        .then(res => {
                            table.draw();
                            message(res);
                        })
                        .catch(err => {
                            message(err);
                        });
                    }
                });
            });

        

            $(document).on('click', '.event', function(e) {
                e.preventDefault();
                let transaction_id = $(this).attr('transaction_id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#198754",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Approve",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = `{{ route('bike-sales.approve', ":id") }}`.replace(':id', transaction_id);
                        $.ajax({
                            url: url,
                            method: 'GET',
                            dataType: 'JSON',
                            success: function(res) {
                                message(res);
                                table.draw();
                            },
                            error: function(xhr, status, error) {
                                message(xhr.responseJSON);
                            }
                        });
                    }
                });
            });
            $(document).on('click', '.nametransfer', function(e) {
                e.preventDefault();
                let transaction_id = $(this).attr('transaction_id');
                Swal.fire({
                    title: "Are you sure?",
                    input: 'date',
                    inputLabel: 'Name Transfer Date',
                    inputPlaceholder: 'Type your name here...',
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#198754",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Transfer",
                    cancelButtonText: "Cancel",
                    preConfirm: (value) => {
                            if (!value) {
                                Swal.showValidationMessage('Please Insert Name Transfer Date!')
                            }
                            return value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let date = result.value;
                            const url = `{{ route('bike-sales.name-transfers', [":id", ":date"]) }}`.replace(':id', transaction_id).replace(':date', date);
                            $.ajax({
                                url: url,
                                method: 'GET',
                                dataType: 'JSON',
                                success: function(res) {
                                    message(res);
                                    table.draw();
                                },
                                error: function(xhr, status, error) {
                                    message(xhr.responseJSON);
                                }
                            });
                        }
                    });

            });
        });
            

        function downloadImage(url){
            const link = document.createElement('a');
            link.href = url;
            link.download = url.split('/').pop();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection