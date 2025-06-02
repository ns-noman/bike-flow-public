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
                                <h3 class="card-title">
                                    <a href="{{ route('bike-profits.share-records.create', $data["bike_profit_id"]) }}"class="btn btn-light shadow rounded m-0">
                                        <i class="fas fa-plus"></i>
                                        <span>Add New</span></a>
                                    <a href="{{ route('bike-profits.index') }}"class="btn btn-warning shadow rounded m-0">
                                        <span>Back to Bike Profit</span></a>
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
                                                    <th>Payment Methods</th>
                                                    <th>Amount</th>
                                                    <th>Reference No</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Date</th>
                                                    <th>Payment Methods</th>
                                                    <th>Amount</th>
                                                    <th>Reference No</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
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
        $('form').on('submit', function(e) {
            const transaction_type = $('#transaction_type option:selected').val();
            if(transaction_type=='debit_amount'){
                const account_bal = parseFloat($('#account_id option:selected').attr('account-bal'));
                const amount = parseFloat($('#amount').val());
                if(amount>account_bal){
                    message({success:false, message: 'Account Balance Exceeded!'});
                    e.preventDefault();
                }
            }
        });
    });
    $(document).ready(function(){
        var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("bike-profits.share-records-list") }}',
            type: 'GET',
            data: function(d) {
                d.bike_profit_id = '{{ $data["bike_profit_id"] }}';
            }
        },
        columns: [
                    { data: null, orderable: false, searchable: false },
                    { data: 'date', name: 'bike_profit_share_records.date'},
                    { data: 'payment_method', name: 'payment_methods.name'},
                    { data: 'amount', name: 'bike_profit_share_records.amount'},
                    { data: 'reference_number', name: 'bike_profit_share_records.reference_number'},
                    { data: 'note', name: 'bike_profit_share_records.note'},
                    { data: 'creator_name', name: 'admins.name'},
                    {
                            data: null, 
                            name: 'bike_profit_share_records.status', 
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
                                return `<button bf_id="{{ $data["bike_profit_id"] }}" bfsr_id="${row.id}" type="button" class="btn btn-sm btn-${color} ${eventClass}">${text}</button>`;
                            }
                        },
                    { 
                        data: null,
                        orderable: false, 
                        searchable: false, 
                        render: function(data, type, row, meta) {
                            let edit = `{{ route('bike-profits.share-records.edit', [":bp_id",":bpsr_id"]) }}`.replace(':bp_id', "{{ $data['bike_profit_id'] }}").replace(':bpsr_id', row.id);
                            let destroy = `{{ route('bike-profits.share-records.destroy', ":id") }}`.replace(':id', row.id);
                            return (` <div class="d-flex justify-content-center">
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
            let bf_id = $(this).attr('bf_id');
            let bfsr_id = $(this).attr('bfsr_id');
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
                    const url = `{{ route('bike-profits.share-records.approve', [":bp_id",":bpsr_id"]) }}`.replace(':bp_id', bf_id).replace(':bpsr_id', bfsr_id);
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