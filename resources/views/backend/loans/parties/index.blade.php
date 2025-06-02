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
                                <a href="{{ route('parties.create') }}"class="btn btn-light shadow rounded m-0"><i
                                        class="fas fa-plus"></i>
                                    <span>Add New</span></i></a>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="bootstrap-data-table-panel">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-sm table-striped table-bordered table-centre">
                                        <thead>
                                            {{-- 'name',
                                            'email',
                                            'phone',
                                            'address',
                                            'nid_number',
                                            'date_of_birth',
                                            'current_balance',
                                            'status',
                                            'created_by_id',
                                            'updated_by_id', --}}
                                            <tr>
                                                <th>SN</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact</th>
                                                <th>Address</th>
                                                <th>NID</th>
                                                <th>DOB</th>
                                                <th>Balance</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>SN</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact</th>
                                                <th>Address</th>
                                                <th>NID</th>
                                                <th>DOB</th>
                                                <th>Balance</th>
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
            const options = {};
            options.url = '{{ route("parties.list") }}';
            options.type = 'GET';
            options.columns = 
                    [
                        { data: null, orderable: false, searchable: false },

                        { data: 'name', name: 'parties.name'},
                        { data: 'email', name: 'parties.email'},
                        { data: 'phone', name: 'parties.phone'},
                        { data: 'address', name: 'parties.address'},
                        { data: 'nid_number', name: 'parties.nid_number'},
                        { data: 'date_of_birth', name: 'parties.date_of_birth'},
                        { data: 'current_balance', name: 'parties.current_balance'},
                        { 
                            data: null, 
                            name: 'parties.status', 
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
                                let edit = `{{ route('parties.edit', ":id") }}`.replace(':id', row.id);
                                let destroy = `{{ route('parties.destroy', ":id") }}`.replace(':id', row.id);

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
                    ];
            options.processing = true;
            dataTable(options);
        });
    </script>
@endsection