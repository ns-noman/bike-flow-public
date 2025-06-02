@extends('layouts.admin.master')
@section('content')
<style>
    td:nth-child(8),
    td:nth-child(7) ,
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
                                        <table id="dataTable" class="table table-sm table-striped table-bordered table-centre">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th width="40%">Title</th>
                                                    <th>CategoryType</th>
                                                    <th>Category</th>
                                                    <th>SubCategory</th>
                                                    <th>PurchasePrice</th>
                                                    <th>Stock</th>      
                                                    <th width="15%">StockValue</th>      
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7"><b>Total:</b></td>
                                                    <td @style('text-align: right')><b id="totalStockValue"></b></td>
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
                const colmd = 3;
                const dataTable_length = $('#dataTable_length').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);
                const dataTable_filter = $('#dataTable_filter').parent().removeClass('col-md-6').addClass(`col-md-${colmd}`);
                filterContainer.before(`
                    <div class="col-sm-12 col-md-${colmd}">
                        <div class="dataTables_filter" style="display: flex; align-items: center; justify-content: center;">
                            <label style="font-weight: normal; white-space: nowrap; display: flex; align-items: center;margin-bottom: .5rem;">
                                Category Type:
                                    <select name="cat_type_id" id="cat_type_id" class="form-control select2 form-control-sm filter" required>
                                        <option value="">All Type</option>
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
                                    <option selected value="">All Category</option>
                                </select>
                            </label>
                        </div>
                    </div>
                `);
                $('.select2').select2();
            },
            processing: false,
            serverSide: true,
            ajax: {
                url: '{{ route("reports.stock-reports") }}',
                type: 'post',
                data: function(d) {
                    d.cat_type_id = $('#cat_type_id').val();
                    d.cat_id = $('#cat_id').val();
                    d._token = $('meta[name="csrf-token"]').attr('content');
                },
                dataSrc: function(json) {
                    document.getElementById('totalStockValue').innerHTML = formatNumber(json.totalStockValue);
                    return json.data;
                }
            },
            columns: [
                { data: null, orderable: false, searchable: false },
                { data: 'name', name: 'items.name'},
                { data: 'cat_type_name', name: 'category_types.title'},
                { data: 'cat_name', name: 'categories.title'},
                { data: 'sub_cat_name', name: 'subcategories.title'},
                { 
                    data: null, 
                    name: 'items.purchase_price', 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                        return formatNumber(row.purchase_price);
                    }
                },
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
                    name: 'items.current_stock', 
                    orderable: true, 
                    searchable: false, 
                    render: function(data, type, row, meta) {
                        return formatNumber(row.current_stock * row.purchase_price);
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
        
        $(document).on('change','#cat_type_id',async function() {
            loadCategories($(this).val());
        });

    });

    async function loadCategories(id) {
        let res = await nsAjaxGet('{{ route("items.categories", ":id") }}'.replace(':id', id));
        let options = `<option selected value="">All Category</option>`;
        res.forEach(function(category) {
            options += `<option value="${category.id}">${category.title}</option>`;
            
            category.subcategories.forEach(function(subcategory) {
                options += `<option value="${subcategory.id}">&nbsp;&nbsp;&rarr; ${subcategory.title}</option>`;
            });
        });
        $('#cat_id').html(options);
    }
    function print() {
        let cat_type_id = $('#cat_type_id').val() || '';
        let cat_id = $('#cat_id').val() || '';
        window.open(`?&print=true&cat_type_id=${cat_type_id}&cat_id=${cat_id}`, '_blank');
    }
</script>
@endsection