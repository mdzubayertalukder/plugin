@extends('core::base.layouts.master')
@section('title')
    {{ translate('Products') }}
@endsection
@section('custom_css')
    <link href="{{ asset('/public/backend/assets/css/ratings.css') }}" rel="stylesheet" />
    <style>
        .product-title {
            max-width: 150px;
            display: inline-block;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Products') }}</h4>
                        @can('Manage Add New Product')
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('plugin.tlcommercecore.product.add.new') }}"
                                    class="btn long">{{ translate('Add New Product') }}</a>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="px-2 filter-area d-flex align-items-center">
                    <!--Filter area-->
                    <form method="get" action="{{ route('plugin.tlcommercecore.product.list') }}">
                        <select class="theme-input-style mb-2" name="per_page">
                            <option value="">{{ translate('Per page') }}</option>
                            <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                            <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                            <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>All</option>
                        </select>
                        <select class="theme-input-style mb-2" name="product_status">
                            <option value="">{{ translate('Product status') }}</option>
                            <option value="{{ config('settings.general_status.active') }}" @selected(request()->has('product_status') && request()->get('product_status') == config('settings.general_status.active'))>
                                {{ translate('Published') }}
                            </option>
                            <option value="{{ config('settings.general_status.in_active') }}" @selected(request()->has('product_status') && request()->get('product_status') == config('settings.general_status.in_active'))>
                                {{ translate('Unpublished') }}
                            </option>
                        </select>
                        <select class="theme-input-style mb-2" name="product_featured">
                            <option value="">{{ translate('Product Featured') }}</option>
                            <option value="{{ config('settings.general_status.active') }}" @selected(request()->has('product_featured') && request()->get('product_featured') == config('settings.general_status.active'))>
                                {{ translate('Featured') }}
                            </option>
                            <option value="{{ config('settings.general_status.in_active') }}" @selected(request()->has('product_featured') && request()->get('product_featured') == config('settings.general_status.in_active'))>
                                {{ translate('Regular') }}
                            </option>
                        </select>
                        <select class="theme-input-style mb-2" name="has_variation">
                            <option value="">{{ translate('Product Variation') }}</option>
                            <option value="{{ config('tlecommercecore.product_variant.variable') }}"
                                @selected(request()->has('has_variation') && request()->get('has_variation') == config('tlecommercecore.product_variant.variable'))>
                                {{ translate('Variant Product') }}
                            </option>
                            <option value="{{ config('tlecommercecore.product_variant.single') }}"
                                @selected(request()->has('has_variation') && request()->get('has_variation') == config('tlecommercecore.product_variant.single'))>
                                {{ translate('Single Product') }}
                            </option>
                        </select>
                        <select class="theme-input-style mb-2" name="discount">
                            <option value="">{{ translate('Product Discount') }}</option>
                            <option value="{{ config('settings.general_status.in_active') }}" @selected(request()->has('discount') && request()->get('discount') == config('settings.general_status.in_active'))>
                                {{ translate('No Discount') }}
                            </option>
                            <option value="{{ config('settings.general_status.active') }}" @selected(request()->has('discount') && request()->get('discount') == config('settings.general_status.active'))>
                                {{ translate('Discounted') }}
                            </option>
                        </select>
                        <input type="text" name="search_key" class="theme-input-style mb-2"
                            value="{{ request()->has('search_key') ? request()->get('search_key') : '' }}"
                            placeholder="Enter product name">
                        <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                    </form>
                    @if (request()->has('search_key') || request()->has('payment_status'))
                        <a class="btn long btn-danger" href="{{ route('plugin.tlcommercecore.product.list') }}">
                            {{ translate('Clear Filter') }}
                        </a>
                    @endif
                    <!--End filter area-->
                    <!--Bulk actions-->
                    <select class="theme-input-style bulk-action-selection">
                        <option value="null">{{ translate('Bulk Action') }}</option>
                        <option value="active">{{ translate('Make publish') }}</option>
                        <option value="in_active">{{ translate('Make unpublish') }}</option>
                        <option value="feature_active">{{ translate('Make feature') }}</option>
                        <option value="feature_in_active">{{ translate('Remove from feature') }}</option>
                        <option value="remove_discount">{{ translate('Remove discount') }}</option>
                        <option value="delete_all">{{ translate('Delete selection') }}</option>
                    </select>
                    <button class="btn long btn-warning fire-bulk-action">{{ translate('Apply') }}
                    </button>
                    <!--End bulk actions-->
                </div>
                <div class="table-responsive">
                    <table id="productTable1" class="hoverable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="d-flex align-items-center">
                                        <label class="position-relative">
                                            <input type="checkbox" name="select_all" class="select-all">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </th>
                                <th>{{ translate('Image') }}</th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Info') }}</th>
                                <th>{{ translate('Stock & Sales') }} </th>
                                <th>{{ translate('Featured') }} </th>
                                <th>{{ translate('Published') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($products->count() > 0)
                                @foreach ($products as $key => $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="position-relative mr-2">
                                                    <input type="checkbox" name="product_id[]" class="product-id"
                                                        value="{{ $product->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <img src="{{ asset(getFilePath($product->thumbnail_image)) }}" class="img-45"
                                                alt="{{ $product->name }}">
                                        </td>
                                        <td>
                                            <span class="product-title text-capitalize">
                                                <a
                                                    href="{{ route('plugin.tlcommercecore.product.edit', ['id' => $product->id, 'lang' => getDefaultLang()]) }}">
                                                    {{ $product->translation('name', getLocale()) }}
                                                </a>
                                            </span>
                                        </td>
                                        <!--Product information-->
                                        <td>
                                            <!--Purchase price-->
                                            <div class="d-flex purchase-price">
                                                <strong>{{ translate('Purchase Price') }}: </strong>
                                                <div class="ml-1">
                                                    @if ($product->has_variant == config('tlecommercecore.product_variant.single'))
                                                        <div class="d-flex">
                                                            <div>
                                                                @if ($product->single_price->purchase_price > 0)
                                                                    {!! currencyExchange($product->single_price->purchase_price) !!}
                                                                @else
                                                                    {!! currencyExchange(0) !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        @php
                                                            $v_price = $product->variations->toArray();
                                                        @endphp
                                                        <div class="d-flex">
                                                            <div class="d-flex">
                                                                <div class="min-purchase-price">
                                                                    @if (min(array_column($v_price, 'purchase_price')) > 0)
                                                                        {!! currencyExchange(min(array_column($v_price, 'purchase_price'))) !!}
                                                                    @else
                                                                        {!! currencyExchange(0) !!}
                                                                    @endif

                                                                </div>
                                                            </div>
                                                            <div class="sperator mx-1">-</div>
                                                            <div class="d-flex">
                                                                <div class="max-purcchase-price">
                                                                    @if (max(array_column($v_price, 'purchase_price')) > 0)
                                                                        {!! currencyExchange(max(array_column($v_price, 'purchase_price'))) !!}
                                                                    @else
                                                                        {!! currencyExchange(0) !!}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <!--End Purchase price-->
                                            <!--Unit price-->
                                            <div class="d-flex unit-price"><strong>{{ translate('Unit Price') }}: </strong>
                                                <div class="ml-1">
                                                    @if ($product->has_variant == config('tlecommercecore.product_variant.single'))
                                                        <div class="d-flex">
                                                            <div class="single-unit-price">
                                                                @if ($product->single_price->unit_price > 0)
                                                                    {!! currencyExchange($product->single_price->unit_price) !!}
                                                                @else
                                                                    {!! currencyExchange(0) !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        @php
                                                            $v_price = $product->variations->toArray();
                                                        @endphp
                                                        <div class="d-flex">
                                                            <div class="d-flex">
                                                                <div class="min-unit-price">
                                                                    @if (min(array_column($v_price, 'unit_price')) > 0)
                                                                        {!! currencyExchange(min(array_column($v_price, 'unit_price'))) !!}
                                                                    @else
                                                                        {!! currencyExchange(0) !!}
                                                                    @endif
                                                                </div>
                                                            </div>-
                                                            <div class="d-flex">
                                                                <div>
                                                                    @if (max(array_column($v_price, 'unit_price')) > 0)
                                                                        {!! currencyExchange(max(array_column($v_price, 'unit_price'))) !!}
                                                                    @else
                                                                        {!! currencyExchange(0) !!}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <!--End Unit Price-->
                                            <!--Discount-->
                                            @if (getEcommerceSetting('enable_product_discount') == config('settings.general_status.active'))
                                                <div class="d-flex product discount">
                                                    <strong>{{ translate('Discount') }}: </strong>
                                                    <div class="ml-1">
                                                        <div class="d-flex">
                                                            <div>
                                                                @if ($product->discount_amount != null)
                                                                    @if ($product->discount_type == config('tlecommercecore.amount_type.flat'))
                                                                        {!! currencyExchange($product->discount_amount) !!}
                                                                    @else
                                                                        {{ $product->discount_amount }}%
                                                                    @endif
                                                                @else
                                                                    <p class="badge badge-danger">
                                                                        {{ translate('No discount') }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <!--End discount-->
                                            <!--Product rating-->
                                            <div class="d-flex product-rating">
                                                <strong>{{ translate('Rating') }}: </strong>
                                                <div class="ml-1">
                                                    <div class="product-rating-wrapper">
                                                        <i data-star="{{ $product->avg_rating }}"
                                                            title="{{ $product->avg_rating }}"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--End product rating-->
                                            <!--Quick action-->
                                            <div class="d-flex action-area gap-10">
                                                @if (getEcommerceSetting('enable_product_discount') == config('settings.general_status.active'))
                                                    <a href="#" class="btn-link quick-action"
                                                        data-id="{{ $product->id }}" data-action="edit_discount">
                                                        @if ($product->discount_amount != null)
                                                            {{ translate('Edit discount') }}
                                                        @else
                                                            {{ translate('Set discount') }}
                                                        @endif
                                                    </a>
                                                @endif
                                                <a href="#" class="btn-link quick-action"
                                                    data-id="{{ $product->id }}" data-action="edit_price">
                                                    {{ translate('Update price') }}
                                                </a>
                                            </div>
                                            <!--End quick action-->
                                        </td>
                                        <!--End product information-->
                                        <td class="text-capitalize">
                                            <div class="stock">

                                                @if ($product->has_variant == config('tlecommercecore.product_variant.single'))
                                                    <strong>{{ translate('Stock') }}: </strong>
                                                    {{ $product->single_price->quantity > 0 ? $product->single_price->quantity : 0 }}
                                                    <span>
                                                        @if ($product->unit_info != null)
                                                            {{ $product->unit_info->translation('name', getLocale()) }}
                                                        @endif
                                                    </span>
                                                    @if ($product->single_price->quantity <= $product->low_stock_quantity_alert)
                                                        @if ($product->single_price->quantity == 0)
                                                            <p class="badge badge-danger">
                                                                {{ translate('Out Of Stock') }}</p>
                                                        @else
                                                            <p class="badge badge-warning">
                                                                {{ translate('Low Stock') }}</p>
                                                        @endif
                                                    @endif
                                                @else
                                                    <strong>
                                                        <p>{{ translate('Stock') }}: </p>
                                                    </strong>
                                                    @php
                                                        $v_prices = $product->variations;
                                                    @endphp
                                                    @foreach ($v_prices as $key => $combination)
                                                        @php
                                                            $variant_array = explode('/', trim($combination->variant, '/'));
                                                            $name = '';
                                                            foreach ($variant_array as $com_key => $variant) {
                                                                $variant_com_array = explode(':', $variant);
                                                                if ($variant_com_array[0] === 'color') {
                                                                    $option_name = translate('Color');
                                                                    $choice_name = \Plugin\TlcommerceCore\Models\Colors::find($variant_com_array[1])->translation('name');
                                                                } else {
                                                                    $option_property = \Plugin\TlcommerceCore\Models\ProductAttribute::select(['id', 'name'])->find($variant_com_array[0]);
                                                                    $option_name = $option_property != null ? $option_property->translation('name') : '';
                                                                    $choice_property = \Plugin\TlcommerceCore\Models\AttributeValues::select(['id', 'name'])->find($variant_com_array[1]);
                                                                    $choice_name = $choice_property != null ? $choice_property->name : '';
                                                                }
                                                                $name .= $option_name . ' : ' . $choice_name . ' | ';
                                                            }
                                                        @endphp
                                                        {{ trim($name, ' | ') }} -
                                                        {{ $combination->quantity > 0 ? $combination->quantity : 0 }}
                                                        <span>
                                                            @if ($product->unit_info != null)
                                                                {{ $product->unit_info->translation('name', getLocale()) }}
                                                            @endif
                                                        </span>
                                                        @if ($combination->quantity <= $product->low_stock_quantity_alert)
                                                            @if ($combination->quantity == 0)
                                                                <p class="badge badge-danger mb-0">
                                                                    {{ translate('Out Of Stock') }}</p>
                                                            @else
                                                                <p class="badge badge-warning mb-0">
                                                                    {{ translate('Low Stock') }}</p>
                                                            @endif
                                                        @endif
                                                        <br>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="d-flex num-of-sale">
                                                <strong>{{ translate('Num of Sale') }}: </strong>
                                                <div class="ml-1">
                                                    <div class="d-flex">
                                                        <div>
                                                            {{ $product->total_sale }}
                                                        </div>
                                                        <div class="ml-1">
                                                            @if ($product->unit_info != null)
                                                                {{ $product->unit_info->translation('name', getLocale()) }}
                                                            @else
                                                                {{ translate('Times') }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Quick action-->
                                            <div class="d-flex action-area gap-10">

                                                <a href="#" class="btn-link quick-action"
                                                    data-id="{{ $product->id }}" data-action="edit_stock">
                                                    {{ translate('Update stock') }}
                                                </a>
                                            </div>
                                            <!--End quick action-->
                                        </td>
                                        <td>
                                            <label class="switch glow primary medium">
                                                <input type="checkbox" class="change-featured"
                                                    data-product="{{ $product->id }}"
                                                    {{ $product->is_featured == config('settings.general_status.active') ? 'checked' : '' }}>
                                                <span class="control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="switch glow primary medium">
                                                <input type="checkbox" class="change-status"
                                                    data-product="{{ $product->id }}"
                                                    {{ $product->status == config('settings.general_status.active') ? 'checked' : '' }}>
                                                <span class="control"></span>
                                            </label>
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
                                                        href="{{ route('plugin.tlcommercecore.product.edit', ['id' => $product->id, 'lang' => getDefaultLang()]) }}">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                    <a href="#" class="delete-product"
                                                        data-product="{{ $product->id }}">{{ translate('Delete') }}</a>
                                                    <a href="/products/{{ $product->permalink }}?preview=1"
                                                        target="_blank">
                                                        {{ translate('Preview') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {{ $products->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') }}
                    </div>
                </div>
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
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.product.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-product-id" name="id">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->
    <!--Quick Action Modal-->
    <div id="quick-action-modal" class="quick-action-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Update Product Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-content-html">

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--End Quick Action Modal-->
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            /**
             * Quick action
             * 
             **/
            $('.quick-action').on('click', function(e) {
                e.preventDefault();
                $(".modal-content-html").html('');
                let action = $(this).data('action');
                let id = $(this).data('id');
                $.post('{{ route('plugin.tlcommercecore.product.quick.action.modal.view') }}', {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    id: id
                }, function(data) {
                    $(".modal-content-html").html(data);
                    $("#quick-action-modal").modal('show');
                })
            });
            /**
             * 
             * Bulk action
             **/
            $('.fire-bulk-action').on('click', function(e) {
                let action = $('.bulk-action-selection').val();
                if (action != 'null') {
                    var selected_items = [];
                    $('input[name^="product_id"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.tlcommercecore.product.bulk.action') }}', {
                            _token: '{{ csrf_token() }}',
                            items: selected_items,
                            action: action
                        }, function(data) {
                            location.reload();
                        })
                    } else {
                        toastr.error('{{ translate('No Item Selected') }}');
                    }
                } else {
                    toastr.error('{{ translate('No Action Selected') }}');
                }
            });
            /**
             * 
             * Change featured  status 
             * 
             * */
            $('.change-featured').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('product');
                $.post('{{ route('plugin.tlcommercecore.product.status.featured.update') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })

            });
            /**
             * 
             * Change  status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('product');
                $.post('{{ route('plugin.tlcommercecore.product.status.update') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
                })

            });
            /**
             * 
             * Delete product
             * 
             * */
            $('.delete-product').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('product');
                $("#delete-product-id").val(id);
                $('#delete-modal').modal('show');
            });
            /**
             * 
             * Select all product
             **/
            $('.select-all').on('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".product-id").prop("checked", true);
                } else {
                    $(".product-id").prop("checked", false);
                }
            });
        })(jQuery);
    </script>
@endsection
