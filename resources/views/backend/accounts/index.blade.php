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
                            <div class="card-header bg-primary p-1">
                                <h3 class="card-title">
                                    <a href="{{ route('accounts.create') }}"class="btn btn-light shadow rounded m-0">
                                        <i class="fas fa-plus"></i>
                                        <span>Add New</span></a>
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
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b id="totalAccountBalance"></b></td>
                                                    <td colspan="2"></td>
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

        var table = $('#dataTable').DataTable({
            processing: false,
            serverSide: true,
            ajax: {
                url: '{{ route("accounts.list") }}',
                type: 'GET',
                dataSrc: function (json) {
                    $('#totalAccountBalance').html(formatNumber(json.totalAccountBalance));
                    return json.data;
                }
            },
            columns: [
                        { data: null, orderable: false, searchable: false },
                        { data: 'payment_method', name: 'payment_methods.name'},
                        { data: 'account_no', name: 'accounts.account_no'},
                        { data: 'holder_name', name: 'accounts.holder_name'},
                        { data: 'balance', name: 'accounts.balance'},
                        { 
                            data: null, 
                            name: 'payment_methods.status', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                               return `<span class="badge badge-${row.status == '1' ? 'success' : 'warning'}">${row.status == '1' ? 'Active' : 'Inactive'}</span>`;
                            }
                        },
                        { 
                            data: null,
                            orderable: false, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                                let edit = `{{ route('accounts.edit', ":id") }}`.replace(':id', row.id);
                                let destroy = `{{ route('accounts.destroy', ":id") }}`.replace(':id', row.id);

                                return (` <div class="d-flex justify-content-center">
                                                <a href="${edit}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form class="delete" action="${destroy}" method="post" hidden>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
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
            $(document).on('change','.filter',function() {
                table.draw();
            });


        // $(document).ready(function(){
        //     const options = {};
        //     options.url = '{{ route("accounts.list") }}';
        //     options.type = 'GET';
        //     options.columns = 
                    // [
                    //     { data: null, orderable: false, searchable: false },
                    //     { data: 'payment_method', name: 'payment_methods.name'},
                    //     { data: 'account_no', name: 'accounts.account_no'},
                    //     { data: 'holder_name', name: 'accounts.holder_name'},
                    //     { data: 'balance', name: 'accounts.balance'},
                    //     { 
                    //         data: null, 
                    //         name: 'payment_methods.status', 
                    //         orderable: true, 
                    //         searchable: false, 
                    //         render: function(data, type, row, meta) {
                    //            return `<span class="badge badge-${row.status == '1' ? 'success' : 'warning'}">${row.status == '1' ? 'Active' : 'Inactive'}</span>`;
                    //         }
                    //     },
                    //     { 
                    //         data: null,
                    //         orderable: false, 
                    //         searchable: false, 
                    //         render: function(data, type, row, meta) {
                    //             let edit = `{{ route('accounts.edit', ":id") }}`.replace(':id', row.id);
                    //             let destroy = `{{ route('accounts.destroy', ":id") }}`.replace(':id', row.id);

                    //             return (` <div class="d-flex justify-content-center">
                    //                             <a href="${edit}"
                    //                                 class="btn btn-sm btn-info">
                    //                                 <i class="fa-solid fa-pen-to-square"></i>
                    //                             </a>
                    //                             <form class="delete" action="${destroy}" method="post" hidden>
                    //                                 @csrf
                    //                                 @method('DELETE')
                    //                                 <button type="submit" class="btn btn-sm btn-danger">
                    //                                     <i class="fa-solid fa-trash-can"></i>
                    //                                 </button>
                    //                             </form>
                    //                         </div>
                    //                     `);
                    //         }
                    //     }
                    // ];
        //     options.processing = true;
        //     dataTable(options);
        // });
    </script>
@endsection