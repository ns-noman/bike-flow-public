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
                                    <a href="{{ route('items.create') }}"class="btn btn-light shadow rounded m-0">
                                        <i class="fas fa-plus"></i>
                                        <span>Add New</span></i></a>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                        <div class="bootstrap-data-table-panel">
                                            <div class="table-responsive">
                                                <table id="dataTable" class="table table-sm table-striped table-bordered table-centre">
                                                    <thead id="thead">
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>Title</th>
                                                            <th>CategoryType</th>
                                                            <th>Category</th>
                                                            <th>SubCategory</th>
                                                            <th>PurchasePrice</th>
                                                            <th>SalesPrice</th>
                                                            <th>Vat(%)</th>
                                                            <th>Stock</th>
                                                            <th>Status</th>
                                                            <th>Action</th>        
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
                                Category Type:
                                    <select name="cat_type_id" id="cat_type_id" class="form-control select2 form-control-sm filter" required>
                                        <option value="">Select Type</option>
                                        @foreach($data['category_types'] as $key => $category_type)
                                            <option value="{{ $category_type->id }}">{{ $category_type->title }}</option>
                                        @endforeach
                                    </select>
                            </label>
                        </div>
                    </div>
                `);
                filterContainer.before(`
                    <div id="category_div" class="col-sm-12 col-md-${colmd}">
                        <div class="dataTables_filter" style="display: flex; align-items: center; justify-content: center;">
                            <label style="font-weight: normal; white-space: nowrap; display: flex; align-items: center;margin-bottom: .5rem;">
                                Category:
                                <select data-column="1" class="form-control form-control-sm select2 filter" id="cat_id" name="cat_id" style="margin-left: 10px;">
                                    <option selected value="">Select Category</option>
                                </select>
                            </label>
                        </div>
                    </div>
                `);
                $('.select2').select2();
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("items.list") }}',
                type: 'GET',
                data: function(d) {
                    d.cat_type_id = $('#cat_type_id').val();
                    d.cat_id = $('#cat_id').val();
                }
            },
            columns: [
                { data: null, orderable: false, searchable: false },
                { data: 'name', name: 'items.name'},
                { data: 'cat_type_name', name: 'category_types.title'},
                { data: 'cat_name', name: 'categories.title'},
                { data: 'sub_cat_name', name: 'subcategories.title'},
                // { data: 'image', name: 'items.image'},
                { data: 'purchase_price', name: 'items.purchase_price'},
                { data: 'sale_price', name: 'items.sale_price'},
                { data: 'vat', name: 'items.vat'},
                { 
                    data: null, 
                    name: 'items.current_stock', 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                        return `${row.current_stock} ${row.unit_name}`;
                    }
                },
                { 
                    data: null, 
                    name: 'items.status', 
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
                        let edit = `{{ route('items.edit', ":id") }}`.replace(':id', row.id);
                        let destroy = `{{ route('items.destroy', ":id") }}`.replace(':id', row.id);

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
            search: { return: false }
        });

        $(document).on('change','.filter',function() {
            table.draw();
        });
        
        $(document).on('change','#cat_type_id',async function() {
            loadCategories($(this).val());
        });

    });

    async function loadCategories(id) {
        let res = await nsAjaxGet('{{ route("items.categories", ":id") }}'.replace(':id', id));
        let options = `<option selected value="">Select Category</option>`;
        res.forEach(function(category) {
            options += `<option value="${category.id}">${category.title}</option>`;
            
            category.subcategories.forEach(function(subcategory) {
                options += `<option value="${subcategory.id}">&nbsp;&nbsp;&rarr; ${subcategory.title}</option>`;
            });
        });
        $('#cat_id').html(options);
    }
</script>
@endsection
