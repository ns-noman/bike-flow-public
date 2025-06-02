@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary p-1">
                            </div>
                            <div class="card-body">
                                <div class="bootstrap-data-table-panel">
                                    <div class="table-responsive">
                                        <table id="dataTable" class="table table-sm table-striped table-bordered table-centre">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Payment Date</th>
                                                    <th>Loan Number</th>
                                                    <th>Party Name</th>
                                                    <th>Payment Type</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Reference No</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Payment Date</th>
                                                    <th>Loan Number</th>
                                                    <th>Party Name</th>
                                                    <th>Payment Type</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Reference No</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
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
                url: '{{ route("party-payments.list") }}',
                type: 'GET',
            },
            columns: [
                        { data: null, orderable: false, searchable: false },
                        { data: 'date', name: 'party_payments.date'},
                        {
                            data: null, 
                            name: 'party_loans.loan_no', 
                            orderable: true, 
                            searchable: true, 
                            render: function(data, type, row, meta) {
                                let view = `{{ route('loans.invoice', ":id") }}`.replace(':id', row.loan_id);
                                return `<a href="${view}" class=""><b>${row.loan_no}</b></a>`;
                            }
                        },
                        { data: 'party_name', name: 'parties.name'},
                        {
                            data: null, 
                            name: 'party_payments.payment_type', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let color;
                                let text;
                                if(row.payment_type == '0'){
                                    color = 'danger';
                                    text = 'Paid To Party';
                                }else if(row.payment_type == '1'){
                                    color = 'success';
                                    text = 'Collected From Party';
                                }
                                return `<span class="badge badge-${color}">${text}</span>`;
                            }
                        },
                        { data: 'amount', name: 'party_payments.amount'},
                      
                        { data: 'payment_method', name: 'payment_methods.name'},
                        { data: 'reference_number', name: 'party_payments.reference_number'},
                        { data: 'note', name: 'party_payments.note'},
                        { data: 'creator_name', name: 'admins.name'},
                        { 
                            data: null, 
                            name: 'party_payments.status', 
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
                                let destroy = `{{ route('party-payments.destroy', ":id") }}`.replace(':id', row.id);
                                return (` <div class="d-flex justify-content-center">
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
                        const url = `{{ route('party-payments.approve', ":id") }}`.replace(':id', transaction_id);
                        $.ajax({
                            url: url,
                            method: 'GET',
                            dataType: 'JSON',
                            success: function(res) {
                                message(res);
                                table.draw();
                                console.log(res);
                                setTimeout(() => {
                                    let url = `{{ route('loans.invoice.print', [":id", "print"]) }}`.replace(':id', res.data.loan_id);
                                    window.open(url, '_self');
                                }, 1000);
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