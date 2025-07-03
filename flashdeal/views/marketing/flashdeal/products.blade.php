@extends('core::base.layouts.master')
@section('title')
    {{ translate('Deals Products') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <style>
        .product-name {
            white-space: normal;
            max-width: 200px;
            display: block;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .dataTable {
            width: 100% !important;
        }
    </style>
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i>{{ $deal_details->translation('title', getLocale()) }}
            {{ translate('Products') }}</h4>

    </div>
    <div class="row">
        <!--Product List-->
        <div class="col-sm-8">
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
                    <table id="dealProductTable" class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Product') }}</th>
                                <th>{{ translate('Discount') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deal_details->deal_products as $key => $deal_product)
                                @if ($deal_product->product != null)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="position-relative mr-2">
                                                    <input type="checkbox" name="items[]" class="item-id"
                                                        value="{{ $deal_product->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-10">
                                                <img src="{{ getFilePath($deal_product->product->thumbnail_image) }}"
                                                    class="img-45" alt="{{ $deal_product->product->name }}"
                                                    title="{{ $deal_product->product->translation('name', getLocale()) }}">
                                                <p class="product-name">
                                                    {{ $deal_product->product->translation('name', getLocale()) }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $deal_product->discount }}{{ $deal_product->discount_type == config('tlecommercecore.amount_type.percent') ? '%' : currencySymbol() }}
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
                                                    <a href="#" class="edit-deal-product"
                                                        data-product="{{ $deal_product->id }}"
                                                        data-discount="{{ $deal_product->discount }}"
                                                        data-discount_type="{{ $deal_product->discount_type }}">
                                                        {{ translate('Edit Discount') }}
                                                    </a>
                                                    @if (isActivePluging('tlecommercecore'))
                                                        <a href="{{ route('plugin.tlcommercecore.product.edit', ['id' => $deal_product->product->id, 'lang' => getDefaultLang()]) }}"
                                                            target="_blank">
                                                            {{ translate('Product Details') }}
                                                        </a>
                                                    @endif
                                                    <a href="#" class="remove-deal-product"
                                                        data-product="{{ $deal_product->id }}">{{ translate('Remove Product') }}</a>
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
        <!--End product list-->
        <!--Product add form-->
        <div class="col-sm-4">
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('Add Product') }} {{ translate('to') }}
                    {{ $deal_details->translation('title', getLocale()) }}</h4>
                <form action="{{ route('plugin.flashdeal.products.store') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-12">
                            <label class="font-14 bold black">{{ translate('Select Products') }} </label>
                        </div>
                        <div class="col-sm-12">
                            <select class="productSelect form-control" name="products[]" multiple>
                            </select>
                            @if ($errors->has('products'))
                                <div class="invalid-input">{{ $errors->first('products') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-12">
                            <label class="font-14 bold black ">{{ translate('Discount') }} </label>
                        </div>
                        <div class="col-sm-12">
                            <input type="hidden" name="deal_id" class="deal-id" value="{{ $deal_details->id }}">
                            <input type="number" name="discount" class="theme-input-style" placeholder="0.00"
                                value="{{ old('discount') }}">
                            @if ($errors->has('discount'))
                                <div class="invalid-input">{{ $errors->first('discount') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-12">
                            <label class="font-14 bold black ">{{ translate('Discount Type') }} </label>
                        </div>
                        <div class="col-sm-12">
                            <select class="theme-input-style" name="discount_type">
                                <option value="{{ config('tlecommercecore.amount_type.flat') }}"> {{ translate('Flat') }}
                                </option>
                                <option value="{{ config('tlecommercecore.amount_type.percent') }}">
                                    {{ translate('Percentage') }}</option>
                            </select>
                            @if ($errors->has('discount_type'))
                                <div class="invalid-input">{{ $errors->first('discount_type') }}</div>
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
        <!--End product add form-->
    </div>

    <!--Edit Discount Modal-->
    <div id="edit-modal" class="edit-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 font-weight-bold">{{ translate('Update Product Discount') }}</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('plugin.flashdeal.products.update') }}" method="POST">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black ">{{ translate('Discount') }} </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="hidden" name="deal_id" class="deal-id" value="{{ $deal_details->id }}">
                                <input type="hidden" id="update-deal-product-id" name="deal_product_id">
                                <input type="number" name="discount" class="discount theme-input-style"
                                    placeholder="0.00" value="{{ old('discount') }}">
                                @if ($errors->has('discount'))
                                    <div class="invalid-input">{{ $errors->first('discount') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black ">{{ translate('Discount Type') }} </label>
                            </div>
                            <div class="col-sm-12">
                                <select class="theme-input-style discount_type" id="discount_type" name="discount_type">
                                    <option value="{{ config('tlecommercecore.amount_type.flat') }}">
                                        {{ translate('Flat') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.amount_type.percent') }}">
                                        {{ translate('Percentage') }}</option>
                                </select>
                                @if ($errors->has('discount_type'))
                                    <div class="invalid-input">{{ $errors->first('discount_type') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 d-flex justify-content-between">
                                <button type="button" class="btn long btn-danger"
                                    data-dismiss="modal">{{ translate('cancel') }}</button>
                                <button type="submit" class="btn long">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Edit Discount Modal-->

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to remove this product') }}?</p>
                    <form method="POST" action="{{ route('plugin.flashdeal.products.remove') }}">
                        @csrf
                        <input type="hidden" id="deal-product-id" name="deal_product_id">
                        <input type="hidden" id="deal-id" name="deal_id" value="{{ $deal_details->id }}">
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
                    allowClear: true,
                    closeOnSelect: false,
                    placeholder: "{{ translate('Select Products') }}",
                    ajax: {
                        url: '{{ route('plugin.tlcommercecore.product.dropdown.options') }}',
                        dataType: 'json',
                        method: "GET",
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    }

                });
            });
            /**
             * 
             * deals product data table
             * */
            $("#dealProductTable").DataTable({
                "scrollX": true,
                orderCellsTop: true,
                fixedHeader: true,
                "pageLength": 10,
                responsive: false,
                sDom: 'lrtip'
            }).buttons().container().appendTo('#dealProductTable_wrapper .col-md-6:eq(0)');
            /**
             * 
             * Select all products
             **/
            $('.select-all').on('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".item-id").prop("checked", true);
                } else {
                    $(".item-id").prop("checked", false);
                }
            })
            /**
             * 
             * Bulk action
             **/
            $('.bulk-action-btn').on('click', function(e) {
                e.preventDefault();
                let action = $('.bulk-action-selection').val();
                if (action == 'delete_all') {
                    let selected_items = [];
                    let deal_id = $('.deal-id').val();
                    $('input[name^="items"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    let data = {
                        'deal_id': deal_id,
                        'selected_items': selected_items
                    }
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.flashdeal.products.remove.bulk') }}', {
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
             * remove deal product
             * 
             * */
            $('.remove-deal-product').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('product');
                $("#deal-product-id").val(id);
                $('#delete-modal').modal('show');
            });
            /**
             * 
             * edit deal product
             * 
             * */
            $('.edit-deal-product').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('product');
                let discount = $this.data('discount');
                $('.discount').val(discount);
                let discount_type = $this.data('discount_type');
                $('.discount_type').val(discount_type);
                $('#update-deal-product-id').val(id);
                $(`#discount_type option[value='${discount_type}']`).prop('selected', true);
                $('#edit-modal').modal('show');
            });

        })(jQuery);
    </script>
@endsection
