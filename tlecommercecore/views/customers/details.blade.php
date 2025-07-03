@php
    $active_tab = request()->has('tab') && request()->get('tab') != null ? request()->get('tab') : 'orders';
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Customer Details') }}
@endsection
@section('custom_css')
    @include('core::base.includes.data_table.css')
    <link href="{{ asset('/public/backend/assets/css/ratings.css') }}" rel="stylesheet" />
    <style>
        .details-card {
            height: calc(100% - 30px);
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <!--Customer details-->
        <div class="col-lg-3 col-12">
            <div class="card mb-30 details-card p-2">
                <div class="d-flex justify-content-center position-relative">
                    <img src="{{ asset(getFilePath($customer_details->image)) }}" alt="{{ $customer_details->name }}"
                        class="img-100 m-2 rounded-circle">
                </div>
                <div class="card-body pt-0">
                    <div class="align-items-center d-flex justify-content-center mb-1 mb-2 pt-1 text-center">
                        <div>
                            <a href="#">
                                <h4>{{ $customer_details->name }}
                                    <span class="font-12 font-weight-normal">({{ $customer_details->uid }})</span>
                                </h4>
                            </a>
                        </div>
                    </div>

                    <div class="contact-body text-center">
                        <p class="mb-0">{{ translate('Email') }}: {{ $customer_details->email }}</p>
                        <p class="mb-0">{{ translate('Phone') }}: {{ $customer_details->phone }}</p>
                        <p class="mb-0"><span>{{ translate('Registered Date') }}: </span>
                            {{ $customer_details->created_at->format('d M Y') }}
                        </p>
                        <div class="align-items-center d-flex gap-10 justify-content-center mb-0 mt-1">
                            {{ translate('Status') }}:
                            @if ($customer_details->status == config('settings.general_status.active'))
                                <p class="badge badge-success">{{ translate('Active') }}</p>
                            @else
                                <p class="badge badge-danger">{{ translate('Iactive') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End customer details-->
        <div
            class="{{ isActivePluging('wallet') &&auth()->user()->can('Manage Wallet Transactions')? 'col-lg-6': 'col-lg-9' }}   col-12">
            <div class="row">
                <div class="col-xl-8 col-md-8 col-sm-6">
                    <div class="card mb-30 py-3">
                        <div class="px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Total Purchase') }}</h4>
                            <h2>{!! currencyExchange($total_purchase) !!}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 col-sm-6">
                    <div class="card mb-30 py-3">
                        <div class="px-3 py-30 px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Total Orders') }}</h4>
                            <h2>{{ $customer_orders->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-30 px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Refunds') }}</h4>
                            <h2>{{ $return_requests->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Cancelled') }}</h4>
                            <h2>{{ $cancelled_orders }}</h2>
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
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card mb-30">
                        <div class="px-3 py-30 state2">
                            <h4 class="font-14 mb-2">{{ translate('Wishlists') }}</h4>
                            <h2>{{ $wishlists->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Customer Wallet-->
        @if (isActivePluging('wallet'))
            @can('Manage Wallet Transactions')
                <div class="col-lg-3 col-12">
                    <div class="card mb-30 p-2 details-card">
                        <div class="card-body">
                            <div class="mb-5">
                                <h4 class="font-14 mb-2">{{ translate('Available Wallet Balance') }}</h4>
                                <h2>{!! currencyExchange($total_credit - $total_debit) !!}</h2>
                            </div>
                            @if ($total_credit > 0)
                                @php
                                    $used_in_percentense = ($total_debit * 100) / $total_credit;
                                @endphp
                            @else
                                @php
                                    $used_in_percentense = 0;
                                @endphp
                            @endif
                            <div class="process-bar-wrapper mb-20">
                                <span class="process-name mb-2">Used</span>
                                <span class="process-width mb-2">{{ (int) $used_in_percentense }}%</span>
                                <span class="process-bar style--two" data-process-width="{{ $used_in_percentense }}"
                                    style="width: 35%; transition: all 2.25s ease 0s;"></span>
                            </div>
                            <form method="POST" action="{{ route('plugin.wallet.customer.add.deduct') }}">
                                @csrf
                                <div class="form-row mb-20">
                                    <input type="hidden" name="customer_id" value="{{ $customer_details->id }}">
                                    <input type="text" class="theme-input-style" value="{{ old('amount') }}" name="amount"
                                        placeholder="Amount">
                                    @if ($errors->has('amount'))
                                        <div class="invalid-input">{{ $errors->first('amount') }}</div>
                                    @endif
                                </div>

                                <div class="btn-wraper d-flex form-row justify-content-between">
                                    <button type="submit" name="action"
                                        value="{{ config('tlecommercecore.wallet_entry_type.credit') }}"
                                        class="btn btn-success rounded sm mb-1">
                                        {{ translate('Add Money') }}
                                    </button>
                                    @if ($total_credit > $total_debit)
                                        <button type="submit" name="action"
                                            value="{{ config('tlecommercecore.wallet_entry_type.debit') }}"
                                            class="btn btn-warning rounded sm mb-1">
                                            {{ translate('Deduct money') }}
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endif
        <!--End Customer Wallet-->
    </div>


    <!--Tab Content-->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'orders' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $customer_details->id, 'tab' => 'orders']) }}">
                        {{ translate('Orders') }}
                    </a>
                </li>
                @if (isActivePluging('refund'))
                    @can('Manage Refund Requests')
                        <li class="nav-item">
                            <a class="nav-link {{ $active_tab == 'refunds' ? 'active' : '' }}"
                                href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $customer_details->id, 'tab' => 'refunds']) }}">{{ translate('Return Requests') }}</a>
                        </li>
                    @endcan
                @endif
                @can('Manage Product Reviews')
                    <li class="nav-item">
                        <a class="nav-link {{ $active_tab == 'reviews' ? 'active' : '' }}"
                            href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $customer_details->id, 'tab' => 'reviews']) }}">{{ translate('Product Reviews') }}</a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link {{ $active_tab == 'address' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $customer_details->id, 'tab' => 'address']) }}">{{ translate('Addresses') }}</a>
                </li>
                @can('Manage Wishlist Reports')
                    <li class="nav-item">
                        <a class="nav-link {{ $active_tab == 'wishlist' ? 'active' : '' }}"
                            href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $customer_details->id, 'tab' => 'wishlist']) }}">{{ translate('Wishlists') }}</a>
                    </li>
                @endcan
                @if (isActivePluging('wallet'))
                    @can('Manage Wallet Transactions')
                        <li class="nav-item">
                            <a class="nav-link {{ $active_tab == 'wallet' ? 'active' : '' }}"
                                href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $customer_details->id, 'tab' => 'wallet']) }}">{{ translate('Wallet Transactions') }}</a>
                        </li>
                    @endcan
                @endif
            </ul>
            <div class="tab-content" id="myTabContent">
                <!--Customer orders table-->
                <div class="tab-pane fade {{ $active_tab == 'orders' ? 'show active' : '' }}" id="Orders"
                    role="tabpanel" aria-labelledby="orders-tab">
                    <div class="bg-white py-3 table-responsive">
                        <table id="ordersTable" class="hoverable text-nowrap border-top2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Code') }}</th>
                                    <th>{{ translate('Date') }}</th>
                                    <th>{{ translate('Num of Products') }}</th>
                                    <th>{{ translate('Tax') }}</th>
                                    <th>{{ translate('Delivery cost') }}</th>
                                    <th>{{ translate('Total Amount') }}</th>
                                    <th class="text-right">{{ translate('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer_orders as $key => $order)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <a
                                                href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $order->id]) }}">
                                                {{ $order->order_code }}
                                                @if ($order->read_at != null)
                                                    <span class="badge badge-success">{{ translate('New') }}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at }}</td>
                                        <td>{{ $order->total_product }}</td>
                                        <td>{!! currencyExchange($order->total_tax) !!}</td>
                                        <td>{!! currencyExchange($order->total_delivery_cost) !!}</td>
                                        <td>{!! currencyExchange($order->total_payable_amount) !!}</td>
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
                                                        href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $order->id]) }}">{{ translate('Details') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <!--End customer orders table-->
                @if (isActivePluging('refund'))
                    @can('Manage Refund Requests')
                        <!--Customer return requests-->
                        <div class="tab-pane fade {{ $active_tab == 'refunds' ? 'show active' : '' }}" id="returnRequest"
                            role="tabpanel" aria-labelledby="returnRequest-tab">
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
                                        @foreach ($return_requests as $key => $order)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('plugin.refund.request.details', ['id' => $order->id]) }}">
                                                        {{ $order->refund_code }}
                                                    </a>
                                                </td>
                                                <td>{{ $order->created_at }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $order->order_id]) }}">
                                                        {{ $order->order_code }}
                                                    </a>
                                                </td>

                                                <td>{!! currencyExchange($order->total_refund_amount) !!}</td>
                                                <td>
                                                    @if ($order->return_status == config('tlecommercecore.return_request_status.processing'))
                                                        <span class="badge badge-info">Processing</span>
                                                    @elseif ($order->return_status == config('tlecommercecore.return_request_status.cancelled'))
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    @elseif ($order->return_status == config('tlecommercecore.return_request_status.approved'))
                                                        <span class="badge badge-success">Returned</span>
                                                    @else
                                                        <span class="badge badge-primary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($order->payment_status == config('tlecommercecore.return_request_payment_status.refunded'))
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
                                                                href="{{ route('plugin.refund.request.details', ['id' => $order->id]) }}">{{ translate('Details') }}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--Customer return requests-->
                    @endcan
                @endif
                @can('Manage Product Reviews')
                    <!--Product reviews-->
                    <div class="tab-pane fade {{ $active_tab == 'reviews' ? 'show active' : '' }}" id="reviews"
                        role="tabpanel" aria-labelledby="reviews-tab">
                        <div class="bg-white py-3 table-responsive">
                            <table id="reviewTable" class="hoverable text-nowrap">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>{{ translate('Product') }}</th>
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
                                                <label class="switch glow primary medium">
                                                    <input type="checkbox" class="change-status"
                                                        data-review="{{ $review->id }}"
                                                        {{ $review->status == '1' ? 'checked' : '' }}>
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
                                                        <a href="#" class="review-details"
                                                            data-review="{{ $review->id }}">
                                                            {{ translate('Details') }}
                                                        </a>
                                                        <a href="#" class="review-delete"
                                                            data-review="{{ $review->id }}">{{ translate('Delete') }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--End product reviews-->
                @endcan
                @can('Manage Wishlist Reports')
                    <!--Customer wishlist-->
                    <div class="tab-pane fade {{ $active_tab == 'wishlist' ? 'show active' : '' }}" id="wishlist"
                        role="tabpanel" aria-labelledby="wishlist-tab">
                        <div class="bg-white py-3 table-responsive">
                            <table id="wishlistTable" class="hoverable text-nowrap">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>{{ translate('Image') }}</th>
                                        <th>{{ translate('Name') }}</th>
                                        <th>{{ translate('Info') }}</th>
                                        <th>{{ translate('Total Stock') }} </th>
                                        <th>{{ translate('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wishlists as $key => $product)
                                        <tr>
                                            <td>
                                                {{ $key + 1 }}
                                            </td>
                                            <td>
                                                <img src="{{ getFilePath($product->thumbnail_image) }}" class="img-45"
                                                    alt="{{ $product->name }}">
                                            </td>
                                            <td>{{ $product->translation('name', getLocale()) }}</td>
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
                                            </td>
                                            <td>
                                                @if ($product->has_variant == config('tlecommercecore.product_variant.single'))
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
                                                                    $option_name = \Plugin\TlcommerceCore\Models\ProductAttribute::find($variant_com_array[0])->translation('name');
                                                                    $choice_name = \Plugin\TlcommerceCore\Models\AttributeValues::find($variant_com_array[1])->name;
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
                                                            {{ translate('Edit Product') }}
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
                    <!--End Customer wishlist-->
                @endcan
                <!--Customer address-->
                <div class="tab-pane fade {{ $active_tab == 'address' ? 'show active' : '' }}" id="address"
                    role="tabpanel" aria-labelledby="address-tab">
                    <div class="bg-white py-3">
                        <div class="row m-0">
                            @foreach ($customer_addresses as $address)
                                <div class="card mb-30 col-lg-4 col-12">
                                    <div class="border card-body rounded">
                                        <div class="contact-body">
                                            <p class="mb-0">
                                                @if ($address->default_shipping == config('settings.general_status.active'))
                                                    <span
                                                        class=" badge badge-success">{{ translate('Default Shiiping') }}</span>
                                                @endif
                                            </p>
                                            <p class="mb-0">
                                                @if ($address->default_billing == config('settings.general_status.active'))
                                                    <span
                                                        class=" badge badge-success">{{ translate('Default Billing') }}</span>
                                                @endif
                                            </p>
                                            <a href="#">
                                                <h4>{{ $address->name }}</h4>
                                            </a>
                                            <p class="mb-0">{{ translate('Phone') }}:{{ $address->phone }}
                                            </p>
                                            <p class="mb-0">{{ translate('Address') }}: {{ $address->address }}</p>
                                            @if ($address->city != null)
                                                <p class="mb-0">City: {{ $address->city->name }}</p>
                                            @endif
                                            @if ($address->state != null)
                                                <p class="mb-0">{{ translate('State') }}: {{ $address->state->name }}
                                                </p>
                                            @endif
                                            @if ($address->country != null)
                                                <p class="mb-0">{{ translate('Country') }}:
                                                    {{ $address->country->name }}</p>
                                            @endif
                                            <p class="mb-0">{{ translate('Postal Code') }}:
                                                {{ $address->postal_code }}</p>
                                            <p class="mb-0"><span>{{ translate('Status') }}:</span>
                                                @if ($address->status == config('settings.general_status.active'))
                                                    <span class=" badge badge-success">{{ translate('Active') }}</span>
                                                @else
                                                    <span class=" badge badge-danger">{{ translate('Inactive') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
                <!--End customer address-->
                @if (isActivePluging('wallet'))
                    @can('Manage Wallet Transactions')
                        <!--Customer wallet tranaction-->
                        <div class="tab-pane fade {{ $active_tab == 'wallet' ? 'show active' : '' }}" id="wallet"
                            role="tabpanel" aria-labelledby="wallet-tab">
                            <div class="bg-white py-3 table-responsive">
                                <table id="walletTranactionTable" class="hoverable text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ translate('Date') }}</th>
                                            <th>{{ translate('Amount') }}</th>
                                            <th>{{ translate('Type') }}</th>
                                            <th>{{ translate('Payment Option') }}</th>
                                            <th class="text-right">{{ translate('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customer_wallet_transactions as $key => $transaction)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    {{ $transaction->created_at }}
                                                </td>
                                                <td>{!! currencyExchange($transaction->recharge_amount) !!}</td>
                                                <td>
                                                    @if ($transaction->entry_type == config('tlecommercecore.wallet_entry_type.credit'))
                                                        <p class="badge badge-success">{{ translate('Credited') }}</p>
                                                    @else
                                                        <p class="badge badge-danger">{{ translate('Debited') }}</p>
                                                    @endif
                                                </td>

                                                <td>
                                                    <p class="text-capitalize">{{ $transaction->payment_method }}</p>
                                                </td>
                                                <td class="text-right">
                                                    @if ($transaction->status == config('tlecommercecore.wallet_transaction_status.accept'))
                                                        <p class="badge badge-success">{{ translate('Accepted') }}</p>
                                                    @elseif($transaction->status == config('tlecommercecore.wallet_transaction_status.declined'))
                                                        <p class="badge badge-danger">{{ translate('Declined') }}</p>
                                                    @else
                                                        <p class="badge badge-info">{{ translate('Pending') }}</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--End customer wallet tranaction-->
                    @endcan
                @endif
            </div>
        </div>
    </div>
    <!--End tab content-->

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.product.reviews.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-review-id" name="id">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->
    <!--Details Modal-->
    <div id="details-modal" class="details-modal modal fade show" aria-modal="true">
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
    <!--Details Modal-->
@endsection
@section('custom_scripts')
    @include('core::base.includes.data_table.script')
    <script>
        (function($) {
            "use strict";
            /**
             * Vat and tax table
             */
            $("#ordersTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#ordersTable_wrapper .col-md-6:eq(0)');
            //returnRequestTable
            $("#returnRequestTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#returnRequestTable_wrapper .col-md-6:eq(0)');
            /**
             * Product review table
             * 
             **/
            $("#reviewTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#reviewTable_wrapper .col-md-6:eq(0)');
            /**
             * Wallet tranaction table
             * 
             **/
            $("#walletTranactionTable").DataTable({
                "responsive": false,
                "scrolX": true,
                "lengthChange": true,
                "autoWidth": false,
            }).buttons().container().appendTo('#walletTranactionTable_wrapper .col-md-6:eq(0)');
            /**
             * 
             * Change status 
             * 
             * */
            $('.change-status').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('review');
                $.post('{{ route('plugin.tlcommercecore.product.reviews.status.change') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    location.reload();
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
                    $('#details-modal').modal('show');
                })
            });
            /**
             * Delete review
             **/
            $('.review-delete').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('review');
                $('#delete-review-id').val(id);
                $('#delete-modal').modal('show');
            });
        })(jQuery);
    </script>
@endsection
