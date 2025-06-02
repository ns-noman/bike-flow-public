@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ $data['title'] }} Form</h3>
                            </div>
                            <form action="{{ isset($data['item']) ? route('items.update',$data['item']->id) : route('items.store'); }}" method="POST" enctype="multipart/form-data">
                                @csrf()
                                @if(isset($data['item']))
                                    @method('put')
                                @endif
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="form-group col-sm-12 col-md col-lg">
                                                    <label>Category Type *</label>
                                                    <select name="cat_type_id" id="cat_type_id" class="form-control" required>
                                                        @foreach($data['category_types'] as $key => $category_type)
                                                            <option  @isset($data['item']) @if( $data['item']->cat_type_id == $category_type->id ) selected @endif @endisset value="{{ $category_type->id }}">{{ $category_type->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg">
                                                    <label>Category *</label>
                                                    <select name="cat_id" id="cat_id" class="form-control" required>
                                                        <option value="">Select Category</option>
                                                    </select> 
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg">
                                                    <label>Sub Category</label>
                                                    <select name="sub_cat_id" id="sub_cat_id" class="form-control">
                                                        <option value="">Select Sub Category</option>
                                                    </select> 
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg">
                                                    <label>Item Name *</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->name : null }}" type="text" class="form-control" name="name" placeholder="Item Name" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg">
                                                    <label>Unit *</label>
                                                    <select name="unit_id" id="unit_id" class="form-control" required>
                                                        <option value="">Select Unit</option>
                                                        @foreach ($data['units'] as $unit)
                                                            <option
                                                                @isset($data['item'])
                                                                    @if($data['item']->unit_id == $unit->id)
                                                                        @selected(true)
                                                                    @endif
                                                                @endisset
                                                            value="{{ $unit->id }}">{{ $unit->title }}</option>
                                                        @endforeach
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="form-group col-sm-12 col-md col-lg">
                                                    <label>Purchase Price *</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->purchase_price : null }}" type="number" class="form-control" name="purchase_price" placeholder="0.00" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg" id="price_div_id">
                                                    <label>Sale Price *</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->sale_price : null }}" type="number" class="form-control" name="sale_price" id="sale_price"  placeholder="0.00" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg" id="vat_div_id">
                                                    <label>Vat (%)*</label>
                                                    <input value="{{ isset($data['item']) ? $data['item']->vat : null }}" type="number" class="form-control" name="vat" id="vat"  placeholder="0.00" required>
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg" {{ isset($data['item']) ? 'hidden' : null }}>
                                                    <label>Opening Stock</label>
                                                    <input {{ isset($data['item']) ? 'disabled' : null }} value="{{ isset($data['item']) ? $data['item']->opening_stock : null }}" type="number" class="form-control" name="opening_stock" id="opening_stock" placeholder="0.00">
                                                </div>
                                                <div class="form-group col-sm-12 col-md col-lg">
                                                    <label>Status *</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option {{ isset($data['item']) ? $data['item']->status == 1 ? 'selected' : null : null }} value="1">Active</option>
                                                        <option {{ isset($data['item']) ? $data['item']->status == 0 ? 'selected' : null : null }} value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-7 col-lg-7">
                                            <label>Description</label>
                                            <textarea name="description" id="description" class="form-control" cols="30" rows="3" placeholder="Description">{{ isset($data['item']) ? $data['item']->description : null }}</textarea>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-5 col-lg-5 margin-auto" style="margin: auto;">
                                            <label>Image (1:1)</label>
                                            <label class="col-md-3" style="cursor:pointer">
                                                <img id="image_view" style="max-width:100%" class="img-thumbnail" src="{{ asset('public/uploads/items/'. ( (isset($data['item']) && $data['item']->image)? $data['item']->image : 'placeholder.png')) }}">
                                                <input id="image" name="image" style="display:none" onchange="itemImage(this);" type="file" accept="image/*">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> 
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(async function(){
            initialize();
            $('#cat_type_id, #cat_id, #sub_cat_id').on('change',function(event){
                updateData(event.target);
            });
        });
        async function initialize() {
            let defaultData = { cat_type_id: 1, cat_id: '', sub_cat_id: '' };
            let data = nsGetItem("itemSearchBy") || defaultData;
            if ("{{ isset($data['item']) }}") {
                data = {
                    cat_type_id: "{{ isset($data['item']) ? $data['item']->cat_type_id : null }}",
                    cat_id: "{{ isset($data['item']) ? $data['item']->cat_id : null }}",
                    sub_cat_id: "{{ isset($data['item']) ? $data['item']->sub_cat_id : null }}"
                };
            }
            nsSetItem("itemSearchBy",data);
            await loadCategories(data.cat_type_id,data.cat_id);
            await loadSubCategories(data.cat_id, data.sub_cat_id);
            $('#cat_type_id').val(data.cat_type_id);
        }
        function updateData(triggeredElement) {
            let data = getFormData();
            if (triggeredElement.id === 'cat_type_id') {
                loadCategories(data.cat_type_id);
                data.cat_id = '';
                data.sub_cat_id = '';
            } else if (triggeredElement.id === 'cat_id') {
                loadSubCategories(data.cat_id);
                data.sub_cat_id = '';
            }
            nsSetItem("itemSearchBy",data);
        }
        function getFormData(){
            return {
                cat_type_id: $('#cat_type_id').val(),
                cat_id: $('#cat_id').val(),
                sub_cat_id: $('#sub_cat_id').val(),
            }
        }
        
        async function loadCategories(cat_type_id, selectd_id = null) {
            res = await nsAjaxGet('{{ route("items.categories", ":id") }}'.replace(':id', cat_type_id));
            nsSetOption({
                selectElementId: 'cat_id',
                data: res,
                defaultValue: '',
                defaultText: 'Select Category',
                displayColumn: 'title',
                selectedValue: selectd_id,
            });
            nsSetOption({
                selectElementId: 'sub_cat_id',
                data: [],
                defaultValue: '',
                defaultText: 'Select Sub Sategory',
                displayColumn: 'title',
            });
        }

        async function loadSubCategories(cat_id, selectd_id = null) {
            res = await nsAjaxGet('{{ route("items.sub-categories", ":id") }}'.replace(':id', cat_id));
            nsSetOption({
                selectElementId: 'sub_cat_id',
                data: res,
                defaultValue: '',
                defaultText: 'Select Sub Sategory',
                displayColumn: 'title',
                selectedValue: selectd_id,
            });
        }

    </script>
@endsection
