@extends('core::base.layouts.master')
@section('title')
    {{ translate('Product Collections') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <style>
        .product-title {
            white-space: initial;
            display: inline-block;
        }
    </style>
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i>{{ $collection_details->translation('name', getLocale()) }}
            {{ translate('Products') }}</h4>

    </div>
    <div class="row">
        <div class="col-sm-7">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20 mb-2">{{ translate('Products') }}</h4>
                        <div id="bulk-action" class="dataTables_length d-flex">
                            <select class="theme-input-style bulk-action-selection mr-3">
                                <option value="">{{ translate('Bulk Action') }}</option>
                                <option value="delete_all">{{ translate('Remove selection') }}</option>
                            </select>
                            <button class="btn long bulk-action-btn">{{ translate('Apply') }}</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="collectionTable" class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Image') }}</th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collection_details->products as $key => $collection)
                                @if ($collection->product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <label class="position-relative">
                                                    <input type="checkbox" name="product_id[]" class="product-id"
                                                        value="{{ $collection->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <img src="{{ asset(getFilePath($collection->product->thumbnail_image)) }}"
                                                class="img-45" alt="{{ $collection->product->name }}">
                                        </td>
                                        <td>
                                            <span class="product-title text-capitalize">
                                                {{ $collection->product->translation('name', getLocale()) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown-button">
                                                <a href="#" class="d-flex align-items-center justify-content-end"
                                                    data-toggle="dropdown">
                                                    <div class="menu-icon mr-0">
                                                        <span></span>
                                                        <span></span>
                                                        <span></span>
                                                    </div>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a
                                                        href="{{ route('plugin.tlcommercecore.product.edit', ['id' => $collection->product->id, 'lang' => getDefaultLang()]) }}">
                                                        {{ translate('Edit Product') }}
                                                    </a>
                                                    <a href="#" class="delete-collection"
                                                        data-collection="{{ $collection->id }}">{{ translate('Remove Product') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="col-sm-5">
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('Add Product') }}</h4>
                <form action="{{ route('plugin.tlcommercecore.product.collection.products.store') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black">{{ translate('Collection') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" name="attribute_name" readonly class="theme-input-style"
                                value="{{ $collection_details->translation('name', getLocale()) }}">
                            <input type="hidden" name="collection_id" class="collection-id"
                                value="{{ $collection_details->id }}">
                            @if ($errors->has('collection_id'))
                                <div class="invalid-input">{{ $errors->first('collection_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-3">
                            <label class="font-14 bold black">{{ translate('Select Products') }} </label>
                        </div>
                        <div class="col-sm-9">
                            <select class="productSelect form-control" name="products[]" multiple>
                                @foreach ($products as $product)
                                    @if (!$collection_details->products->contains('product_id', $product->id))
                                        <option value="{{ $product->id }}">
                                            {{ $product->translation('name', getLocale()) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('products'))
                                <div class="invalid-input">{{ $errors->first('products') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to remove this product') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.product.collection.products.remove') }}">
                        @csrf
                        <input type="hidden" id="delete-collection-id" name="id">
                        <input type="hidden" id="delete-collection-id" name="collection_id"
                            value="{{ $collection_details->id }}">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->
@endsection
@section('custom_scripts')
    @include('core::base.includes.data_table.script')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('.productSelect').select2({
                    theme: "classic",
                    closeOnSelect: false,
                    placeholder: '{{ translate('No Product Selected') }}',
                });
            });
            /**
             * 
             * Collection product data table
             * */
            $("#collectionTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#collectionTable_wrapper .col-md-6:eq(0)');

            /**
             * 
             * Select all collections
             **/
            $('.select-all').on('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".product-id").prop("checked", true);
                } else {
                    $(".product-id").prop("checked", false);
                }
            });
            /**
             * 
             * Bulk action
             **/
            $('.bulk-action-btn').on('click', function(e) {
                let action = $('.bulk-action-selection').val();
                if (action === 'delete_all') {
                    let selected_items = [];
                    let collection_id = $('.collection-id').val();
                    $('input[name^="product_id"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    let data = {
                        'collection_id': collection_id,
                        'selected_items': selected_items
                    }
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.tlcommercecore.product.collection.products.remove.bulk') }}', {
                            _token: '{{ csrf_token() }}',
                            data: data
                        }, function(data) {
                            location.reload();
                        })
                    } else {
                        toastr.error('{{ translate('No Item Selected') }}', "Error!");
                    }
                } else {
                    toastr.error('{{ translate('No Action Selected') }}', "Error!");
                }
            });

            /**
             * 
             * Remove collection product
             * 
             * */
            $('.delete-collection').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('collection');
                $("#delete-collection-id").val(id);
                $('#delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
