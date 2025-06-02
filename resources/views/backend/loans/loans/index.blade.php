@extends('layouts.admin.master')
@section('content')
    <style>
        td:nth-child(8),
        td:nth-child(9),
        td:nth-child(10),
        td:nth-child(11) {
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
                                    <a href="{{ route('loans.create') }}"class="btn btn-light shadow rounded m-0"><i
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
                                                    <th>LoanNo</th>
                                                    <th>LoanType</th>
                                                    <th>PartyName</th>
                                                    <th>LoanDate</th>
                                                    <th>DueDate</th>
                                                    <th>LastPaymentDate</th>
                                                    <th>LoanAmount</th>
                                                    <th>Paid</th>
                                                    <th>ReceivableAmount</th>
                                                    <th>PayableAmount</th>
                                                    <th>PayemntMethod</th>
                                                    <th>ReferenceNumber</th>
                                                    <th>Note</th>
                                                    <th>PaymentStatus</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="9"><b>Total:</b></td>
                                                    <td @style('text-align: right;')><b id="receivableAmount"></b></td>
                                                    <td @style('text-align: right;')><b id="payableAmount"></b></td>
                                                    <td colspan="6"></td>
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
                            <h5 class="modal-title" id="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="form-submit" action="{{ route('loans.payment.store') }}" method="POST"
                            enctype="multipart/form-data">
                            <div class="modal-body">
                                @csrf()
                                <div class="row">
                                    <input type="hidden" name="loan_id" id="loan_id">
                                    <input type="hidden" name="party_id" id="party_id">
                                    <input type="hidden" name="loan_type" id="loan_type">
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <label>Payment Date *</label>
                                        <input value="{{ date('Y-m-d') }}" type="date" class="form-control" name="date" id="date" placeholder="0.00">
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
                                        <label>Payment Methods *</label>
                                        <select class="form-control" name="account_id" id="account_id" required>
                                            <option selected value=''>Select Payment Methods</option>
                                            @foreach ($data['paymentMethods'] as $paymentMethod)
                                                <option account-bal="{{ $paymentMethod['balance'] }}" @selected(isset($data['item']) && $data['item']['account_id'] == $paymentMethod['id']) value="{{ $paymentMethod['id'] }}">{{ $paymentMethod['name'] .' : '. $paymentMethod['account_no'] . ' (Bal: ' . $paymentMethod['balance'] }} &#2547;)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <label>Reference Number</label>
                                        <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder="Reference Number">
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
                $('#party_id').val($(this).attr('party-id'));
                $('#loan_id').val($(this).attr('loan-id'));
                $('#loan_type').val($(this).attr('loan-type'));
                $('#amount').val(parseFloat($(this).attr('due')).toFixed(2));
                $('#due_amount').val(parseFloat($(this).attr('due')).toFixed(2));
                $('#modal-title').text($(this).attr('loan-type') == 1 ? 'Party Payment Form' : 'Party Collection Form');
            });
            $('#form-submit').submit(function(e) {
                let paid_amount = parseFloat($('#amount').val());
                let due = parseFloat($('#due_amount').val());
                if(paid_amount>due){
                    e.preventDefault();
                    Swal.fire("Couldn't be pay more then payable!");
                }
                let loan_type = $('#loan_type').val();
                let amount = $('#amount').val();
                if(parseFloat($('#amount').val())>0 && !$('#account_id option:selected').val()){
                    e.preventDefault();
                    Swal.fire("Please Select Payment Method");
                }
                if(loan_type==0 && amount > parseFloat($('#account_id option:selected').attr('account-bal'))){
                    e.preventDefault();
                    Swal.fire("Account Balance Exceed!");
                }
            });


            var table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("loans.list") }}',
                type: 'GET',
                dataSrc: function (json) {
                    $('#receivableAmount').html(formatNumber(json.receivableAmount));
                    $('#payableAmount').html(formatNumber(json.payableAmount));
                    return json.data;
                }
            },
            columns: [
                        { data: null, orderable: false, searchable: false },
                        {
                            data: null, 
                            name: 'party_loans.loan_no', 
                            orderable: true, 
                            searchable: true, 
                            render: function(data, type, row, meta) {
                                let view = `{{ route('loans.invoice', ":id") }}`.replace(':id', row.id);
                                return `<a href="${view}" class=""><b>${row.loan_no}</b></a>`;
                            }
                        },
                        {
                            data: null, 
                            name: 'party_loans.loan_type', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let color;
                                let text;
                                if(row.loan_type == '0'){
                                    color = 'success';
                                    text = 'Loan Given';
                                }else if(row.loan_type == '1'){
                                    color = 'danger';
                                    text = 'Loan Taken';
                                }
                                return `<span class="badge badge-${color}">${text}</span>`;
                            }
                        },
                        {
                            data: null, 
                            name: 'parties.name', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                return `${row.party_name}<br>${row.party_contact_no}`;
                            }
                        },
                        { data: 'loan_date', name: 'party_loans.loan_date'},
                        { data: 'due_date', name: 'party_loans.due_date'},
                        { data: 'last_payment_date', name: 'party_loans.last_payment_date'},
                        { data: 'amount', name: 'party_loans.amount'},
                        {
                            data: null, 
                            name: 'party_loans.paid_amount', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                return `<div><span class="text-success fw-bold"><b>${row.paid_amount}</b></span>`;
                            }
                        },
                        {
                            data: null, 
                            name: null, 
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                if (row.loan_type == '0') {
                                    return `<div><span class="text-success fw-bold"><b>${row.amount - row.paid_amount}</b></span></div>`;
                                }else{
                                    return ``;
                                }
                            }
                        },
                        {
                            data: null, 
                            name: 'party_loans.paid_amount', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                if (row.loan_type == '1') {
                                    return `<div><span class="text-danger fw-bold"><b>${row.amount - row.paid_amount}</b></span></div>`;
                                }else{
                                    return ``;
                                }
                            }
                        },
                        { data: 'payment_method', name: 'payment_methods.name'},
                        { data: 'reference_number', name: 'party_loans.reference_number'},
                        { data: 'note', name: 'party_loans.note'},
                        {
                            data: null, 
                            name: 'party_loans.payment_status', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let color;
                                let text;
                                if(row.payment_status == '0'){
                                    color = 'danger';
                                    text = 'Unpaid';
                                }else if(row.payment_status == '-1'){
                                    color = 'warning';
                                    text = 'Partial';
                                }else if(row.payment_status == '1'){
                                    color = 'success';
                                    text = 'Paid';
                                }
                                return `<span class="badge badge-${color}">${text}</span>`;
                            }
                        },
                        {
                            data: null, 
                            name: 'party_loans.status', 
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
                                let edit = `{{ route('loans.edit', ":id") }}`.replace(':id', row.id);
                                let print = `{{ route('loans.invoice.print', [":id", "print"]) }}`.replace(':id', row.id);
                                let view = `{{ route('loans.invoice', [":id"]) }}`.replace(':id', row.id);
                                let destroy = `{{ route('loans.destroy', ":id") }}`.replace(':id', row.id);
                                return (`<div class="d-flex justify-content-center">
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
                                                        <button
                                                            due="${row.amount - row.paid_amount}"
                                                            loan-type="${row.loan_type}"
                                                            party-id="${row.party_id}"
                                                            loan-id="${row.id }"
                                                            type="button"
                                                            class="btn btn-success btn-sm pay-now dropdown-item"
                                                            data-toggle="modal"
                                                            data-target="#exampleModal"
                                                            data-whatever="@getbootstrap" ${(row.amount - row.paid_amount)==0? 'disabled' : null}
                                                            >${row.loan_type == 1? 'Pay to Party' : 'Collect From Party' }</button>
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
                        const url = `{{ route('loans.approve', ":id") }}`.replace(':id', transaction_id);
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
