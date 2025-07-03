@php
    $subtotal = 0;
    $total_delivery_cost = 0;
    $total_tax = 0;
    $total_coupon_discount = 0;
@endphp
@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Order Details') }} {{ $order_details != null ? $order_details->order_code : '' }}
@endsection
@section('custom_css')
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <style>
        .status-list li span.badge {
            line-height: unset;
        }

        .product-title {
            display: block;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .m-w-70 {
            min-width: 70px !important;
        }

        .product-border {
            border: 1px solid #d8dbe0 !important;
        }
    </style>
@endsection
@section('seller_main_content')
    @if (auth()->user()->shop->status == config('settings.general_status.active'))
        @if ($order_details != null && $order_details->products->count() > 0)
            <div class="row">
                <div class="col-12">
                    <!-- Invoice Header -->
                    <div
                        class="invoice-details-header bg-white d-flex align-items-sm-center flex-column flex-sm-row mb-30 justify-content-sm-between">
                        <div class="d-flex align-items-center">
                            <h2 class="regular mr-3 font-30">{{ translate('Order') }}</h2>
                            <h4 class="c4">{{ $order_details->order_code }}</h4>
                        </div>

                        <div class="d-flex flex-wrap gap-10 invoice-header-right justify-content-around mt-3 mt-sm-0">
                            <div class="shipping-lebel-btn">
                                <button class="btn long rounded" data-toggle="modal"
                                    data-target="#order-shipping-label-modal">{{ translate('Print Shipping Label') }}</button>
                            </div>
                            <div class="invoice-btn">
                                <button data-toggle="modal" data-target="#order-invoice-print-modal"
                                    class="btn long rounded">{{ translate('Print Invoice') }}</button>
                            </div>
                        </div>
                    </div>
                    <!-- End Invoice Header -->
                    <!-- Order details -->
                    <div class="bg-white invoice-pd mb-30">
                        <div class="row">
                            @php
                                $canCancelOrAccept = $order_details->sellerCancelOrAcceptOrder(auth()->user()->id);
                            @endphp
                            <!--Action area-->
                            <div
                                class="col-12 d-flex filter-area {{ $canCancelOrAccept == config('settings.general_status.active') ? 'justify-content-between' : 'justify-content-end' }} mb-20 px-2">
                                @if ($canCancelOrAccept == config('settings.general_status.active'))
                                    <div class="d-flex gap-10 left-side">
                                        <form method="POST"
                                            action="{{ route('plugin.multivendor.seller.dashboard.order.accept') }}">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                                            <button class="btn long rounded btn-success">{{ translate('Accept Order') }}
                                            </button>
                                        </form>
                                        <button class="btn long rounded btn-danger" data-toggle="modal"
                                            data-target="#order-cancel-modal" title="Cancel Order">
                                            {{ translate('Cancel Order') }}
                                        </button>
                                    </div>
                                @endif
                                <div class="right-side">
                                    <button class="btn long rounded status-update-modal-open-button"
                                        data-order="{{ $order_details->id }}">{{ translate('Update delivery status') }}
                                    </button>
                                </div>

                            </div>
                            <!--End actions area-->
                            <!-- Order Details -->
                            <div
                                class="{{ $order_details->billing_details != null ? 'col-xl-3' : 'col-xl-4' }} col-md-6 mb-30">
                                <div class="invoice payment-details mt-5 mt-xl-0">
                                    <div class="invoice-title c4 bold font-14 mb-3 black">{{ translate('Order Details') }}:
                                    </div>
                                    <ul class="status-list">
                                        <li>
                                            <span class="black font-17 black bold">{{ $order_details->order_code }}</span>
                                        </li>
                                        <li><span class="key">{{ translate('Date') }}</span> <span
                                                class="black">{{ $order_details->created_at->format('d M y h:i A') }}</span>
                                        </li>
                                        <li><span class="key">{{ translate('Paid by') }}</span>
                                            @if ($order_details->wallet_payment == config('settings.general_status.active'))
                                                <span class="black">
                                                    {{ translate('Wallet') }}
                                                </span>
                                            @else
                                                <span class="black">
                                                    {{ $order_details->payment_method_info->name }}
                                                </span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- End Order Details -->
                            <!--Pickup point details-->
                            @if ($order_details->pickup_point != null)
                                <div
                                    class="{{ $order_details->billing_details != null ? 'col-xl-3' : 'col-xl-4' }} col-md-6 mb-30">
                                    <div class="invoice invoice-form">
                                        <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Pickup Point') }}
                                        </div>
                                        @if (isActivePluging('pickuppoint'))
                                            <ul class="list-invoice">
                                                <li class="bold black font-17">{{ $order_details->pickup_point->name }}
                                                </li>
                                                <li class="location">
                                                    {{ $order_details->pickup_point->location }}
                                                </li>
                                                <li class="call">
                                                    {{ $order_details->pickup_point->phone }}
                                                </li>
                                            </ul>
                                        @else
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <!--End pickup point details-->
                            <!--Shipping info-->
                            @if ($order_details->shipping_details != null)
                                <div
                                    class="{{ $order_details->billing_details != null ? 'col-xl-3' : 'col-xl-4' }} col-md-6 mb-30">
                                    <div class="invoice invoice-form">
                                        <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Shipping Info') }}
                                        </div>

                                        <ul class="list-invoice">
                                            <li class="bold black font-17">{{ $order_details->shipping_details->name }}
                                            </li>
                                            <li class="location">
                                                {{ $order_details->shipping_details->address }},{{ $order_details->shipping_details->city->name }}<br>
                                                {{ $order_details->shipping_details->state->name }},{{ $order_details->shipping_details->country->name }}
                                            </li>
                                            <li class="call">
                                                <a href="#"> {{ $order_details->shipping_details->phone }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            <!--End shipping info-->
                            <!--Billing info-->
                            @if ($order_details->billing_details != null)
                                <div class="col-xl-3 col-md-6 mb-30">
                                    <!-- Invoice Form -->
                                    <div class="invoice invoice-form">
                                        <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Billing Info') }}
                                        </div>

                                        <ul class="list-invoice">
                                            <li class="bold black font-17">{{ $order_details->billing_details->name }}</li>
                                            <li class="location">
                                                {{ $order_details->billing_details->address }},{{ $order_details->billing_details->city->name }}<br>
                                                {{ $order_details->billing_details->state->name }},{{ $order_details->billing_details->country->name }}
                                            </li>
                                            <li class="call">
                                                <a href="#">
                                                    {{ $order_details->billing_details->phone }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- End Invoice Form -->
                                </div>
                            @endif
                            <!--End Billing info-->
                            <!-- Customer info -->
                            <div
                                class="{{ $order_details->billing_details != null ? 'col-xl-3' : 'col-xl-4' }} col-md-6 mb-30">
                                <div class="invoice payment-details mt-5 mt-xl-0">
                                    <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Customer') }}</div>
                                    @if ($order_details->customer_info != null)
                                        <ul class="status-list">
                                            <li>
                                                <a href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $order_details->customer_info->id]) }}"
                                                    class="black font-17 black bold">{{ $order_details->customer_info->name }}</a>
                                            </li>
                                            <li>
                                                <span class="key">{{ translate('Uid') }}</span>
                                                <span class="black">{{ $order_details->customer_info->uid }}</span>
                                            </li>
                                            <li>
                                                <span class="key">{{ translate('Email') }}</span>
                                                <span class="black">{{ $order_details->customer_info->email }}</span>
                                            </li>
                                            <li>
                                                <span class="key">{{ translate('Phone') }}</span>
                                                <span class="black">
                                                    {{ $order_details->customer_info->phone }}
                                                </span>
                                            </li>
                                        </ul>
                                    @else
                                        @if ($order_details->guest_customer != null)
                                            <ul class="status-list">
                                                <li>
                                                    {{ $order_details->guest_customer->name }}
                                                    <span class="badge badge-info ml-10">Guest</span>
                                                </li>
                                                <li>
                                                    <span class="key">{{ translate('Email') }}</span>
                                                    <span class="black">{{ $order_details->guest_customer->email }}</span>
                                                </li>
                                            </ul>
                                        @else
                                            <p class="alert alert-danger">{{ translate('No Customer details found') }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <!-- End customer info -->
                        </div>
                    </div>
                    <!-- End order details -->
                </div>
                <!--Packages-->
                <div class="col-12 col-lg-8">
                    <!-- Product list -->
                    @foreach ($order_details->products as $key => $product)
                        @php
                            $subtotal = $subtotal + $product->unit_price * $product->quantity;
                            $total_delivery_cost = $total_delivery_cost + $product->delivery_cost;
                            $total_tax = $total_tax + $product->tax;
                            $total_coupon_discount = $total_coupon_discount + $product->couponDiscountedAmount();
                        @endphp
                        <div class="card mb-30">
                            <div class="bg-white border-bottom2 card-header d-flex justify-content-between flex-wrap">
                                <div class="package-title">
                                    <h4>{{ translate('Product') }}-{{ $key + 1 }}</h4>
                                </div>
                                <!--Shipping-->
                                @if ($product->shipping_rate_info != null)
                                    <div class="shipping-info d-flex">
                                        <span class="black">{{ translate('Shipping fee') }}:</span>
                                        <span>
                                            {!! currencyExchange($product->delivery_cost) !!}
                                        </span>

                                        <span>(
                                            @if ($product->shipping_rate_info->carrier_id != null)
                                                @if ($product->shipping_rate_info->carrier != null)
                                                    <span class="black">
                                                        {{ $product->shipping_rate_info->carrier['name'] }}</span>
                                                @endif
                                            @else
                                                <span> {{ $product->shipping_rate_info->name }}</span>
                                            @endif
                                            @if ($product->shipping_rate_info->shipping_medium != null)
                                                <span>{{ translate('Via') }}</span>
                                                <span>{{ $product->shipping_rate_info->shippied_by() }}</span>
                                            @endif
                                            )
                                        </span>
                                    </div>
                                @endif
                                <!--End shipping-->
                            </div>
                            <div class="card-body">
                                <div class="row px-12">
                                    <!--Delivery steps and tracking info-->
                                    <div class="col-md-12 mb-20">
                                        <!--Order steps-->
                                        @if ($product->delivery_status != config('tlecommercecore.order_delivery_status.cancelled'))
                                            <div class="order-status-range">
                                                <ul class="progressbar d-flex">
                                                    <li
                                                        class="{{ $product->delivery_status == config('tlecommercecore.order_delivery_status.pending') || $product->delivery_status == config('tlecommercecore.order_delivery_status.processing') || $product->delivery_status == config('tlecommercecore.order_delivery_status.ready_to_ship') || $product->delivery_status == config('tlecommercecore.order_delivery_status.shipped') || $product->delivery_status == config('tlecommercecore.order_delivery_status.delivered') ? 'active' : '' }}">
                                                        @if ($product->delivery_status == config('tlecommercecore.order_delivery_status.pending'))
                                                            {{ translate('Pending') }}
                                                        @else
                                                            {{ $product->delivery_status == config('tlecommercecore.order_delivery_status.processing') ? translate('Processing') : translate('Ready to ship') }}
                                                        @endif
                                                    </li>

                                                    <li
                                                        class="{{ $product->delivery_status == config('tlecommercecore.order_delivery_status.shipped') || $product->delivery_status == config('tlecommercecore.order_delivery_status.delivered') ? 'active' : '' }}">
                                                        {{ translate(' Shipped') }}
                                                    </li>
                                                    <li
                                                        class="{{ $product->delivery_status == config('tlecommercecore.order_delivery_status.delivered') ? 'active' : '' }}">
                                                        {{ translate('Delivered') }}
                                                    </li>
                                                </ul>
                                            </div>
                                        @else
                                            <div class="mb-20">
                                                <p class="alert alert-danger">
                                                    {{ translate('This item has been cancelled') }}
                                                </p>
                                            </div>
                                        @endif
                                        <!--End order steps-->
                                        <!--Order tracking history-->
                                        @if ($product->product_tracking != null && count($product->product_tracking) > 0)
                                            <div
                                                class="border-bottom-0 d-flex justify-content-between order-status-details pb-0 tracking-header">
                                                <div class="item">
                                                    <div class="details-item d-flex">
                                                        <div class="time black">{{ $product->product_tracking[0]->date }}
                                                        </div>
                                                        <div class="text text-dark ml-10">{!! xss_clean($product->product_tracking[0]->message) !!}</div>
                                                    </div>
                                                </div>
                                                @if (count($product->product_tracking) > 1)
                                                    <div class="toogle-items">
                                                        <a href="#" data-toggle="collapse"
                                                            data-target="item-body-{{ $product->id }}"
                                                            class="tracking-toogle-button">
                                                            <i class="icofont-plus"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div id="item-body-{{ $product->id }}"
                                                class="border-top-0 hidden items order-status-details pt-0">
                                                @php
                                                    
                                                    $first_key = $product->product_tracking->keys()->first();
                                                @endphp
                                                @foreach ($product->product_tracking->forget($first_key) as $tracking)
                                                    <div class="item">
                                                        <div class="details-item d-flex">
                                                            <div class="time black">{{ $tracking->date }}</div>
                                                            <div class="text text-dark ml-10">{!! xss_clean($tracking->message) !!}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <!--End order tracking history-->
                                    </div>
                                    <!--End Delivery steps and tracking info-->
                                    <!--Product info-->
                                    <div class="col-md-12">
                                        <div class="product-list-group">
                                            <div class="product-list p-3 product-border">
                                                <!--Product info-->
                                                <div class="align-items-center product-information row">
                                                    <div class="col-md-6">
                                                        @if ($product->product_details != null)
                                                            <div class="align-items-center d-flex product-info">
                                                                <div class="image m-w-70"><img
                                                                        src="{{ $product->image }}"
                                                                        alt="{{ $product->product_details->name }}"
                                                                        class="img-70 rounded"></div>
                                                                <div class="title">
                                                                    <h5>{{ $product->product_details->name }}</h5>
                                                                    @if ($product->variant_id != null)
                                                                        <p class="black font-13 mb-0 mt-1 text-capitalize">
                                                                            {{ $product->variant_id }}</p>
                                                                    @endif
                                                                    @if ($product->attachment != null)
                                                                        <a href="{{ getFilePath($product->attachment) }}"
                                                                            class="font-14 font-weight-normal link-btn"
                                                                            target="_blank">{{ translate('Download attatchment') }}</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="price">
                                                                <span>{!! currencyExchange($product->unit_price) !!}
                                                                    X {{ $product->quantity }}</span>
                                                            </div>
                                                            <div class="price">
                                                                <span>{!! currencyExchange($product->unit_price * $product->quantity) !!}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--End product info-->
                                            </div>
                                        </div>
                                    </div>
                                    <!--End product info-->
                                    <!--Cancel item-->
                                    @if ($product->delivery_status == config('tlecommercecore.order_delivery_status.pending'))
                                        <div class="col-12 d-flex justify-content-end mt-10">
                                            <a href="#" class="btn long rounded btn-danger cancel-item"
                                                data-item="{{ $product->id }}">{{ translate('Cancel Item') }}</a>
                                        </div>
                                    @endif
                                    <!--End cancel item-->
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- End product list -->
                    <!--Order Note-->
                    @if ($order_details->note != null)
                        <div class="card mb-30">
                            <div class="card-header bg-white border-bottom2">
                                <div class="d-sm-flex justify-content-between align-items-center">
                                    <h4>{{ translate('Order Note') }}</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>{{ $order_details->note }}</p>
                            </div>
                        </div>
                    @endif
                    <!--End order note-->
                </div>
                <!--End packages-->
                <!--Order Summary-->
                <div class="col-12 col-lg-4">
                    <div class="card mb-30">
                        <div class="card-header bg-white border-bottom2">
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <h4>{{ translate('Order Summary') }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="cart_totals calculated_shipping">
                                <table class="text-right shop_table style-two">
                                    <tbody>
                                        <tr class="order-total">
                                            <td>{{ translate('Subtotal') }}</td>
                                            <td>
                                                <strong>
                                                    <span class="Price-amount amount">
                                                        {!! currencyExchange($subtotal) !!}
                                                    </span>
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr class="cart-tax">
                                            <td>{{ translate('Shipping fee') }}</td>
                                            <td>
                                                <span class="Price-amount amount">
                                                    {!! currencyExchange($total_delivery_cost) !!}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="cart-tax">
                                            <td>{{ translate('Tax') }}</td>
                                            <td>
                                                <span class="Price-amount amount">
                                                    {!! currencyExchange($total_tax) !!}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="cart-tax border-1">
                                            <td>{{ translate('Coupon Discount') }}</td>
                                            <td>
                                                <span class="Price-currencySymbol">-</span>
                                                <span class="Price-amount amount">
                                                    {!! currencyExchange($total_coupon_discount) !!}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="cart-subtotal">
                                            <th class="border-0">{{ translate('Total Payable') }}</th>
                                            <th class="border-0">
                                                @php
                                                    $total_payable = $subtotal + $total_tax + $total_delivery_cost - $total_coupon_discount;
                                                @endphp
                                                <span class="Price-amount amount">
                                                    {!! currencyExchange($total_payable) !!}
                                                </span>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End order Summary-->

            </div>

            <!--Update  status modal-->
            <div id="order-status--update-modal" class="order-status--update-modal modal fade show" aria-modal="true"
                role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title h6 bold">{{ translate('Update delivery Status') }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pt-0">
                            <form id="order-status-update-form">
                                <p class="text-error"></p>
                                <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                                <!--Product checkbox-->
                                <div class="form-row">
                                    <div class="d-flex justify-content-between label w-100">
                                        <label class="font-14 bold black">{{ translate('Select Product') }}</label>
                                        <div class="d-flex align-items-center">
                                            <label class="custom-checkbox position-relative mr-2">
                                                <input type="checkbox" id="selectAllItems" name="select_all"
                                                    class="select-all-products">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label for="selectAllItems">{{ translate('Select All') }}</label>
                                        </div>
                                    </div>
                                    <div class="invalid-input mb-1 mt-0 products-error"></div>
                                    @foreach ($order_details->products as $key => $product)
                                        <div
                                            class="col-12 {{ $order_details->products->count() == 1 ? 'col-lg-12' : 'col-lg-6' }} mb-20">
                                            <div class="product-select-box">
                                                <label class="d-flex justify-content-between m-0 py-1">
                                                    <span class="label-title">
                                                        @if ($product->product_details != null)
                                                            <div class="align-items-center d-flex product-info">
                                                                <div class="image"><img src="{{ $product->image }}"
                                                                        alt="{{ $product->product_details->name }}"
                                                                        class="img-60 radius-0">
                                                                </div>
                                                                <div class="description">
                                                                    <div class="info">
                                                                        <h5 class="product-title"
                                                                            title="{{ $product->product_details->name }}">
                                                                            {{ $product->product_details->name }}</h5>
                                                                        <p class="mb-0 font-weight-normal">
                                                                            {{ $product->variant_id }}</p>
                                                                    </div>
                                                                    <div class="delivery-status">
                                                                        <span
                                                                            class="font-weight-light">{{ translate('Delivery status') }}:</span>
                                                                        <span
                                                                            class="badge {{ $product->delivery_status_label() }} text-capitalize">{{ $product->delivery_status_label() }}</span>

                                                                    </div>
                                                                    <div class="payment-status">
                                                                        <span
                                                                            class="font-weight-light">{{ translate('Payment status') }}:</span>
                                                                        <span
                                                                            class="badge {{ $product->payment_status_label() }} text-capitalize">{{ $product->payment_status_label() }}</span>

                                                                    </div>
                                                                    @if ($product->shipping_rate_info != null)
                                                                        <div class="shipping">
                                                                            <div class="shipping-info d-flex">
                                                                                <span
                                                                                    class="font-weight-light">{{ translate('Shipping') }}:</span>
                                                                                <span
                                                                                    class="font-weight-light">{!! currencyExchange($product->delivery_cost) !!}</span>
                                                                                <span class="font-weight-light d-flex">(
                                                                                    @if ($product->shipping_rate_info->carrier_id != null)
                                                                                        @if ($product->shipping_rate_info->carrier != null)
                                                                                            <span class="black">
                                                                                                {{ $product->shipping_rate_info->carrier['name'] }}</span>
                                                                                        @endif
                                                                                    @else
                                                                                        <span>
                                                                                            {{ $product->shipping_rate_info->name }}</span>
                                                                                    @endif
                                                                                    @if ($product->shipping_rate_info->shipping_medium != null)
                                                                                        <span>{{ translate('Via') }}</span>
                                                                                        <span>{{ $product->shipping_rate_info->shippied_by() }}</span>
                                                                                    @endif
                                                                                    )
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </span>
                                                    <input type="checkbox" class="order-product-id"
                                                        value="{{ $product->id }}" @disabled($product->delivery_status == config('tlecommercecore.order_delivery_status.delivered'))
                                                        data-item="{{ $product }}" name="product[]">
                                                </label>
                                                <div class="tracking-id tracking-id-{{ $product->id }}">
                                                    <label class="font-14 bold black">Tracking id</label>
                                                    <input type="text" value="{{ $product->tracking_id }}"
                                                        name="{{ $product->id }}-tracking" class="theme-input-style">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!--End product checkbox-->

                                <div class="form-row">
                                    <!--Delivery status-->
                                    <div class="form-group col-12  mb-20">
                                        <label class="font-14 bold black">{{ translate('Delivery Status') }}<span
                                                class="text text-danger">*</span></label>
                                        <select class="theme-input-style" name="delivery_status" id="delivery_status">
                                            <option value="">{{ translate('Select delivery status') }}</option>
                                            <option
                                                value="{{ config('tlecommercecore.order_delivery_status.ready_to_ship') }}">
                                                {{ translate('Ready to ship') }}
                                            </option>
                                            <option
                                                value="{{ config('tlecommercecore.order_delivery_status.shipped') }}">
                                                {{ translate('Shipped') }}
                                            </option>
                                            <option
                                                value="{{ config('tlecommercecore.order_delivery_status.cancelled') }}">
                                                {{ translate('Cancelled') }}
                                            </option>
                                        </select>
                                        <div class="delivery-status-error invalid-input"></div>
                                    </div>
                                    <!--End delivery status-->
                                    <!--Payment status-->
                                    <div class="form-group col-12 d-none">
                                        <label class="font-14 bold black">{{ translate('Payment Status') }}<span
                                                class="text text-danger">*</span></label>
                                        <select class="theme-input-style" name="payment_status" id="payment_status">
                                            <option value="">{{ translate('Select payment status') }}</option>
                                            <option value="{{ config('tlecommercecore.order_payment_status.unpaid') }}">
                                                {{ translate('Unpaid') }}
                                            </option>
                                            <option value="{{ config('tlecommercecore.order_payment_status.paid') }}">
                                                {{ translate('Paid') }}
                                            </option>
                                        </select>
                                        <div class="payment-status-error invalid-input"></div>
                                    </div>
                                    <!--End payment status-->
                                </div>
                                <!--Comment-->
                                <div class="form-row mb-20">
                                    <label class="font-14 bold black col-12">{{ translate('Comment') }}</label>
                                    <div class="editor-wrap col-12">
                                        <textarea name="comment" id="order-comment" class="theme-input-style h-25" rows="2"></textarea>
                                    </div>
                                </div>
                                <!--End comment-->
                                <div class="form-row">
                                    <div class="col-12 text-right">
                                        <button
                                            class="btn long update-order-status rounded">{{ translate('Update') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End  status modal-->
            <!--Cancel order modal-->
            <div id="order-cancel-modal" class="order-cancel-modal modal fade show" aria-modal="true">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title h6">{{ translate('Cancel Confirmation') }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mt-1">{{ translate('Are you sure to cancel  this order') }}?</p>
                            <form method="POST"
                                action="{{ route('plugin.multivendor.seller.dashboard.order.cancel') }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                                <button type="submit" class="btn long mt-2">{{ translate('Confirm') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End cancel order modal-->
            <!--Cancel item modal-->
            <div id="item-cancel-modal" class="item-cancel-modal modal fade show" aria-modal="true">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title h6">{{ translate('Cancel Confirmation') }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mt-1">{{ translate('Are you sure to cancel  this item') }}?</p>
                            <form method="POST"
                                action="{{ route('plugin.multivendor.seller.dashboard.order.item.cancel') }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                                <input type="hidden" name="item_id" id="cancel-item-id" value="">
                                <button type="submit" class="btn long mt-2">{{ translate('Confirm') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End cancel item modal-->
            <!--Shipping label modal-->
            <div id="order-shipping-label-modal" class="order-shipping-label-modal modal fade show" aria-modal="true"
                role="dialog">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title h6 bold">{{ translate('Shipping Label') }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pt-0">
                            <form method="POST"
                                action="{{ route('plugin.tlcommercecore.orders.print.shipping.label') }}">
                                @csrf
                                <p class="text-error"></p>
                                <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                                <!--Product checkbox-->
                                <div class="form-row">
                                    <div class="d-flex justify-content-between label w-100">
                                        <label class="font-14 bold black">{{ translate('Select Product') }}</label>
                                        <div class="d-flex align-items-center">
                                            <label class="custom-checkbox position-relative mr-2">
                                                <input type="checkbox" id="selectAllShippingLabelItems" name="select_all"
                                                    class="select-all-products-for-shipping-label">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label
                                                for="selectAllShippingLabelItems">{{ translate('Select All') }}</label>
                                        </div>
                                    </div>
                                    <div class="invalid-input mb-1 mt-0 products-error"></div>
                                    @foreach ($order_details->products as $key => $product)
                                        <div class="col-12 col-lg-12 mb-20">
                                            <div class="product-select-box">
                                                <label class="d-flex justify-content-between m-0 py-1">
                                                    <span class="label-title">
                                                        @if ($product->product_details != null)
                                                            <div class="align-items-center d-flex product-info">
                                                                <div class="image"><img src="{{ $product->image }}"
                                                                        alt="{{ $product->product_details->name }}"
                                                                        class="img-60 radius-0">
                                                                </div>
                                                                <div class="description">
                                                                    <div class="info">
                                                                        <h5>{{ $product->product_details->name }}</h5>
                                                                        <span>{{ $product->variant_id }}</span>
                                                                    </div>
                                                                    <div class="delivery-status">
                                                                        <span
                                                                            class="font-weight-light">{{ translate('Delivery status') }}:</span>
                                                                        <span
                                                                            class="badge {{ $product->delivery_status_label() }} text-capitalize">{{ $product->delivery_status_label() }}</span>

                                                                    </div>
                                                                    <div class="payment-status">
                                                                        <span
                                                                            class="font-weight-light">{{ translate('Payment status') }}:</span>
                                                                        <span
                                                                            class="badge {{ $product->payment_status_label() }} text-capitalize">{{ $product->payment_status_label() }}</span>

                                                                    </div>
                                                                    @if ($product->shipping_rate_info != null)
                                                                        <div class="shipping">
                                                                            <div class="shipping-info d-flex">
                                                                                <span
                                                                                    class="font-weight-light">{{ translate('Shipping') }}:</span>
                                                                                <span
                                                                                    class="font-weight-light">{!! currencyExchange($product->delivery_cost) !!}</span>
                                                                                <span class="font-weight-light">(
                                                                                    @if ($product->shipping_rate_info->carrier_id != null)
                                                                                        @if ($product->shipping_rate_info->carrier != null)
                                                                                            <span class="black">
                                                                                                {{ $product->shipping_rate_info->carrier['name'] }}</span>
                                                                                        @endif
                                                                                    @else
                                                                                        <span>
                                                                                            {{ $product->shipping_rate_info->name }}</span>
                                                                                    @endif
                                                                                    @if ($product->shipping_rate_info->shipping_medium != null)
                                                                                        <span>{{ translate('Via') }}</span>
                                                                                        <span>{{ $product->shipping_rate_info->shippied_by() }}</span>
                                                                                    @endif
                                                                                    )
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </span>
                                                    <input type="checkbox" class="shipping-label-product"
                                                        @disabled($product->delivery_status == config('tlecommercecore.order_delivery_status.cancelled')) value="{{ $product->id }}"
                                                        data-item="{{ $product }}"
                                                        name="shipping_label_products[]">
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!--End product checkbox-->
                                <div class="form-row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <input type="submit" name="action" value="preview"
                                            class="btn long btn-info rounded"></input>
                                        <input type="submit" name="action" value="download"
                                            class="btn long btn-success rounded"></input>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End  Shipping label modal-->
            <!--Invoice Print Modal-->
            <div id="order-invoice-print-modal" class="order-invoice-print-modal modal fade show" aria-modal="true"
                role="dialog">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title h6 bold">{{ translate('Print Invoice') }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body pt-0">
                            <form method="POST" action="{{ route('plugin.tlcommercecore.orders.print.invoice') }}">
                                @csrf
                                <p class="text-error"></p>
                                <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                                <!--Product checkbox-->
                                <div class="form-row">
                                    <div class="d-flex justify-content-between label w-100">
                                        <label class="font-14 bold black">{{ translate('Select Product') }}</label>
                                        <div class="d-flex align-items-center">
                                            <label class="custom-checkbox position-relative mr-2">
                                                <input type="checkbox" id="selectAllIvoiceItems" name="select_all"
                                                    class="select-all-products-for-print-invoice">
                                                <span class="checkmark"></span>
                                            </label>
                                            <label for="selectAllIvoiceItems">{{ translate('Select All') }}</label>
                                        </div>
                                    </div>
                                    <div class="invalid-input mb-1 mt-0 products-error"></div>
                                    @foreach ($order_details->products as $key => $product)
                                        <div class="col-12 col-lg-12 mb-20">
                                            <div class="product-select-box">
                                                <label class="d-flex justify-content-between m-0 py-1">
                                                    <span class="label-title">
                                                        @if ($product->product_details != null)
                                                            <div class="align-items-center d-flex product-info">
                                                                <div class="image"><img src="{{ $product->image }}"
                                                                        alt="{{ $product->product_details->name }}"
                                                                        class="img-60 radius-0">
                                                                </div>
                                                                <div class="description">
                                                                    <div class="info">
                                                                        <h5>{{ $product->product_details->name }}</h5>
                                                                        <span>{{ $product->variant_id }}</span>
                                                                    </div>
                                                                    <div class="delivery-status">
                                                                        <span
                                                                            class="font-weight-light">{{ translate('Delivery status') }}:</span>
                                                                        <span
                                                                            class="badge {{ $product->delivery_status_label() }} text-capitalize">{{ $product->delivery_status_label() }}</span>

                                                                    </div>
                                                                    <div class="payment-status">
                                                                        <span
                                                                            class="font-weight-light">{{ translate('Payment status') }}:</span>
                                                                        <span
                                                                            class="badge {{ $product->payment_status_label() }} text-capitalize">{{ $product->payment_status_label() }}</span>

                                                                    </div>
                                                                    @if ($product->shipping_rate_info != null)
                                                                        <div class="shipping">
                                                                            <div class="shipping-info d-flex">
                                                                                <span
                                                                                    class="font-weight-light">{{ translate('Shipping') }}:</span>
                                                                                <span
                                                                                    class="font-weight-light">{!! currencyExchange($product->delivery_cost) !!}</span>
                                                                                <span class="font-weight-light">(
                                                                                    @if ($product->shipping_rate_info->carrier_id != null)
                                                                                        @if ($product->shipping_rate_info->carrier != null)
                                                                                            <span class="black">
                                                                                                {{ $product->shipping_rate_info->carrier['name'] }}</span>
                                                                                        @endif
                                                                                    @else
                                                                                        <span>
                                                                                            {{ $product->shipping_rate_info->name }}</span>
                                                                                    @endif
                                                                                    @if ($product->shipping_rate_info->shipping_medium != null)
                                                                                        <span>{{ translate('Via') }}</span>
                                                                                        <span>{{ $product->shipping_rate_info->shippied_by() }}</span>
                                                                                    @endif
                                                                                    )
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </span>
                                                    <input type="checkbox" class="invoice-product"
                                                        value="{{ $product->id }}" @disabled($product->delivery_status == config('tlecommercecore.order_delivery_status.cancelled'))
                                                        data-item="{{ $product }}" name="invoice_products[]">
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!--End product checkbox-->
                                <div class="form-row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <input type="submit" name="action" value="preview"
                                            class="btn long btn-info rounded" />
                                        <input type="submit" name="action" value="download"
                                            class="btn long btn-success rounded" />

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End invoice print modal-->
        @else
            <p class="alert alert-danger text-center">{{ translate('Order not found') }} </p>
        @endif
    @else
        <p class="alert alert-info">Your Shop is Inactive. Please contact with Administration </p>
    @endif
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            /**    
             * Tracking toogle items
             * 
             **/
            $(".tracking-toogle-button").on('click', function(e) {
                $(".tracking-toogle-button i").toggleClass("icofont-plus icofont-minus");

            });
            /**
             * Open cancel item modal
             **/
            $('.cancel-item').on('click', function(e) {
                let id = $(this).data('item');
                $("#cancel-item-id").val(id);
                $("#item-cancel-modal").modal('show');
            });

            $("#order-comment").summernote({
                tabsize: 2,
                height: 200,
                codeviewIframeFilter: false,
                codeviewFilter: true,
                codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                toolbar: [
                    ["style", ["style"]],
                    ["font", ["fontname", "bold", "underline", "clear"]],
                    ["color", ["color"]],
                    ['insert', ['link']],
                    ["view", ["fullscreen", "codeview", "help"]],
                ],
                callbacks: {
                    onChangeCodeview: function(contents, $editable) {
                        let code = $(this).summernote('code')
                        code = code.replace(
                            /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                            '')
                        $(this).val(code)
                    }
                }
            });
            /**
             * Open status update modal
             **/
            $('.status-update-modal-open-button').on('click', function(e) {
                e.preventDefault();
                $(".payment-status-error").html('');
                $(".delivery-status-error").html('');
                $(".item-id").prop("checked", false);
                $(".select-all").prop("checked", false);
                $("#order-status--update-modal").modal('show');

            });
            /**
             * 
             * Select all products
             **/
            $('.select-all-products').on('change', function(e) {
                if ($('.select-all-products').is(":checked")) {
                    $(".order-product-id").prop("checked", true);
                } else {
                    $(".order-product-id").prop("checked", false);
                }
                checkPaymentAndDeliveryStatus();
            });
            /**
             * Select product for print invoice
             * 
             **/
            $('.select-all-products-for-print-invoice').on('change', function(e) {
                if ($('.select-all-products-for-print-invoice').is(":checked")) {
                    $(".invoice-product").prop("checked", true);
                } else {
                    $(".invoice-product").prop("checked", false);
                }
            });
            /**
             * Select all product for shipping label 
             * 
             * 
             **/
            $('.select-all-products-for-shipping-label').on('change', function(e) {
                if ($('.select-all-products-for-shipping-label').is(":checked")) {
                    $(".shipping-label-product").prop("checked", true);
                } else {
                    $(".shipping-label-product").prop("checked", false);
                }
            });
            /**
             * 
             * Select product and manage delivery and payment status
             * 
             **/
            $('.order-product-id').on('change', function(e) {
                checkPaymentAndDeliveryStatus();
            })
            /**
             * 
             * Select product and manage delivery and payment status
             * 
             **/
            function checkPaymentAndDeliveryStatus() {
                $(".payment-status-error").html('');
                $(".delivery-status-error").html('');
                var selected_items = [];
                $('input[name^="product"]:checked').each(function() {
                    selected_items.push($(this).data('item'));
                });

                if (selected_items.length > 0) {
                    if (selected_items.length == 1) {
                        $("#payment_status").val(selected_items[0].payment_status);
                        $("#delivery_status").val(selected_items[0].delivery_status);
                    } else {
                        //delivery status
                        let match_delivery_item = selected_items.filter(item => item.delivery_status == selected_items[
                                0]
                            .delivery_status)
                        if (match_delivery_item.length == selected_items.length) {
                            $("#delivery_status").val(selected_items[0].delivery_status);
                        } else {
                            $("#delivery_status").val("");
                            $(".delivery-status-error").html('your selected items have differrent delivery status');
                        }

                        //payment status
                        let match_payment_item = selected_items.filter(item => item.payment_status == selected_items[0]
                            .payment_status)
                        if (match_payment_item.length == selected_items.length) {
                            $("#payment_status").val(selected_items[0].payment_status);
                        } else {
                            $("#payment_status").val("");
                            $(".payment-status-error").html('your selected items have differrent payment status');
                        }
                    }

                } else {
                    $("#payment_status").val("");
                    $("#delivery_status").val("");
                }
            }
            /**
             * Will update delivery status
             * 
             **/
            $('.update-order-status').on('click', function(e) {
                e.preventDefault();
                $(".products-error").html('');
                $(".delivery-status-error").html('');
                let errors = [];
                let delivery_status = $("#delivery_status").val();

                var selected_items = [];
                $('input[name^="product"]:checked').each(function() {
                    selected_items.push($(this).data('item'));
                });

                if (selected_items.length < 1) {
                    $(".products-error").html('Please select  product ');
                    errors.push('products_error');
                }

                if (!delivery_status) {
                    $(".delivery-status-error").html('Please select a delivery status');
                    errors.push('delivery_error');
                }

                if (errors.length == 0) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        type: "POST",
                        data: $('#order-status-update-form').serialize(),
                        url: '{{ route('plugin.multivendor.seller.dashboard.order.status.update') }}',
                        success: function(response) {
                            if (response.success) {
                                toastr.success(
                                    '{{ translate('Order status updated successfully') }}');
                                $("#order-status--update-modal").modal('hide');
                                location.reload();
                            } else {
                                toastr.error('{{ translate('Update Failed ') }}');
                            }
                        },
                        error: function(response) {
                            toastr.error('{{ translate('Update Failed ') }}');
                        }
                    });
                }

            });
        })(jQuery);
    </script>
@endsection
