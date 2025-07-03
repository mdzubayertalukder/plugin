@php
    $active_theme = getActiveTheme();
    $applicable_plugins = $package_info->plugins->toArray();
    $applicable_payment_methods = $package_info->payment_methods->toArray();
@endphp
@if ($active_theme->location == 'default')
    @extends('theme/default::frontend.layout.master')

    @section('seo')
        {{-- SEO --}}
        <title>{{ translate('Order Plan') }} {{ $package_info->name }}</title>
        <meta name="title" content="Registration">
        <meta name="description" content="Registration">
    @endsection

    @section('custom-css')
        <style>
            .bg-custom {
                background-color: #5dd9c1 !important;
            }

            .white-box {
                background-color: #fff;
                border: 1px solid #f7f8fa;
                box-shadow: 3px 3px 30px rgba(0, 0, 0, .04);
                padding: 30px 30px 30px
            }

            .list-group {
                gap: 5px;
            }

            input[type=radio] {
                margin-bottom: 0px !important;
            }

            .mb-20 {
                margin-bottom: 20px !important;
            }

            .list-unstyled li {
                margin-bottom: 5px !important;

            }

            .custom-radio-btn {
                background-color: #fff;
                border: 1px solid #dee2e6;
                display: block;
                font-size: 16px;
                padding: 10px 20px;
            }

            .label-title {
                cursor: pointer;
            }

            .list-group li {
                flex-grow: 1;
            }
        </style>
    @endsection

    @section('content')
        <div class="container my-5 pt-5">
            <div class="row mt-3">
                <div class="col-md-7">
                    <div class="white-box billing-card">
                        <form id="confirm-plan-form" method="POST"
                            action="{{ route('plugin.saas.user.create.store.plan.buy') }}">
                            @csrf
                            <input type="hidden" name="package" id="package_id" value="{{ $package_info->id }}">
                            <input type="hidden" name="plan_id" id="plan_id" value="{{ $plan_info?->id }}">
                            @if ($errors->has('package'))
                                <p class="alert alert-danger">{{ $errors->first('package') }}</p>
                            @endif
                            @if ($errors->has('plan_id'))
                                <div class="alert alert-danger">{{ $errors->first('plan_id') }}</div>
                            @endif
                            @if ($errors->has('store'))
                                <div class="alert alert-danger">{{ $errors->first('store') }}</div>
                            @endif
                            <!--Trial Information and Free Package-->
                            @if ($valid_is_trial == 1 || $package_info->type == 'free')
                                <h3 class="mb-2">{{ translate('Package Information') }}</h3>
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <ul>
                                            <li>
                                                <p>
                                                    <span class="font-weight-bold">{{ translate('Plan') }}:</span>
                                                    <span class="ml-1">{{ $package_info->name }}</span>
                                                </p>
                                            </li>
                                            @if ($price_info != null)
                                                <li>
                                                    <p>
                                                        <span class="font-weight-bold">{{ translate('Price') }}:</span>
                                                        <span>{{ currencyExchange($price_info->cost) }}</span>
                                                        <span>{{ $plan_info->name }}</span>
                                                    </p>
                                                </li>
                                            @else
                                                <input type="hidden" name="free_package" value="free">
                                                <li>
                                                    <p>
                                                        <span class="font-weight-bold">{{ translate('Price') }}:</span>
                                                        <span>{{ currencyExchange(0) }}</span>
                                                    </p>
                                                </li>
                                            @endif
                                            @if ($valid_is_trial == 1)
                                                <input type="hidden" name="is_trial" value="1">
                                                <li>
                                                    <p>
                                                        <span class="font-weight-bold">{{ translate('Trial') }}: </span>
                                                        <span>{{ $package_info->trail_period }} Days</span>
                                                    </p>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                <h3 class="mb-2">{{ translate('Personal Information') }}</h3>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <ul>
                                            <li>
                                                <p>
                                                    <span class="font-weight-bold">{{ translate('Name') }}:</span>
                                                    <span class="ml-1">{{ auth()->user()->name }}</span>
                                                </p>
                                            </li>
                                            <li>
                                                <p>
                                                    <span class="font-weight-bold">{{ translate('Email') }}:</span>
                                                    <span>{{ auth()->user()->email }}</span>
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            <!--End Trial Information and Free Package--->

                            <!--Store Information-->
                            <h4 class="mb-2">{{ translate('Store Information') }}</h4>
                            @if ($store_info == null)
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-20">
                                            <label for="store_name">
                                                {{ translate('Store Name') }}<span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="store_name" name="store_name"
                                                class="form-control bg-light border mb-0" placeholder="Store Name"
                                                value="{{ old('store_name') }}">
                                            @if ($errors->has('store_name'))
                                                <div class="text-danger small">{{ $errors->first('store_name') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group mb-20 col-md-6">
                                        <label class="mb-2 font-14 bold black">{{ translate('Default Language') }}
                                        </label>
                                        <select class="form-control bg-light border mb-0" name="default_language">
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->id }}">{{ $language->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('default_language'))
                                            <div class="text-danger small">{{ $errors->first('default_language') }}</div>
                                        @endif
                                    </div>

                                    <div class="form-group mb-20 col-md-6">
                                        <label class="mb-2 font-14 bold black">{{ translate('Default Currency') }}</label>
                                        <select class="form-control bg-light border mb-0" name="default_currency">
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('default_currency'))
                                            <div class="text-danger small">{{ $errors->first('default_currency') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-20">
                                            <label for="store_name">
                                                {{ translate('Store Name') }}<span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="store_name" name="store_name"
                                                class="form-control bg-light border mb-0" placeholder="Store Name"
                                                value="{{ $store_info->store_name }}" readonly>
                                            @if ($errors->has('store_name'))
                                                <div class="text-danger small">{{ $errors->first('store_name') }}</div>
                                            @endif
                                            <input type="hidden" name="store" value="{{ $store_info->id }}">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!--End Store Information-->

                            <!--Payment Information-->
                            @if ($package_info->type == 'paid' && $valid_is_trial == 0)
                                <h4 class="mb-2">{{ translate('Billing Address') }}</h4>
                                <input type="hidden" name="primary_amount" id="primary_input_amount"
                                    value="{{ $price_info != null ? $price_info->cost : 0 }}">
                                <input type="hidden" name="discount_amount" id="discount_input_amount" value="0">
                                <input type="hidden" name="amount" id="amount"
                                    value="{{ $price_info != null ? $price_info->cost : 0 }}">
                                <div class="form-row">
                                    <div class="form-group mb-20 col-md-6">
                                        <label for="name" class="mb-2 font-14 bold black">
                                            {{ translate('Full Name') }} <span class="text-danger"> *</span>
                                        </label>
                                        <input type="text" id="name" name="name"
                                            class="form-control bg-light border mb-0" placeholder="Name"
                                            value="{{ $billing_details == null ? old('name') : $billing_details->name }}">
                                        @if ($errors->has('name'))
                                            <div class="text-danger small">{{ $errors->first('name') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-20 col-md-6">
                                        <label class="mb-2 font-14 bold black">
                                            {{ translate('Email') }} <span class="text-danger"> *</span>
                                        </label>
                                        <input type="email" name="email" class="form-control bg-light border mb-0"
                                            placeholder="Email"
                                            value="{{ $billing_details == null ? old('email') : $billing_details->email }}">
                                        @if ($errors->has('email'))
                                            <div class="text-danger small">{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group mb-20 col-md-6">
                                        <label for="phone" class="mb-2 font-14 bold black">
                                            {{ translate('Phone Number') }}<span class="text-danger"> *</span>
                                        </label>
                                        <input type="text" id="phone" name="phone"
                                            class="form-control bg-light border mb-0" placeholder="Phone"
                                            value="{{ $billing_details == null ? old('phone') : $billing_details->phone }}">
                                        @if ($errors->has('phone'))
                                            <div class="text-danger small">{{ $errors->first('phone') }}</div>
                                        @endif

                                    </div>
                                    <div class="form-group mb-20 col-md-6">
                                        <label for="post_code"class="mb-2 font-14 bold black">
                                            {{ translate('Postal Code') }}
                                        </label>
                                        <input type="text" id="post_code" name="post_code"
                                            class="form-control bg-light border mb-0" placeholder="Post Code"
                                            value="{{ $billing_details == null ? old('post_code') : $billing_details->post_code }}">
                                        @if ($errors->has('post_code'))
                                            <div class="text-danger small">{{ $errors->first('post_code') }}</div>
                                        @endif
                                    </div>

                                </div>
                                <div class="form-row">
                                    <div class="form-group mb-20 col-md-6">
                                        <label for="country" class="mb-2 font-14 bold black">
                                            {{ translate('Country') }} <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control bg-light border mb-0" id="country" name="country"
                                            onchange="getAllStatesOfCountry()">
                                            <option>{{ translate('Select Country') }}</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" @selected($billing_details != null && $billing_details->country == $country->id)>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                            @if ($errors->has('country'))
                                                <div class="text-danger small">{{ $errors->first('country') }}</div>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group mb-20 col-md-6">
                                        <label for="state"
                                            class="mb-2 font-14 bold black">{{ translate('State') }}</label>
                                        <select class="form-control bg-light border mb-0" id="state" name="state"
                                            onchange="getAllCitiesOfState()">

                                        </select>
                                        @if ($errors->has('state'))
                                            <div class="text-danger small">{{ $errors->first('state') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group mb-20 col-md-6">
                                        <label for="city"
                                            class="mb-2 font-14 bold black">{{ translate('City') }}</label>
                                        <select class="form-control bg-light border mb-0" id="city" name="city">
                                        </select>
                                        @if ($errors->has('city'))
                                            <div class="text-danger small">{{ $errors->first('city') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group mb-20 col-md-6">
                                        <label for="address" class="mb-2 font-14 bold black">
                                            {{ translate('Address') }}<span class="text-danger"> *</span>
                                        </label>
                                        <input type="text" id="address" name="address"
                                            class="form-control bg-light border mb-0" placeholder="Address"
                                            value="{{ $billing_details == null ? old('address') : $billing_details->address }}">
                                        @if ($errors->has('address'))
                                            <div class="text-danger small">{{ $errors->first('address') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <h4 class="mb-2">{{ translate('Payment Method') }}<span class="text-danger"> *</span>
                                </h4>
                                <div class="form-row mb-20">
                                    <div class="col-12">
                                        @if ($errors->has('payment_method'))
                                            <div class="text-danger small">{{ $errors->first('payment_method') }}</div>
                                        @endif
                                        <ul class="list-group d-flex flex-row flex-wrap">
                                            @foreach ($payment_gateways as $key => $payment)
                                                <li class="single-form-selector">
                                                    <span class="custom-radio-btn">
                                                        <label class="mb-0" for="p-{{ $payment->id }}">
                                                            <input type="radio" id="p-{{ $payment->id }}"
                                                                name="payment_method" value="{{ $payment->id }}">
                                                            <span class="label-title">
                                                                @if ($payment->logo != null)
                                                                    <img src="{{ $payment->logo }}"
                                                                        alt="{{ $payment->name }}">
                                                                @else
                                                                    {{ $payment->name }}
                                                                @endif
                                                            </span>
                                                        </label>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <label>{{ translate('Have a coupon?') }}</label>
                                <div class="d-flex flex-wrap form-group mb-20">
                                    <input type="text" id="coupon" name="coupon" value=""
                                        class="bg-light border form-control mb-0 w-75" placeholder="Enter Coupon Code">
                                    <button class="bg-custom btn-crs rounded-0 s-btn w-25 coupon-apply-btn">
                                        {{ translate('Apply') }}
                                    </button>
                                    <span class="text-danger small" id="coupon_error"></span>
                                </div>
                                <h5 class="mb-2 discount-label d-none">
                                    {{ translate('Discount') }}:<span class="ml-1" id="discount_amount"></span>
                                </h5>
                            @endif
                            <h3>
                                {{ translate('Total:') }}<span
                                    id="total_amount">{{ currencyExchange($price_info != null ? $price_info->cost : 0) }}</span>
                            </h3>
                            <!--End Payment Information-->
                            <!--Form Action-->
                            <div class="mt-3">
                                @if ($errors->has('package'))
                                    <div class="text-danger small">{{ $errors->first('package') }}</div>
                                @endif
                                <button type="submit" class="btn-crs s-btn w-100 bg-custom mb-2" id="registration">
                                    <img src="{{ asset('/public/backend/assets/img/loader-w4.svg') }}" alt=""
                                        class="d-none" id="loader">
                                    {{ translate('Order Package') }}
                                </button>
                                <p class="text-danger text-danger wait-notification text-center d-none">
                                    {{ translate('Please wait. It may take some time.') }}
                                </p>
                            </div>
                            <!--End Form Action-->
                        </form>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card white-box">
                        <h3 class="mb-1">{{ $package_info->name }}</h3>

                        <h5 class="mb-4">
                            <span>{{ currencyExchange($price_info != null ? $price_info->cost : 0) }}</span>
                            <span class="ml-1">
                                @if ($package_info->type == 'paid')
                                    {{ $plan_info->name }}
                                @else
                                    {{ translate('Lifetime') }}
                                @endif
                            </span>
                        </h5>
                        <h5 class="mb-2">{{ front_translate('Available Features') }}</h5>
                        <ul class="list-unstyled mb-4">
                            @foreach ($plugins as $plugin)
                                @if ($plugin->type != 'saas' && $plugin->location != 'tlecommercecore')
                                    <li>
                                        @if (in_array($plugin->id, array_column($applicable_plugins, 'plugin_id')))
                                            <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        @else
                                            <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                        @endif

                                        {{ str_replace('Tlcommerce', '', $plugin->name) }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        @php
                            $privileges =
                                $package_info->privileges != null
                                    ? json_decode($package_info->privileges->privileges, true)
                                    : null;
                        @endphp
                        @if ($privileges != null)
                            <h5 class="mb-2">{{ front_translate('Access Privileges') }}</h5>
                            <ul class="list-unstyled mb-4">
                                @foreach ($privileges as $key => $value)
                                    @php
                                        $privilege = str_replace('package_privileges_', '', $key);
                                        $privilege = ucwords(implode(' ', explode('_', $privilege)));
                                    @endphp
                                    <li>
                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        {{ $privilege }} - {{ $value == -1 ? 'Unlimitted' : $value }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <h5 class="mb-2">{{ front_translate('Available Payment Methods') }}</h5>
                        <ul class="list-unstyled">
                            @foreach ($payment_methods as $method => $id)
                                <li>
                                    @if (in_array($id, array_column($applicable_payment_methods, 'payment_method_id')))
                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                    @else
                                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                    @endif
                                    @if ($method == 'cod')
                                        Cash On Delivery
                                    @else
                                        {{ ucfirst($method) }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endsection
@endif

@section('custom-js')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            'use strict'
            getAllStatesOfCountry()
        });
        /**
         *  Will request for all states of selected country
         */
        function getAllStatesOfCountry() {
            'use strict';
            let selected_country = $('#country').val();
            $.ajax({
                url: '{{ route('plugin.saas.get.states.of.country') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    country_id: selected_country
                },
                success: function(response) {
                    if (response.success) {
                        let states = response.states

                        let html = ``

                        for (let i = 0; i < states.length; i++) {
                            let state = '{{ $billing_details != null ? $billing_details->state : -1 }}'
                            if (state == states[i]['id']) {
                                html = html + `<option value='` + states[i]['id'] + `' selected>` + states[i][
                                        'name'
                                    ] +
                                    `</option>`
                            } else {
                                html = html + `<option value='` + states[i]['id'] + `'>` + states[i]['name'] +
                                    `</option>`
                            }

                        }
                        $('#state').html(html)
                        getAllCitiesOfState()
                    }
                }
            });
        }

        /**
         *  Will request for all cities of selected state
         */
        function getAllCitiesOfState() {
            'use strict';
            let selected_state = $('#state').val();
            $.ajax({
                url: '{{ route('plugin.saas.get.cities.of.state') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    state_id: selected_state
                },
                success: function(response) {
                    if (response.success) {
                        let cities = response.cities

                        let html = ``

                        for (let i = 0; i < cities.length; i++) {
                            let city = '{{ $billing_details != null ? $billing_details->city : -1 }}'

                            if (city == cities[i]['id']) {
                                html = html + `<option value='` + cities[i]['id'] + `' selected>` + cities[i][
                                        'name'
                                    ] +
                                    `</option>`
                            } else {
                                html = html + `<option value='` + cities[i]['id'] + `'>` + cities[i]['name'] +
                                    `</option>`
                            }


                        }
                        $('#city').html(html)
                    }
                }
            });
        }
        /**
         * Calculate price after coupon discount
         */
        $(document).on("click", '.coupon-apply-btn', function(e) {
            e.preventDefault();
            $('#coupon_error').html('');
            let coupon = $('#coupon').val();
            let package_id = "{{ $package_info->id }}";
            let plan_id = "{{ $plan_info?->id }}";

            $.ajax({
                url: '{{ route('plugin.saas.apply.coupon') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon: coupon,
                    package: package_id,
                    plan_id: plan_id
                },
                success: function(response) {
                    if (response.success) {
                        let summery = response.summery;
                        $(".discount-label").removeClass('d-none');
                        $('#discount_amount').html(summery.discount_amount + '<span class="ml-1">(' +
                            coupon + ')</span>');
                        $('#discount_input_amount').val(summery.discount_amount_value);
                        $('#total_amount').html(summery.total);
                        $('#amount').val(summery.total_value);
                        $('#coupon').prop('readonly', true);
                        $('#coupon_error').html('');
                    } else {
                        $('#coupon_error').html("{{ translate('Please provide a valid coupon !') }}");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#coupon_error').html("{{ translate('Please provide a valid coupon !') }}");
                }
            });
        })

        //Confirm a plan
        $('#confirm-plan-form').submit(function(event) {
            $('.wait-notification').removeClass('d-none');
            event.preventDefault()
            $('#registration').prop('disabled', true)
            $('#loader').removeClass('d-none')
            this.submit();
        });
    </script>
@endsection
