@php
    $total_earning = \Plugin\Multivendor\Models\SellerEarnings::where('seller_id', $seller_details->id)
        ->where('status', config('tlecommercecore.seller_earning_status.approve'))
        ->sum('earning');
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Seller Details') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
    <link href="{{ asset('/public/backend/assets/css/ratings.css') }}" rel="stylesheet" />
    <style>
        .product-title {
            max-width: 150px;
            display: inline-block;
        }

        .details-card {
            height: calc(100% - 30px);
        }

        .profile-action-area {
            position: absolute;
            top: 10px;
            right: 10px
        }
    </style>
@endsection
@section('main_content')
    <!--Customer details-->
    <div class="row">
        <!--Seller Details-->
        <div class="col-lg-3 col-12 col-sm-6">
            <div class="card mb-30 details-card p-2">

                <div class="align-items-start d-flex justify-content-center position-relative">
                    <img src="{{ asset(getFilePath($seller_details->image)) }}" alt="{{ $seller_details->name }}"
                        class="img-100 m-2 rounded-circle">
                    <div class="profile-action-area">
                        <button data-toggle="modal" data-target="#seller-edit-modal" class="btn btn-link"
                            title="{{ translate('Edit Seller') }}">
                            <i class="icofont-edit-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="align-items-center d-flex justify-content-center mb-1 mb-2 pt-1 text-center">
                        <div>
                            <a href="#">
                                <h4>{{ $seller_details->name }} <span
                                        class="font-12 font-weight-normal">({{ $seller_details->uid }})</span></h4>
                            </a>
                            <div class="seller-rating">
                                <div class="product-rating-wrapper">
                                    <i data-star="{{ $reviews->avg('rating') }}" title="{{ $reviews->avg('rating') }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contact-body text-center">
                        <p class="mb-0">{{ translate('Email') }}: {{ $seller_details->email }}</p>
                        @if ($seller_details->shop != null)
                            <p class="mb-0">{{ translate('Phone') }}:{{ $seller_details->shop->seller_phone }}</p>
                        @endif
                        <p class="mb-0"><span>{{ translate('Registered Date') }}:</span>
                            {{ $seller_details->created_at->format('d M Y') }}
                        </p>
                        <div class="align-items-center d-flex gap-10 justify-content-center mb-0 mt-1">
                            {{ translate('Status') }}:
                            <label class="switch glow primary medium">
                                <input type="checkbox" class="change-seller-status" data-seller="{{ $seller_details->id }}"
                                    {{ $seller_details->status == config('settings.general_status.active') ? 'checked' : '' }}>
                                <span class="control"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Seller Details-->
        <div class="col-lg-6 col-12 col-sm-6">
            <div class="row">
                <div class="col-xl-6 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-5 state2">
                            <h4 class="font-14 mb-2">{{ translate('Total Sales') }}</h4>
                            <h2>{!! currencyExchange($total_sales) !!}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3  py-5 state2">
                            <h4 class="font-14 mb-2">{{ translate('Total Earning') }}</h4>
                            <h2>{!! currencyExchange($total_earning) !!}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Products') }}</h4>
                            <h2>{{ $products->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Orders') }}</h4>
                            <h2>{{ $orders->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Refunds') }}</h4>
                            <h2>{{ $refunds->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Reviews') }}</h4>
                            <h2>{{ $reviews->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Shop Details-->
        @if ($seller_details->shop != null)
            <div class="col-lg-3 col-12 col-sm-6">
                <div class="card mb-30 details-card p-2">
                    <div class="align-items-start d-flex justify-content-center position-relative">
                        <img src="{{ asset(getFilePath($seller_details->shop->logo)) }}"
                            alt="{{ $seller_details->shop->shop_name }}" class="img-100 m-2 rounded-circle">
                        <div class="profile-action-area">
                            <button data-toggle="modal" data-target="#shop-edit-modal" class="btn btn-link"
                                title="{{ translate('Edit Shop') }}">
                                <i class="icofont-edit-alt"></i>
                            </button>
                            <a href="/shop/{{ $seller_details->shop->shop_slug }}" target="_blank"
                                title="{{ translate('Visit Shop') }}" class="ml-2 btn-link text-primary">
                                <i class="icofont-web"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="align-items-center d-flex justify-content-center mb-1 mb-2 pt-1 text-center">
                            <div>
                                <a href="#">
                                    <h4>{{ $seller_details->shop->shop_name }}</h4>
                                </a>
                            </div>
                        </div>

                        <div class="contact-body text-center">
                            @if ($seller_details->shop != null)
                                <p class="mb-0">{{ translate('Phone') }}: {{ $seller_details->shop->shop_phone }}</p>
                            @endif
                            @if ($seller_details->shop->shop_address != null)
                                <p class="mb-0">{{ translate('Address') }}: {{ $seller_details->shop->shop_address }}
                                </p>
                            @endif

                            <div class="align-items-center d-flex gap-10 justify-content-center mb-0 mt-1">
                                {{ translate('Status') }}:
                                <label class="switch glow primary medium">
                                    <input type="checkbox" class="change-shop-status"
                                        data-shop="{{ $seller_details->shop->id }}"
                                        {{ $seller_details->shop->status == config('settings.general_status.active') ? 'checked' : '' }}>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!--End shop Details page-->
    </div>
    <!--End customer details-->

    <!--Tab Content-->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="Orders-tab" data-toggle="tab" href="#products" role="tab"
                        aria-controls="products" aria-selected="true">{{ translate('Products') }}</a>
                </li>
                @can('Manage Refund Requests')
                    <li class="nav-item">
                        <a class="nav-link" id="orders-tab" data-toggle="tab" href="#orders" role="tab"
                            aria-controls="orders" aria-selected="false">{{ translate('Orders') }}</a>
                    </li>
                @endcan
                @if (isActivePluging('refund'))
                    @can('Manage Refund Requests')
                        <li class="nav-item">
                            <a class="nav-link" id="returnRequest-tab" data-toggle="tab" href="#returnRequest"
                                role="tab" aria-controls="returnRequest"
                                aria-selected="false">{{ translate('Return Requests') }}</a>
                        </li>
                    @endcan
                @endif
                @can('Manage Refund Requests')
                    <li class="nav-item">
                        <a class="nav-link" id="payoutRequest-tab" data-toggle="tab" href="#payoutRequest" role="tab"
                            aria-controls="payoutRequest" aria-selected="false">{{ translate('Payout Requests') }}</a>
                    </li>
                @endcan
                @can('Manage Refund Requests')
                    <li class="nav-item">
                        <a class="nav-link" id="payouts-tab" data-toggle="tab" href="#payouts" role="tab"
                            aria-controls="payouts" aria-selected="false">{{ translate('Payouts') }}</a>
                    </li>
                @endcan
                @can('Manage Product Reviews')
                    <li class="nav-item">
                        <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab"
                            aria-controls="button" aria-selected="false">{{ translate('Product Reviews') }}</a>
                    </li>
                @endcan
            </ul>
            <div class="tab-content" id="myTabContent">
                <!--Seller Products List-->
                <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                    <div class="bg-white py-3 table-responsive">
                        <table id="productTable" class="hoverable">
                            <thead>
                                <tr>

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
                                @foreach ($products as $key => $product)
                                    <tr>
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
                                            <div class="d-flex unit-price"><strong>{{ translate('Unit Price') }}:
                                                </strong>
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
                                                <a href="#" class="btn-link quick-action"
                                                    data-id="{{ $product->id }}" data-action="edit_discount">
                                                    @if ($product->discount_amount != null)
                                                        {{ translate('Edit discount') }}
                                                    @else
                                                        {{ translate('Set discount') }}
                                                    @endif
                                                </a>
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
                                                        <p class="badge badge-danger">{{ translate('Low') }}</p>
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
                                                                $name .= $option_name . ' : ' . $choice_name . ' / ';
                                                            }
                                                        @endphp
                                                        {{ trim($name, ' / ') }} -
                                                        {{ $combination->quantity > 0 ? $combination->quantity : 0 }}
                                                        <span>
                                                            @if ($product->unit_info != null)
                                                                {{ $product->unit_info->translation('name', getLocale()) }}
                                                            @endif
                                                        </span>
                                                        @if ($combination->quantity <= $product->low_stock_quantity_alert)
                                                            <p class="badge badge-danger">{{ translate('Low') }}</p>
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
                                                <input type="checkbox" class="change-product-featured"
                                                    data-product="{{ $product->id }}"
                                                    {{ $product->is_featured == config('settings.general_status.active') ? 'checked' : '' }}>
                                                <span class="control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="switch glow primary medium">
                                                <input type="checkbox" class="change-product-status"
                                                    data-product="{{ $product->id }}"
                                                    {{ $product->status == '1' ? 'checked' : '' }}>
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
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--End Seller Products List-->
                <!--Seller Orders List-->
                <div class="tab-pane fade show" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                    <div class="bg-white py-3 table-responsive">
                        <table class="style--three table-centered text-nowrap order-table">
                            <thead>
                                <tr>
                                    <th>{{ translate('Order ID') }}</th>
                                    <th>{{ translate('Date') }}</th>
                                    <th>{{ translate('Customer') }}</th>
                                    <th>{{ translate('Total Amount') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $order->id]) }}">{{ $order->order_code }}</a>
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y h:i A') }}</td>
                                        <td>
                                            @if ($order->customer_info != null)
                                                <p>{{ $order->customer_info->name }}</p>
                                            @else
                                                <p>{{ $order->guest_customer->name }}
                                                    <span class="badge badge-info ml-1">{{ translate('Guest') }}</span>
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $total_payable_amount = 0;
                                                foreach ($order->products as $product) {
                                                    $total_payable_amount = $total_payable_amount + $product->totalPayableAmount();
                                                }
                                            @endphp
                                            {!! currencyExchange($total_payable_amount) !!}
                                        </td>
                                        <td>
                                            <a href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $order->id]) }}"
                                                class="details-btn">
                                                Details
                                                <i class="icofont-arrow-right"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--End Seller Orders List-->
                <!--Seller refunds lists-->
                @if (isActivePluging('refund'))
                    @can('Manage Refund Requests')
                        <div class="tab-pane fade" id="returnRequest" role="tabpanel" aria-labelledby="returnRequest-tab">
                            <div class="bg-white py-3 table-responsive">
                                <table id="returnRequestTable" class="hoverable text-nowrap border-top2">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ translate('Code') }}</th>
                                            <th>{{ translate('Return Date') }}</th>
                                            <th>{{ translate('Order Code') }}</th>
                                            <th>{{ translate('Total Amount') }}</th>
                                            <th>{{ translate('Return Status') }}</th>
                                            <th>{{ translate('Payment Status') }}</th>

                                            <th class="text-right">{{ translate('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($refunds as $key => $refund)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('plugin.refund.request.details', ['id' => $refund->id]) }}">
                                                        {{ $refund->refund_code }}
                                                    </a>
                                                </td>
                                                <td>{{ $refund->created_at }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $refund->order_id]) }}">
                                                        {{ $refund->order->order_code }}
                                                    </a>
                                                </td>

                                                <td>{!! currencyExchange($refund->total_refund_amount) !!}</td>
                                                <td>
                                                    @if ($refund->return_status == config('tlecommercecore.return_request_status.processing'))
                                                        <span class="badge badge-info">Processing</span>
                                                    @elseif ($refund->return_status == config('tlecommercecore.return_request_status.cancelled'))
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    @elseif ($refund->return_status == config('tlecommercecore.return_request_status.approved'))
                                                        <span class="badge badge-success">Returned</span>
                                                    @else
                                                        <span class="badge badge-primary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($refund->payment_status == config('tlecommercecore.return_request_payment_status.refunded'))
                                                        <span class="badge badge-success">Refunded</span>
                                                    @else
                                                        <span class="badge badge-danger">Pending</span>
                                                    @endif
                                                <td>
                                                    <div class="dropdown-button">
                                                        <a href="#"
                                                            class="d-flex align-items-center justify-content-end"
                                                            data-toggle="dropdown">
                                                            <div class="menu-icon mr-0">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </div>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a
                                                                href="{{ route('plugin.refund.request.details', ['id' => $refund->id]) }}">{{ translate('Details') }}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endcan
                @endif
                <!--End seller refund list-->
                <!--Seller payout requests -->
                <div class="tab-pane fade show" id="payoutRequest" role="tabpanel" aria-labelledby="payoutRequest-tab">
                    <div class="bg-white py-3 table-responsive">
                        <table class="hoverable text-nowrap border-top2" id="payoutRequestTable">
                            <thead>
                                <tr>
                                    <th>{{ translate('Date') }}</th>
                                    <th>{{ translate('Requested Amount') }}</th>
                                    <th>{{ translate('Mesage') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payouts->where('status', '!=', config('multivendor.payout_request_status.accepted')) as $key => $request)
                                    <tr>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>{!! currencyExchange($request->amount) !!} </td>
                                        <td class="text-wrap">{{ $request->message }}</td>
                                        <td>
                                            @if ($request->status == config('multivendor.payout_request_status.accepted'))
                                                <p class="badge badge-success">{{ translate('Accepted') }}</p>
                                            @endif
                                            @if ($request->status == config('multivendor.payout_request_status.pending'))
                                                <p class="badge badge-info">{{ translate('pending') }}</p>
                                            @endif
                                            @if ($request->status == config('multivendor.payout_request_status.cancelled'))
                                                <p class="badge badge-danger">{{ translate('cancelled') }}</p>
                                            @endif
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
                                                    <a href="#" class="request-details"
                                                        data-request="{{ $request->id }}">{{ translate('Update Status') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--End seller payout requests-->
                <!--Seller payout lists-->
                <div class="tab-pane fade show" id="payouts" role="tabpanel" aria-labelledby="payouts-tab">
                    <div class="bg-white py-3 table-responsive">
                        <table class="hoverable text-nowrap border-top2" id="payoutsTable">
                            <thead>
                                <tr>
                                    <th>{{ translate('Request Date') }}</th>
                                    <th>{{ translate('Payment Date') }}</th>
                                    <th>{{ translate('Paid Amount') }}</th>
                                    <th>{{ translate('Paid By') }}</th>
                                    <th>{{ translate('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payouts->where('status', config('multivendor.payout_request_status.accepted')) as $key => $request)
                                    <tr>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if ($request->payment_date != null)
                                                {{ $request->payment_date->format('d M Y') }}
                                            @endif
                                        </td>
                                        <td>{!! currencyExchange($request->amount) !!} </td>
                                        <td class="text-wrap">
                                            @if ($request->payment_method == config('multivendor.seller_payment_methods.bank_transfer'))
                                                {{ translate('Bank Payment') }}
                                                [{{ translate('TRX ID : ') }} {{ $request->transaction_number }}]
                                            @endif
                                        </td>
                                        <td class="text-wrap">{{ $request->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--End seller payout list-->
                <!--Seller product reviews-->
                <div class="tab-pane fade show" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div class="bg-white py-3 table-responsive">
                        <table id="reviewTable" class="hoverable text-nowrap border-top2">
                            <thead>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>{{ translate('Product') }}</th>
                                    <th>{{ translate('Customer') }}</th>
                                    <th>{{ translate('Order') }}</th>
                                    <th>{{ translate('Rating') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th>{{ translate('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reviews as $key => $review)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            <a href="{{ route('plugin.tlcommercecore.product.edit', ['id' => $review->product_id, 'lang' => getDefaultLang()]) }}"
                                                target="_blank">
                                                {{ $review->product_name }}
                                            </a>

                                        </td>
                                        <td>
                                            {{ $review->customer_name }}
                                        </td>
                                        <td>
                                            <a href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $review->order_id]) }}"
                                                target="_blank">
                                                {{ $review->order_code }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="product-rating-wrapper">
                                                <i data-star="{{ $review->rating }}"
                                                    title="{{ $review->rating }}"></i><span>{{ $review->rating }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($review->status == config('settings.general_status.active'))
                                                <p class="badge badge-success">{{ translate('Visible') }}</p>
                                            @else
                                                <p class="badge badge-danger">{{ translate('Hidden') }}</p>
                                            @endif
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
                                                    <a href="#" class="review-details"
                                                        data-review="{{ $review->id }}">
                                                        {{ translate('Details') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--End seller product reviews-->

            </div>
        </div>
    </div>
    <!--End tab content-->

    <!--Payout Request details Modal-->
    <div id="request-details-modal" class="request-details-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Payout Request Information') }}</h4>
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
    <!--End Payout Request details Modal-->
    <!--Seller Edit Modal-->
    <div id="seller-edit-modal" class="seller-edit-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 font-weight-bold">{{ translate('Seller Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <form id="seller-info-edit-form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $seller_details->id }}">
                        <input type="hidden" name="is_for_profile" value="true">
                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Profile Picture') }}</label>
                            </div>
                            <div class="col-md-8">
                                @include('core::base.includes.media.media_input', [
                                    'input' => 'pro_pic',
                                    'data' => $seller_details->image,
                                ])
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Name') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="name" class="theme-input-style"
                                    value="{{ $seller_details->name }}"
                                    placeholder="{{ translate('Enter Seller Name') }}">
                                @if ($errors->has('name'))
                                    <div class="invalid-input">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Email') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="email" name="email" class="theme-input-style"
                                    value="{{ $seller_details->email }}"
                                    placeholder="{{ translate('Enter Seller Email') }}">
                                @if ($errors->has('email'))
                                    <div class="invalid-input">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Phone') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="phone" class="theme-input-style"
                                    value="{{ $seller_details->shop->seller_phone }}"
                                    placeholder="{{ translate('Give your phone') }}">
                                @if ($errors->has('phone'))
                                    <div class="invalid-input">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Password') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="password" class="theme-input-style"
                                    placeholder="{{ translate('Enter New Password') }}">
                                @if ($errors->has('password'))
                                    <div class="invalid-input">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Confirm Password') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" name="password_confirmation" class="theme-input-style"
                                    placeholder="{{ translate('Confirm your password') }}">
                                @if ($errors->has('password_confirmation'))
                                    <div class="invalid-input">{{ $errors->first('password_confirmation') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button type="btn"
                                    class="btn long seller-update-btn">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End seller Edit Modal-->
    <!--Shop Edit Modal-->
    <div id="shop-edit-modal" class="shop-edit-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 font-weight-bold">{{ translate('Shop Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <form id="shop-info-edit-form">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Shop Name') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="hidden" name="id" value="{{ $seller_details->shop->id }}">
                                <input type="text" name="shop_name" class="theme-input-style"
                                    placeholder="{{ translate('Enter Shop Name') }}"
                                    value="{{ $seller_details->shop->shop_name }}" required>
                                @if ($errors->has('shop_name'))
                                    <div class="invalid-input">{{ $errors->first('shop_name') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Shop Link') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <a href="{{ url('/shop') }}/{{ $seller_details->shop->shop_slug }}"
                                    target="_blank">{{ url('') }}/shop/<span
                                        id="permalink">{{ $seller_details->shop->shop_slug }}</span>
                                    <span class="btn custom-btn ml-1 permalink-edit-btn">
                                        {{ translate('Edit') }}
                                    </span>
                                </a>
                                <input type="hidden" name="shop_slug" id="permalink_input_field"
                                    value="{{ $seller_details->shop->shop_slug }}" required>
                                <div class="permalink-editor d-none">
                                    <input type="text" class="theme-input-style" id="permalink-updated-input"
                                        placeholder="{{ translate('Type here') }}">
                                    <button type="button"
                                        class="btn long mt-2 btn-danger permalink-cancel-btn">{{ translate('Cancel') }}</button>
                                    <button type="button"
                                        class="btn long mt-2 permalink-save-btn">{{ translate('Save') }}</button>
                                </div>

                                @if ($errors->has('shop_slug'))
                                    <div class="invalid-input">{{ $errors->first('shop_slug') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Shop Logo') }} </label>
                            </div>
                            <div class="col-md-8">
                                @include('core::base.includes.media.media_input', [
                                    'input' => 'shop_logo',
                                    'data' => $seller_details->shop->logo,
                                ])
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Shop Banner') }} </label>
                            </div>
                            <div class="col-md-8">
                                @include('core::base.includes.media.media_input', [
                                    'input' => 'shop_banner',
                                    'data' => $seller_details->shop->shop_banner,
                                ])
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Shop Phone') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="shop_phone" class="theme-input-style"
                                    placeholder="{{ translate('Enter Shop Phone') }}"
                                    value="{{ $seller_details->shop->shop_phone }}" required>
                                @if ($errors->has('shop_phone'))
                                    <div class="invalid-input">{{ $errors->first('shop_phone') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-sm-3">
                                <label class="font-14 bold black">{{ translate('Shop Address') }} </label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="shop_address" class="theme-input-style"
                                    placeholder="{{ translate('Enter Shop Address') }}"
                                    value="{{ $seller_details->shop->shop_address }}">
                                @if ($errors->has('shop_address'))
                                    <div class="invalid-input">{{ $errors->first('shop_address') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button type="btn"
                                    class="btn long shop-update-btn">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Shop Edit Modal-->
    <!--Review Details Modal-->
    <div id="review-details-modal" class="review-details-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 font-weight-bold">{{ translate('Review Details') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="detail-content"></div>
                </div>
            </div>
        </div>
    </div>
    <!--Review Details Modal-->

    <!--Product Quick Action Modal-->
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
                    <div class="product-quick-action-modal-content-html">

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--End Product Quick Action Modal-->
    @include('core::base.media.partial.media_modal')
@endsection
@section('custom_scripts')
    @include('core::base.includes.data_table.script')
    <script>
        (function($) {
            "use strict";
            initDropzone();
            /**
             * products table
             */
            $("#productTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            });
            /**
             * Order table
             */
            $(".order-table").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            });
            //returnRequestTable
            $("#returnRequestTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            });
            /**
             * Product review table
             * 
             **/
            $("#reviewTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            });
            /**
             * payouts table
             * 
             **/
            $("#payoutsTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            });
            /**
             * payout requests table
             * 
             **/
            $("#payoutRequestTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            });
            /**
             * 
             * Change seller status 
             * 
             * */
            $('.change-seller-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('seller');
                $.post('{{ route('plugin.multivendor.admin.seller.list.change.status') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        toastr.success("Status updated successfully");
                        location.reload();
                    } else {
                        toastr.error('Status update failed');
                    }
                })

            });
            /**
             * 
             * Change shop  status 
             * 
             * */
            $('.change-shop-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('shop');
                $.post('{{ route('plugin.multivendor.admin.seller.list.change.shop.status') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    if (data.success) {
                        toastr.success("Status updated successfully");
                        location.reload();
                    } else {
                        toastr.error('Status update failed');
                    }
                })

            });
            /**
             *Payout Request Details
             * 
             **/
            $('.request-details').on('click', function(e) {
                e.preventDefault();
                $(".modal-content-html").html('');
                let id = $(this).data('request');
                $.post('{{ route('plugin.multivendor.admin.seller.payout.requests.details') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    $(".modal-content-html").html(data.data);
                    $("#request-details-modal").modal('show');
                })
            });

            /**
             * Get review details
             **/
            $('.review-details').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('review');
                $.post('{{ route('plugin.tlcommercecore.product.reviews.details') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    $('.detail-content').html(data);
                    $('#review-details-modal').modal('show');
                })
            });
            /**
             * Product Quick action
             * 
             **/
            $('.quick-action').on('click', function(e) {
                e.preventDefault();
                $(".product-quick-action-modal-content-html").html('');
                let action = $(this).data('action');
                let id = $(this).data('id');
                $.post('{{ route('plugin.tlcommercecore.product.quick.action.modal.view') }}', {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    id: id
                }, function(data) {
                    $(".product-quick-action-modal-content-html").html(data);
                    $("#quick-action-modal").modal('show');
                })
            });
            /**
             * 
             * Change product status 
             * 
             * */
            $('.change-product-status').on('click', function(e) {
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
             * Change product featured  status 
             * 
             * */
            $('.change-product-featured').on('click', function(e) {
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
             * seller update
             * 
             **/
            $('.seller-update-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').html("");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#seller-info-edit-form').serialize(),
                    url: '{{ route('plugin.multivendor.admin.seller.update') }}',
                    success: function(data) {
                        if (data.success) {
                            toastr.success(
                                '{{ translate('Seller information updated successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ translate('Seller update faled') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status == 422) {

                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').closest(
                                    '.theme-input-style').after(
                                    '<div class="invalid-input ">' +
                                    error + '</div>')
                            })
                        } else {
                            toastr.error('{{ translate('Seller update failed') }}');
                        }
                    }
                });
            });
            /*edit permalink*/
            $(".permalink-edit-btn").on("click", function(e) {
                e.preventDefault();
                let permalink = $("#permalink").html();
                $("#permalink-updated-input").val(permalink);
                $(".permalink-edit-btn").addClass("d-none");
                $(".permalink-editor").removeClass("d-none");
            });
            /*Cancel permalink edit*/
            $(".permalink-cancel-btn").on("click", function(e) {
                e.preventDefault();
                $("#permalink-updated-input").val();
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /*Update permalink*/
            $(".permalink-save-btn").on("click", function(e) {
                e.preventDefault();
                let input = $("#permalink-updated-input").val();
                let updated_permalink = string_to_slug(input);
                $("#permalink_input_field").val(updated_permalink);
                $("#permalink").html(updated_permalink);
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /**
             * Shop update
             * 
             **/
            $('.shop-update-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').html("");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#shop-info-edit-form').serialize(),
                    url: '{{ route('plugin.multivendor.admin.seller.shop.update') }}',
                    success: function(data) {
                        if (data.success) {
                            toastr.success(
                                '{{ translate('Shop information updated successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ translate('Shop update faled') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status == 422) {

                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').closest(
                                    '.theme-input-style').after(
                                    '<div class="invalid-input ">' +
                                    error + '</div>')
                            })
                        } else {
                            toastr.error('{{ translate('Shop update failed') }}');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
