@extends('core::base.layouts.master')
@section('title')
    {{ translate('Refund Request Redetails') }}
@endsection
@section('custom_css')
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <style>
        .status-list li span.badge {
            line-height: unset;
        }

        .img-fit {
            max-height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .file-preview-item .thumb {
            -ms-flex: 0 0 50px;
            flex: 0 0 50px;
            max-width: 50px;
            height: 45px;
            width: 50px;
            text-align: center;
            background: #f1f2f4;
            font-size: 20px;
            color: #92969b;
            border-radius: 0.25rem;
            overflow: hidden;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <!-- Request and Order details -->
            <div class="bg-white invoice-pd mb-30">
                <div class="row">
                    <!--Action area-->
                    <div class="col-12 d-flex filter-area justify-content-between mb-20 px-2">
                        <div class="ml-auto right-side">
                            <button class="btn long rounded btn-info status-update-modal-open-button" data-toggle="modal"
                                data-target="#status-update-model">{{ translate('Update Request status') }}
                            </button>
                        </div>

                    </div>
                    <!--End actions area-->
                    <!--Request info-->
                    @if ($details != null)
                        <div class="col-xl-3 col-md-6 mb-30">
                            <div class="invoice invoice-form">
                                <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Request Details') }}</div>

                                <ul class="list-invoice">
                                    <li class="bold black font-17">{{ $details->refund_code }}</li>
                                    <li><span class="key">{{ translate('Date') }}</span> <span
                                            class="black">{{ $details->created_at->format('d M Y h:i A') }}</span>
                                    </li>
                                    <li>
                                        <span class="key">{{ translate('Total Amount') }}</span>
                                        <span class="black">{!! currencyExchange($details->total_amount) !!}</span>
                                    </li>
                                    <li>
                                        <span class="key">{{ translate('Refunded Amount') }}</span>
                                        <span class="black">{!! currencyExchange($details->total_refund_amount) !!}</span>
                                    </li>
                                    <li>
                                        <span class="key">{{ translate('Return Status') }}</span>
                                        <span
                                            class="badge badge-info text-capitalize">{{ $details->returnStatusLabel() }}</span>
                                    </li>
                                    <li>
                                        <span class="key">{{ translate('Payment Status') }}</span>
                                        <span
                                            class="badge badge-info text-capitalize">{{ $details->paymentStatusLabel() }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                    <!--End Request info-->
                    <!-- Order Details -->
                    <div class="col-xl-3 col-md-6 mb-30">
                        <div class="invoice payment-details mt-5 mt-xl-0">
                            <div class="invoice-title c4 bold font-14 mb-3 black">Order Details:</div>
                            <ul class="status-list">
                                <li>
                                    <span class="black font-17 black bold">{{ $details->order->order_code }}</span>
                                </li>
                                <li><span class="key">{{ translate('Date') }}</span> <span
                                        class="black">{{ $details->order->created_at->format('d M Y h:i A') }}</span>
                                </li>
                                <li><span class="key">Total</span> <span class="black">{!! currencyExchange($details->order->total_payable_amount) !!}</span>
                                </li>

                                <li><span class="key">{{ translate('Paid by') }}</span> <span
                                        class="black">{{ $details->order->payment_method_info->name }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Order Details -->
                    <!--Pickup point details-->
                    @if ($details->order->pickup_point != null)
                        <div class="col-xl-3 col-md-6 mb-30">
                            <div class="invoice invoice-form">
                                <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Pickup Point') }}</div>
                                @if (isActivePluging('pickuppoint'))
                                    <ul class="list-invoice">
                                        <li class="bold black font-17">{{ $details->order->pickup_point->name }}</li>
                                        <li class="location">
                                            {{ $details->order->pickup_point->location }},{{ $details->order->pickup_point->city_info->name }}
                                        </li>
                                        <li class="call">
                                            {{ $details->order->pickup_point->phone }}
                                        </li>
                                    </ul>
                                @else
                                @endif

                            </div>
                        </div>
                    @endif
                    <!--End pickup point details-->
                    <!--Shipping info-->
                    @if ($details->order->shipping_details != null)
                        <div class="col-xl-3 col-md-6 mb-30">
                            <div class="invoice invoice-form">
                                <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Shipping Info') }}</div>

                                <ul class="list-invoice">
                                    <li class="bold black font-17">{{ $details->order->shipping_details->name }}</li>
                                    <li class="location">
                                        {{ $details->order->shipping_details->address }},
                                        @if ($details->order->shipping_details->city != null)
                                            {{ $details->order->shipping_details->city->translation('name') }}<br>
                                        @endif
                                        @if ($details->order->shipping_details->state != null)
                                            {{ $details->order->shipping_details->state->translation('name') }},
                                        @endif
                                        @if ($details->order->shipping_details->country != null)
                                            {{ $details->order->shipping_details->country->translation('name') }}
                                        @endif
                                    </li>
                                    <li class="call">
                                        <a
                                            href="tel:+01234567891">{{ $details->order->shipping_details->phone_code }}{{ $details->order->shipping_details->phone }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                    <!--End shipping info-->
                    <!-- Customer info -->
                    <div class="col-xl-3 col-md-6 mb-30">
                        <div class="invoice payment-details mt-5 mt-xl-0">
                            <div class="invoice-title c4 bold font-14 mb-3">{{ translate('Customer') }}</div>
                            @if ($details->customer != null)
                                <ul class="status-list">
                                    <li>
                                        <a href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $details->customer->id]) }}"
                                            class="black font-17 black bold">{{ $details->customer->name }}</a>
                                    </li>
                                    <li>
                                        <span class="key">{{ translate('Uid') }}</span>
                                        <span class="black">{{ $details->customer->uid }}</span>
                                    </li>
                                    <li>
                                        <span class="key">{{ translate('Email') }}</span>
                                        <span class="black">{{ $details->customer->email }}</span>
                                    </li>
                                    <li>
                                        <span class="key">{{ translate('Phone') }}</span>
                                        <span
                                            class="black">{{ $details->customer->phone_code }}{{ $details->customer->phone }}</span>
                                    </li>
                                </ul>
                            @endif
                        </div>
                    </div>
                    <!-- End customer info -->
                </div>
            </div>
            <!-- End Request and Order details -->
        </div>
        <!--Packages-->
        <div class="col-12 col-lg-8">
            <!-- Product Details -->
            <div class="card mb-30">
                <div class="bg-white border-bottom2 card-header d-flex justify-content-between">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Product Details') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row px-12">
                        <!--Delivery steps and tracking info-->
                        <div class="col-md-12 mb-20">
                            <!--steps-->
                            @if ($details->return_status != config('tlecommercecore.return_request_status.cancelled'))
                                <div class="order-status-range">
                                    <ul class="progressbar d-flex">
                                        <li
                                            class="{{ $details->return_status == config('tlecommercecore.return_request_status.pending') || $details->return_status == config('tlecommercecore.return_request_status.processing') || $details->return_status == config('tlecommercecore.return_request_status.product_received') || $details->return_status == config('tlecommercecore.return_request_status.approved') || $details->return_status == config('tlecommercecore.return_request_payment_status.refunded') ? 'active' : '' }}">
                                            @if ($details->return_status == config('tlecommercecore.return_request_status.pending'))
                                                {{ translate('Pending') }}
                                            @else
                                                {{ $details->return_status == config('tlecommercecore.return_request_status.processing') ? translate('Processing') : translate('Product Received') }}
                                            @endif
                                        </li>

                                        <li
                                            class="{{ $details->return_status == config('tlecommercecore.return_request_status.approved') ? 'active' : '' }}">
                                            {{ translate('Return Approved') }}
                                        </li>
                                        <li
                                            class="{{ $details->refund_status == config('tlecommercecore.return_request_payment_status.refunded') ? 'active' : '' }}">
                                            {{ translate('Refunded') }}
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <div class="mb-20">
                                    <p class="alert alert-danger">{{ translate('This request has been cancelled') }}</p>
                                </div>
                            @endif
                            <!--End steps-->
                            <!--tracking history-->
                            @if ($details->trackings != null && count($details->trackings) > 0)
                                <div
                                    class="border-bottom-0 d-flex justify-content-between order-status-details pb-0 tracking-header">
                                    <div class="item">
                                        <div class="details-item d-flex">
                                            <div class="time black">{{ $details->trackings[0]->created_at }}</div>
                                            <div class="text text-dark ml-10">{!! xss_clean($details->trackings[0]->message) !!}</div>
                                        </div>
                                    </div>
                                    @if (count($details->trackings) > 1)
                                        <div class="toogle-items">
                                            <a href="#" data-toggle="collapse" data-target="item-body-1"
                                                class="tracking-toogle-button">
                                                <i class="icofont-plus"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div id="item-body-1" class="border-top-0 hidden items order-status-details pt-0">
                                    @php
                                        
                                        $first_key = $details->trackings->keys()->first();
                                    @endphp
                                    @foreach ($details->trackings->forget($first_key) as $tracking)
                                        <div class="item">
                                            <div class="details-item d-flex">
                                                <div class="time black">{{ $tracking->created_at }}</div>
                                                <div class="text text-dark ml-10">{!! xss_clean($tracking->message) !!}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <!--End tracking history-->
                        </div>
                        <!--End Delivery steps and tracking info-->
                        <!--Product info-->
                        <div class="col-md-12">
                            <div class="product-list-group">
                                <div class="product-list p-3 border">
                                    <!--Product info-->
                                    <div class="align-items-center product-information row">
                                        <div class="col-md-6">
                                            @if ($details->product != null)
                                                <div class="align-items-center d-flex product-info">
                                                    <div class="image"><img
                                                            src="{{ getFilePath($details->product->thumbnail_image) }}"
                                                            alt="{{ $details->product->name }}" class="img-70 rounded">
                                                    </div>
                                                    <div class="title">
                                                        <h5>{{ $details->product->translation('name') }}</h5>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between">
                                                <div class="price"><span>Qty {{ $details->quantity }}</span></div>
                                                <div class="price">
                                                    <span>{!! currencyExchange($details->total_amount) !!}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--End product info-->
                                </div>
                            </div>
                        </div>
                        <!--End product info-->
                    </div>
                </div>
            </div>
            <!-- End Product Details -->
            <!--Refund Request Information-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Refund Request Information') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mar-no">
                            <tbody>
                                @if ($details->reason != null)
                                    <tr>
                                        <td>{{ translate('Reason') }}</td>
                                        <td>
                                            <p>{{ $details->reason->translation('name') }}</p>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ translate('Note') }}</td>
                                    <td>
                                        {{ $details->comment }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ translate('Attachements') }}</td>
                                    <td>
                                        @if ($details->images != null)
                                            <div class="align-items-center d-flex file-preview-item gap-10 mt-2">
                                                @php
                                                    $images = substr($details->images, 1, -1);
                                                    $images = explode(',', $images);
                                                @endphp
                                                @foreach ($images as $image)
                                                    <a href="{{ getFilePath($image) }}" target="_blank"
                                                        class="d-block text-reset">
                                                        <div
                                                            class="align-items-center align-self-stretch d-flex justify-content-center thumb">
                                                            <img src="{{ getFilePath($image) }}" class="img-fit">
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--End Refund Request Information-->
        </div>
        <!--End packages-->
        <!--Right area-->
        <div class="col-12 col-lg-4">
            <!--Order Summary-->
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
                                                {!! currencyExchange($details->order->sub_total) !!}
                                            </span>
                                        </strong>
                                    </td>
                                </tr>
                                <tr class="cart-tax">
                                    <td>{{ translate('Shipping') }}</td>
                                    <td>
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($details->order->total_delivery_cost) !!}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="cart-tax">
                                    <td>{{ translate('Tax') }}</td>
                                    <td>
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($details->order->total_tax) !!}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="cart-tax border-1">
                                    <td>{{ translate('Discount') }}</td>
                                    <td>
                                        <span class="Price-currencySymbol">-</span>
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($details->order->total_discount) !!}
                                        </span>
                                    </td>
                                </tr>
                                <tr class="cart-subtotal">
                                    <th class="border-0">Total Payable</th>
                                    <th class="border-0">
                                        <span class="Price-amount amount">
                                            {!! currencyExchange($details->order->total_payable_amount) !!}
                                        </span>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--End order summary-->
        </div>
        <!--End Right area-->
    </div>

    <!--status update model-->
    <div id="status-update-model" class="status-update-model modal fade show" aria-modal="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Status Update') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="update_status_form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $details->id }}">
                        <!--Delivery status-->
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Return Status') }}<span
                                    class="text text-danger">*</span></label>
                            <select class="theme-input-style" name="return_status" id="return_status">
                                <option value="{{ config('tlecommercecore.return_request_status.pending') }}"
                                    @selected($details->return_status == config('tlecommercecore.return_request_status.pending'))>
                                    {{ translate('Pending') }}
                                </option>
                                <option value="{{ config('tlecommercecore.return_request_status.processing') }}"
                                    @selected($details->return_status == config('tlecommercecore.return_request_status.processing'))>
                                    {{ translate('Processing') }}
                                </option>
                                <option value="{{ config('tlecommercecore.return_request_status.product_received') }}"
                                    @selected($details->return_status == config('tlecommercecore.return_request_status.product_received'))>
                                    {{ translate('Product Received') }}
                                </option>
                                <option value="{{ config('tlecommercecore.return_request_status.approved') }}"
                                    @selected($details->return_status == config('tlecommercecore.return_request_status.approved'))>
                                    {{ translate('Approved') }}
                                </option>
                                <option value="{{ config('tlecommercecore.return_request_status.cancelled') }}"
                                    @selected($details->return_status == config('tlecommercecore.return_request_status.cancelled'))>
                                    {{ translate('Cancelled') }}
                                </option>
                            </select>
                        </div>
                        <!--End delivery status-->
                        <!--Payment status-->
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Payment Status') }}<span
                                    class="text text-danger">*</span></label>
                            <select class="theme-input-style payment_status" name="payment_status" id="payment_status">
                                <option value="{{ config('tlecommercecore.return_request_payment_status.pending') }}"
                                    @selected($details->refund_status == config('tlecommercecore.return_request_payment_status.pending'))>
                                    {{ translate('Pending') }}
                                </option>
                                <option value="{{ config('tlecommercecore.return_request_payment_status.refunded') }}"
                                    @selected($details->refund_status == config('tlecommercecore.return_request_payment_status.refunded'))>
                                    {{ translate('Refunded') }}
                                </option>
                            </select>
                        </div>
                        <!--End payment status-->
                        <!--Amount-->
                        <div
                            class="form-row mb-20 refund-amount {{ $details->refund_status == config('tlecommercecore.return_request_payment_status.refunded') ? '' : 'd-none' }} ">
                            <label class="black font-14">{{ translate('Refund Amount') }}</label>
                            <input type="text" name="refund_amount" id="amount" class="theme-input-style"
                                value="{{ $details->total_refund_amount != 0 ? $details->total_refund_amount : $details->total_amount }}"
                                @disabled($details->refund_status == config('tlecommercecore.return_request_payment_status.refunded'))>
                        </div>
                        <!--End amount-->
                        <!--Paid by-->
                        <div class="mb-20 refund_by d-none">
                            <div class="form-group">
                                <label class="mb-3 d-block black font-14 bold">{{ translate('Paid by') }}</label>
                                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                    <div class="custom-radio mr-3">
                                        <input type="radio" id="manual_paid" name="paid_by" value="manual">
                                        <label for="manual_paid"></label>
                                    </div>
                                    <label for="manual_paid">{{ translate('Manual') }}</label>
                                </div>
                                <div class="d-flex d-sm-inline-flex align-items-center mr-sm-5 mb-3">
                                    <div class="custom-radio mr-3">
                                        <input type="radio" id="wallet_paid" name="paid_by" value="wallet">
                                        <label for="wallet_paid"></label>
                                    </div>
                                    <label for="wallet_paid">{{ translate('Wallet') }}</label>
                                </div>
                            </div>
                        </div>
                        <!--End paid by-->
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
                                <button class="btn long update-request-status rounded">{{ translate('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--End status update model-->
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            /**
             * Summer note
             * 
             **/
            $("#order-comment").summernote({
                tabsize: 2,
                height: 150,
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
             * Select payment status
             * 
             **/
            $(".payment_status").on('change', function(e) {
                e.preventDefault();
                let payment_status = $(this).val();
                if (payment_status ==
                    {{ config('tlecommercecore.return_request_payment_status.refunded') }}) {
                    $(".refund-amount").removeClass('d-none');
                    $(".refund_by").removeClass('d-none');
                } else {
                    $(".refund-amount").addClass('d-none');
                    $(".refund_by").addClass('d-none');
                }
            });
            /**
             * Status update of refund request 
             * 
             **/
            $(".update-request-status").on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $("#update_status_form").serialize(),
                    url: '{{ route('plugin.refund.requests.status.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ translate('Request status successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ translate('Request status update failed') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status == 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').closest(
                                    '.input-option').after(
                                    '<div class="invalid-input">' + error + '</div>')
                            })
                        } else {
                            toastr.error('{{ translate('Update Failed ') }}');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
