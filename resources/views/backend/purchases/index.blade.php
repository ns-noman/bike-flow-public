@extends('layouts.admin.master')
@section('content')
<style>
    td:nth-child(5),
    td:nth-child(6),
    td:nth-child(7),
    td:nth-child(8),
    td:nth-child(9),
    {
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
                            <div class="card-header bg-primary p-1">
                                <h3 class="card-title">
                                    <a href="{{ route('purchases.create') }}"class="btn btn-light shadow rounded m-0"><i
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
                                                    <th>VoucharNo</th>
                                                    <th>Supplier|Mobile</th>
                                                    <th>Date</th>
                                                    <th>TotalPrice</th>
                                                    <th>Vat/Tax</th>
                                                    <th>Discount</th>
                                                    <th>Payable</th>
                                                    <th>Paid|Due</th>
                                                    <th>PaymentStatus</th>
                                                    <th>Note</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                             <tfoot>
                                                <tr>
                                                    <td colspan="7"><b>Total:</b></td>
                                                    <td @style('text-align: right;')><b id="totalPurchase"></b></td>
                                                    <td colspan="5"></td>
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
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Payment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="form-submit" action="{{ route('purchases.payment.store') }}" method="POST"
                            enctype="multipart/form-data">
                            <div class="modal-body">
                                @csrf()
                                <div class="row">
                                    <input type="hidden" name="purchase_id" id="purchase_id">
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <label>Payment Date *</label>
                                        <input value="{{ date('Y-m-d') }}" type="date" class="form-control" name="date" id="date" placeholder="0.00">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <label>Payment Methods *</label>
                                        <select class="form-control" name="account_id" id="account_id" required>
                                            <option selected value=''>Select Payment Methods</option>
                                            @foreach ($data['paymentMethods'] as $paymentMethod)
                                                <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] . ' (Bal: ' . $paymentMethod['balance'] }} &#2547;)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <label>Due Amount</label>
                                        <input readonly value="0.00" type="number" class="form-control" name="due_amount" id="due_amount" placeholder="0.00">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <label>Paid Amount</label>
                                        <input value="0.00" type="number" class="form-control" name="amount" id="amount" placeholder="0.00">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <label>Note</label>
                                        <input type="text" class="form-control" name="note" id="note" placeholder="Note">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="cancel" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button id="save_payment" type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function(){
            $(document).on('click','.pay-now', function(e) {
                $('#purchase_id').val($(this).attr('purchase-id'));
                $('#amount').val(parseFloat($(this).attr('due')).toFixed(2));
                $('#due_amount').val(parseFloat($(this).attr('due')).toFixed(2));
            });
            $('#form-submit').submit(function(e) {
                let paid_amount = parseFloat($('#amount').val());
                let due = parseFloat($('#due_amount').val());
                if(paid_amount>due){
                    e.preventDefault();
                    Swal.fire("Couldn't be pay more then payable!");
                }
            });


            var table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("purchases.list") }}',
                type: 'GET',  
                dataSrc: function (json) {
                    $('#totalPurchase').html(formatNumber(json.totalPurchase));
                    return json.data;
                }
                
            },
            columns: [
                        { data: null, orderable: false, searchable: false },
                        {
                            data: null, 
                            name: 'purchases.vouchar_no', 
                            orderable: true, 
                            searchable: true, 
                            render: function(data, type, row, meta) {
                                let view = `{{ route('purchases.vouchar', ":id") }}`.replace(':id', row.id);
                                return `<a href="${view}" class=""><b>${row.vouchar_no}</b></a>`;
                            }
                        },
                        {
                            data: null, 
                            name: 'suppliers.name', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                return `${row.supplier_name}<br>${row.supplier_contact}`;
                            }
                        },
                        { data: 'date', name: 'purchases.date'},
                        { data: 'total_price', name: 'purchases.total_price'},
                        { data: 'vat_tax', name: 'purchases.vat_tax'},
                        { data: 'discount', name: 'purchases.discount'},
                        { data: 'total_payable', name: 'purchases.total_payable'},
                        {
                            data: null, 
                            name: 'purchases.paid_amount', 
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                return `<div class="text-center"><span class="text-success fw-bold"><b>${row.paid_amount}</b></span><br><span class="text-danger fw-bold"><b>${row.total_payable - row.paid_amount}</b></span></div>`;
                            }
                        },
                        {
                            data: null, 
                            name: 'purchases.payment_status', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let color;
                                let text;
                                if(row.payment_status == '0'){
                                    color = 'warning';
                                    text = 'Unpaid';
                                }else if(row.payment_status == '1'){
                                    color = 'info';
                                    text = 'Paid';
                                }
                                return `<span class="badge badge-${color}">${text}</span>`;
                            }
                        },
                        { data: 'note', name: 'purchases.note'},
                        {
                            data: null, 
                            name: 'purchases.status', 
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
                                let edit = `{{ route('purchases.edit', ":id") }}`.replace(':id', row.id);
                                let print = `{{ route('purchases.vouchar.print', [":id", "print"]) }}`.replace(':id', row.id);
                                let view = `{{ route('purchases.vouchar', [":id"]) }}`.replace(':id', row.id);
                                let destroy = `{{ route('purchases.destroy', ":id") }}`.replace(':id', row.id);
                                return (` <div class="d-flex justify-content-center">
                                                <a href="${view}" class="btn btn-sm btn-warning ml-1">
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
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        More
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button due="${row.total_payable - row.paid_amount}" purchase-id="${row.id }" type="button" class="btn btn-success btn-sm pay-now dropdown-item"
                                                            data-toggle="modal" data-target="#exampleModal" data-whatever="@getbootstrap" 
                                                                ${(row.total_payable - row.paid_amount)==0? 'disabled' : null}
                                                            >Add Payments</button>
                                                            <a href="${print}" class="dropdown-item">Print</a>
                                                    </div>
                                                </div>
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
                        const url = `{{ route('purchases.approve', ":id") }}`.replace(':id', transaction_id);
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
    </script>
@endsection
