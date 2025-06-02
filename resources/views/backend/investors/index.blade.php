@extends('layouts.admin.master')
@section('content')
 <style>
    td:nth-child(8),
    td:nth-child(9){
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
                                <a href="{{ route('investors.create') }}"class="btn btn-light shadow rounded m-0"><i
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
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact</th>
                                                <th>Address</th>
                                                <th>DOB</th>
                                                <th>NID</th>
                                                <th>InvestmentCapital</th>
                                                <th>AvailableBalance</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="7"><b>Total:</b></td>
                                                <td @style('text-align: right')><b id="total_investment_capital"></b></td>
                                                <td @style('text-align: right')><b id="total_available_balance"></b></td>
                                                <td></td>
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
        var table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("investors.list") }}',
                type: 'GET', 
                dataSrc: function (json) {
                    $('#total_investment_capital').html(formatNumber(json.totalInvestmentCapital));
                    $('#total_available_balance').html(formatNumber(json.totalAvailableBalance));
                    return json.data;
                }
            },
            columns: [
                        { data: null, orderable: false, searchable: false },
                        { 
                            data: null, 
                            name: 'investors.name', 
                            orderable: true, 
                            searchable: false, 
                            render: function(data, type, row, meta) {
                               return `${row.name} ${row.is_self == '1' ? '<span class="badge badge-info">Self</span>' : ''}`;
                            }
                        },
                        { data: 'email', name: 'investors.email'},
                        { data: 'contact', name: 'investors.contact'},
                        { data: 'address', name: 'investors.address'},
                        { data: 'dob', name: 'investors.dob'},
                        { data: 'nid', name: 'investors.nid'},
                        { data: 'investment_capital', name: 'investors.investment_capital'},
                        { data: 'balance', name: 'investors.balance'},
                        { 
                            data: null, 
                            name: 'investors.status', 
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
                                let edit = `{{ route('investors.edit', ":id") }}`.replace(':id', row.id);
                                let destroy = `{{ route('investors.destroy', ":id") }}`.replace(':id', row.id);

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

           







        // $(document).ready(function(){
        //     const options = {};
        //     options.url = '{{ route("investors.list") }}';
        //     options.type = 'GET';
        //     options.columns = 
                    // [
                    //     { data: null, orderable: false, searchable: false },
                    //     { 
                    //         data: null, 
                    //         name: 'investors.name', 
                    //         orderable: true, 
                    //         searchable: false, 
                    //         render: function(data, type, row, meta) {
                    //            return `${row.name} ${row.is_self == '1' ? '<span class="badge badge-info">Self</span>' : ''}`;
                    //         }
                    //     },
                    //     { data: 'email', name: 'investors.email'},
                    //     { data: 'contact', name: 'investors.contact'},
                    //     { data: 'address', name: 'investors.address'},
                    //     { data: 'dob', name: 'investors.dob'},
                    //     { data: 'nid', name: 'investors.nid'},
                    //     { data: 'investment_capital', name: 'investors.investment_capital'},
                    //     { data: 'balance', name: 'investors.balance'},
                    //     { 
                    //         data: null, 
                    //         name: 'investors.status', 
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
                    //             let edit = `{{ route('investors.edit', ":id") }}`.replace(':id', row.id);
                    //             let destroy = `{{ route('investors.destroy', ":id") }}`.replace(':id', row.id);

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