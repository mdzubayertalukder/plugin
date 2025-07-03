@php
    if ($order_details->payment_method == config('tlecommercecore.payment_methods.bank')) {
        $bank_details = $order_details->bank_transaction;
    } else {
        $bank_details = null;
    }

@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Order Details') }}
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
@section('main_content')
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
                        <button class="btn btn-success long rounded" data-toggle="modal"
                            data-target="#order-shipping-label-modal">{{ translate('Print Shipping Label') }}</button>
                    </div>
                    <div class="invoice-btn">
                        <button data-toggle="modal" data-target="#order-invoice-print-modal"
                            class="btn btn-info long rounded">{{ translate('Print Invoice') }}</button>
                    </div>
                </div>
            </div>
            <!-- End Invoice Header -->
            <!-- Order details -->
            <div class="bg-white invoice-pd mb-30">
                <div class="row">
                    @php
                        $canCancelOrAccept = $order_details->adminCancelOrAcceptOrder();
                    @endphp
                    <!--Action area-->
                    <div
                        class="col-12 d-flex filter-area {{ $canCancelOrAccept == config('settings.general_status.active') ? 'justify-content-between' : 'justify-content-end' }} mb-20 px-2">
                        @if ($canCancelOrAccept == config('settings.general_status.active'))
                            <div class="d-flex gap-10 left-side">
                                <form method="POST" action="{{ route('plugin.tlcommercecore.orders.accept') }}">
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
                            <button class="btn long rounded btn-info status-update-modal-open-button"
                                data-order="{{ $order_details->id }}">{{ translate('Update Order status') }}
                            </button>
                        </div>

                    </div>
                    <!--End actions area-->
                    <!-- Order Details -->
                    <div class="{{ $order_details->billing_details != null ? 'col-xl-3' : 'col-xl-4' }} col-md-6 mb-30">
                        <div class="invoice payment-details mt-5 mt-xl-0">
                            <div class="invoice-title c4 bold font-14 mb-3 black">{{ translate('Order Details') }}:</div>
                            <ul class="status-list">
                                <li>
                                    <span class="black font-17 black bold">{{ $order_details->order_code }}</span>
                                </li>
                                <li><span class="key">{{ translate('Date') }}</span> <span
                                        class="black">{{ $order_details->created_at->format('d M y h:i A') }}</span>
                                </li>
                                <li><span class="key">{{ translate('Total') }}</span> <span
                                        class="black">{!! currencyExchange($order_details->total_payable_amount) !!}</span>
                                </li>
                                <li><span class="key">{{ translate('Paid by') }} </span>
                                    @if ($order_details->wallet_payment == config('settings.general_status.active'))
                                        <span class="black">
                                            {{ translate('Wallet') }}
                                        </span>
                                    @else
                                        <span class="black">
                                            {{ $order_details->payment_method_info?->name }}
                                            @if ($bank_details != null)
                                                <button class="badge badge-success p-1" data-toggle="modal"
                                                    data-target="#bank-payment-details" title="Bank Payment details">
                                                    {{ translate('View Details') }}
                                                </button>
                                            @endif
                                        </span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Order Details -->
                    <!--Pickup point details-->
                    @if (isActivePluging('pickuppoint') && $order_details->pickup_point != null)
                        <div
                            class="{{ $order_details->billing_details != null ? 'col-xl-3' : 'col-xl-4' }} col-md-6 mb-30">
                            <div class="invoice invoice-form">
                                <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Pickup Point') }}</div>
                                @if (isActivePluging('pickuppoint'))
                                    <ul class="list-invoice">
                                        <li class="bold black font-17">{{ $order_details->pickup_point->name }}</li>
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
                                <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Shipping Info') }}</div>

                                <ul class="list-invoice">
                                    <li class="bold black font-17">{{ $order_details->shipping_details->name }}</li>
                                    <li class="location mb-0">
                                        {{ $order_details->shipping_details->address }},
                                        @if ($order_details->shipping_details->city != null)
                                            {{ $order_details->shipping_details->city->translation('name') }} <br>
                                        @endif
                                        @if ($order_details->shipping_details->state != null)
                                            {{ $order_details->shipping_details->state->translation('name') }},
                                        @endif
                                        @if ($order_details->shipping_details->country != null)
                                            {{ $order_details->shipping_details->country->translation('name') }}
                                        @endif
                                    </li>
                                    <li class="mb-0">
                                        <a href="#">{{ translate('Postal Code:') }}
                                            {{ $order_details->shipping_details->postal_code }}
                                        </a>
                                    </li>
                                    <li class="call mb-0">
                                        <a href="#">{{ translate('Phone:') }}
                                            {{ $order_details->shipping_details->phone }}
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
                                <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Billing Info') }}</div>

                                <ul class="list-invoice">
                                    <li class="bold black font-17">{{ $order_details->billing_details->name }}</li>
                                    <li class="location mb-0">
                                        {{ $order_details->billing_details->address }},
                                        @if ($order_details->billing_details->city != null)
                                            {{ $order_details->billing_details->city->translation('name') }}<br>
                                        @endif
                                        @if ($order_details->billing_details->state != null)
                                            {{ $order_details->billing_details->state->translation('name') }},
                                        @endif
                                        @if ($order_details->billing_details->country != null)
                                            {{ $order_details->billing_details->country->translation('name') }}
                                        @endif
                                    </li>
                                    <li class="mb-0">
                                        <a href="#">{{ translate('Postal Code:') }}
                                            {{ $order_details->billing_details->postal_code }}
                                        </a>
                                    </li>
                                    <li class="call mb-0">
                                        <a href="#">{{ translate('Phone:') }}
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
                    <div class="col-xl-3 col-md-6 mb-30">
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
                <div class="card mb-30">
                    <div class="bg-white border-bottom2 card-header d-flex justify-content-between flex-wrap">
                        <div class="package-title">
                            <h4>{{ translate('Package') }}-{{ $key + 1 }}</h4>
                        </div>
                        <!--Shipping-->
                        @if ($product->shipping_rate_info != null)
                            <div class="shipping-info d-flex">
                                <span class="black">{{ translate('Shipping') }}:</span>
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
                                        <p class="alert alert-danger">{{ translate('This item has been cancelled') }}</p>
                                    </div>
                                @endif
                                <!--End order steps-->
                                <!--Order tracking history-->
                                @if ($product->product_tracking != null && count($product->product_tracking) > 0)
                                    <div
                                        class="border-bottom-0 d-flex justify-content-between order-status-details pb-0 tracking-header">
                                        <div class="item">
                                            <div class="details-item d-flex">
                                                <div class="time black">{{ $product->product_tracking[0]->date }}</div>
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
                                                        <div class="image m-w-70"><img src="{{ $product->image }}"
                                                                alt="{{ $product->product_details->name }}"
                                                                class="img-70 rounded"></div>
                                                        <div class="title">
                                                            <h5>{{ $product->product_details->name }}</h5>
                                                            @if ($product->variant_id != null)
                                                                <p class="black font-13 mb-0 mt-1 text-capitalize">
                                                                    {{ $product->variant_id }}</p>
                                                            @endif
                                                            <!--Seller Info-->
                                                            @if (isActivePluging('multivendor'))
                                                                @php
                                                                    $seller_info = $product->seller();
                                                                @endphp
                                                                @if ($seller_info != null)
                                                                    @if ($seller_info->id != getSupperAdminId())
                                                                        <p class="black font-13 mb-0 mt-1">
                                                                            {{ translate('Seller ') }} <a
                                                                                href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $seller_info->id]) }}"
                                                                                target="_blank"
                                                                                class="link-btn">{{ $seller_info->name }}</a>
                                                                        </p>
                                                                    @else
                                                                        <p class="black font-13 mb-0 mt-1 text-primary">
                                                                            {{ translate('Inhouse Product') }}</p>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                            <!--End Seller info-->
                                                            <!--Order Attachment-->
                                                            @if ($product->attachment != null)
                                                                <a href="{{ getFilePath($product->attachment) }}"
                                                                    class="font-14 font-weight-normal link-btn"
                                                                    target="_blank">{{ translate('Download attatchment') }}</a>
                                                            @endif
                                                            <!--End Order Attachment-->
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between">
                                                    <div class="price"><span>{!! currencyExchange($product->unit_price) !!}
                                                            X {{ $product->quantity }}</span></div>
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
                                        data-item="{{ $product->id }}">Cancel Item</a>
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
                                                {!! currencyExchange($order_details->sub_total) !!}
                                            </span>
                                        </strong>
                                    </td>
                                </tr>
                                <tr class="cart-tax">
                                    <td>{{ translate('Shipping') }}</td>
                                    <td>
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($order_details->total_delivery_cost) !!}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="cart-tax">
                                    <td>{{ translate('Tax') }}</td>
                                    <td>
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($order_details->total_tax) !!}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="cart-tax border-1">
                                    <td>{{ translate('Coupon Discount') }}</td>
                                    <td>
                                        <span class="Price-currencySymbol">-</span>
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($order_details->total_discount) !!}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="cart-subtotal">
                                    <th class="border-0">{{ translate('Total Payable') }}</th>
                                    <th class="border-0">
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($order_details->total_payable_amount) !!}
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
                    <h4 class="modal-title h6 bold">{{ translate('Update Order Status') }}</h4>
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
                                <div class="col-12 col-lg-6 mb-20">
                                    <div class="product-select-box">
                                        <label class="d-flex justify-content-between m-0 py-1">
                                            <div class="label-title">
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

                                            </div>
                                            <input type="checkbox" class="order-product-id" value="{{ $product->id }}"
                                                @disabled($product->delivery_status == config('tlecommercecore.order_delivery_status.delivered')) data-item="{{ $product }}"
                                                name="product[]">
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
                            <div class="form-group col-12 col-lg-6 mb-20">
                                <label class="font-14 bold black">{{ translate('Delivery Status') }}<span
                                        class="text text-danger">*</span></label>
                                <select class="theme-input-style" name="delivery_status" id="delivery_status">
                                    <option value="">{{ translate('Select delivery status') }}</option>
                                    <option value="{{ config('tlecommercecore.order_delivery_status.pending') }}">
                                        {{ translate('Pending') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.order_delivery_status.processing') }}">
                                        {{ translate('Processing') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.order_delivery_status.ready_to_ship') }}">
                                        {{ translate('Ready to ship') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.order_delivery_status.shipped') }}">
                                        {{ translate('Shipped') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.order_delivery_status.delivered') }}">
                                        {{ translate('Delivered') }}
                                    </option>
                                    <option value="{{ config('tlecommercecore.order_delivery_status.cancelled') }}">
                                        {{ translate('Cancelled') }}
                                    </option>
                                </select>
                                <div class="delivery-status-error invalid-input"></div>
                            </div>
                            <!--End delivery status-->
                            <!--Payment status-->
                            <div class="form-group col-12 col-lg-6">
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
                                <button class="btn long update-order-status rounded">{{ translate('Update') }}</button>
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.orders.cancel') }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                        <button type="submit" class="btn long mt-2">{{ translate('Confirm') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End cancel order modal-->

    <!--Bank Payment Details Modal-->
    <div id="bank-payment-details" class="order-cancel-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Bank Payment Details') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="status-list">
                        @if ($bank_details != null)
                            <li>
                                <span class="key col-6">{{ translate('Account Name') }}</span>
                                <span class="black col-4 text-right">{{ $bank_details->account_name }}</span>
                            </li>
                            <li>
                                <span class="key col-4">{{ translate('Account Number') }}</span>
                                <span class="black col-4 text-right">{{ $bank_details->account_number }}</span>
                            </li>
                            <li>
                                <span class="key col-4">{{ translate('Bank Name') }}</span>
                                <span class="black col-4 text-right">{{ $bank_details->bank_name }}</span>
                            </li>
                            <li>
                                <span class="key col-4">{{ translate('Branch Name') }}</span>
                                <span class="black col-4 text-right">{{ $bank_details->branch_name }}</span>
                            </li>
                            <li>
                                <span class="key col-6">{{ translate('Teansaction Number') }}</span>
                                <span class="black col-6 text-right">{{ $bank_details->transaction_number }}</span>
                            </li>
                            @if ($bank_details->receipt != null)
                                <li>
                                    <span class="key col-4">{{ translate('Receipt') }}</span>
                                    <a href="{{ getFilePath($bank_details->receipt) }}"
                                        class="col-4 btn sm btn-info">{{ translate('Download') }}</a>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--End Bank Payment Details Modal-->

    <!--Cancel order modal-->
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.orders.item.cancel') }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                        <input type="hidden" name="item_id" id="cancel-item-id" value="">
                        <button type="submit" class="btn long mt-2">{{ translate('Confirm') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End cancel order modal-->
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.orders.print.shipping.label') }}">
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
                                    <label for="selectAllShippingLabelItems">{{ translate('Select All') }}</label>
                                </div>
                            </div>
                            <div class="invalid-input mb-1 mt-0 products-error"></div>
                            @foreach ($order_details->products as $key => $product)
                                <div class="col-12 col-lg-12 mb-20">
                                    <div class="product-select-box">
                                        <label class="d-flex justify-content-between m-0 py-1">
                                            <div class="label-title">
                                                @if ($product->product_details != null)
                                                    <div class="align-items-center d-flex product-info">
                                                        <div class="image">
                                                            <img src="{{ $product->image }}"
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

                                            </div>
                                            <input type="checkbox" class="shipping-label-product"
                                                @disabled($product->delivery_status == config('tlecommercecore.order_delivery_status.cancelled')) value="{{ $product->id }}"
                                                data-item="{{ $product }}" name="shipping_label_products[]">
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
                                            <div class="label-title">
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

                                            </div>
                                            <input type="checkbox" class="invoice-product" value="{{ $product->id }}"
                                                @disabled($product->delivery_status == config('tlecommercecore.order_delivery_status.cancelled')) data-item="{{ $product }}"
                                                name="invoice_products[]">
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
    <!--End invoice print modal-->
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
                $(".payment-status-error").html('');
                $(".delivery-status-error").html('');
                let errors = [];
                let payment_status = $("#payment_status").val();
                let delivery_status = $("#delivery_status").val();

                var selected_items = [];
                $('input[name^="product"]:checked').each(function() {
                    selected_items.push($(this).data('item'));
                });

                if (selected_items.length < 1) {
                    $(".products-error").html('Please select  product ');
                    errors.push('products_error');
                }


                if (!payment_status) {
                    $(".payment-status-error").html('Please select a payment status');
                    errors.push('payment_error');
                }

                if (!delivery_status) {
                    $(".delivery-status-error").html('Please select a delivery status');
                    errors.push('delivery_error');
                }


                if (delivery_status == {{ config('tlecommercecore.order_delivery_status.delivered') }} &&
                    payment_status !=
                    {{ config('tlecommercecore.order_payment_status.paid') }}) {
                    $(".payment-status-error").html('please make payment before delivered');
                    errors.push('payment_delivery_error');
                }

                if (errors.length == 0) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        type: "POST",
                        data: $('#order-status-update-form').serialize(),
                        url: '{{ route('plugin.tlcommercecore.orders.status.update') }}',
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
