@php
    $packages = getAllActivePackages();
    $system_name = getGeneralSetting('system_name');
    $desktop_logo = !empty(getGeneralSetting('admin_logo')) ? getFilePath(getGeneralSetting('admin_logo')) : '';

    $active_theme = getActiveTheme();
    $auth_layout = getThemeOption('auth_layout', $active_theme->id);
    $reg_bg_image = !empty($auth_layout['user_reg_bg_image'])
        ? asset(getFilePath($auth_layout['user_reg_bg_image']))
        : '';
@endphp
@extends('core::base.auth.auth_layout')
@section('title')
    {{ translate('Customer Register') }}
@endsection

@section('custom_css')
    <style>
        body {
            background-image: url('{{ $reg_bg_image }}');
            background-size: cover;
            position: relative;
            min-height: 100vh;
        }

        body:before {
            content: "";
            background-color: rgb(183 180 180 / 50%);
            top: 0;
            height: 100%;
            left: 0;
            width: 100%;
            position: absolute;
        }

        .registration-page-layout {
            height: 100vh;
            min-height: 100%;
        }
    </style>
@endsection

@section('main_content')
    <div class="mn-vh-100 d-flex align-items-center registration-page-layout">
        <div class="container p-4">
            <div class="row">
                <div class="col-xl-8 col-lg-8 col-12 mx-auto">
                    <div class="card bg-white p-3 py-4">
                        <div class="auth-card-header text-center pt-3">
                            <div class="logo">
                                @if (empty($desktop_logo))
                                    <a href="/" class="default-logo">
                                        <h3>{{ $system_name }}</h3>
                                    </a>
                                @else
                                    <a href="/" class="default-logo">
                                        <img src="{{ asset($desktop_logo) }}" alt="TLCommerce Saas">
                                    </a>
                                @endif
                            </div>
                            <h3 class="mt-3">{{ translate('Welcome to ') }} {{ $system_name }}</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('plugin.saas.user.registration') }}" method="post"
                                id="registration-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="name"
                                                        class="mb-2 font-14 bold black">{{ translate('Name') }} <span
                                                            class="text-danger"> *
                                                        </span></label>
                                                    <input type="text" id="name" name="name"
                                                        class="theme-input-style" placeholder="{{ translate('Name') }}"
                                                        value="{{ old('name') }}">
                                                    @if ($errors->has('name'))
                                                        <div class="text-danger mt-2">{{ $errors->first('name') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="email"
                                                        class="mb-2 font-14 bold black">{{ translate('Email') }} <span
                                                            class="text-danger"> *
                                                        </span></label>
                                                    <input type="email" id="email" name="email"
                                                        class="theme-input-style"
                                                        placeholder="{{ translate('Email Address') }}">
                                                    @if ($errors->has('email'))
                                                        <div class="text-danger mt-2">{{ $errors->first('email') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="password"
                                                        class="mb-2 font-14 bold black">{{ translate('Password') }}
                                                        <span class="text-danger"> *
                                                        </span></label>
                                                    <input type="password" id="password" name="password"
                                                        class="theme-input-style" placeholder="{{ translate('******') }}">
                                                    @if ($errors->has('password'))
                                                        <div class="text-danger mt-2">{{ $errors->first('password') }}
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="password_confirmation"
                                                        class="mb-2 font-14 bold black">{{ translate('Password Confirmation') }}
                                                        <span class="text-danger"> *</span></label>
                                                    <input type="password" id="password_confirmation"
                                                        name="password_confirmation" class="theme-input-style"
                                                        placeholder="{{ translate('******') }}">
                                                    @if ($errors->has('password_confirmation'))
                                                        <div class="text-danger mt-2">
                                                            {{ $errors->first('password_confirmation') }}
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="store_name"
                                                        class="mb-2 font-14 bold black">{{ translate('Store Name') }} <span
                                                            class="text-danger"> *
                                                        </span></label>
                                                    <input type="text" id="store_name" name="store_name"
                                                        class="theme-input-style"
                                                        placeholder="{{ translate('Store Name') }}">
                                                    @if ($errors->has('store_name'))
                                                        <div class="text-danger mt-2">{{ $errors->first('store_name') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="coupon"
                                                        class="mb-2 font-14 bold black">{{ translate('Coupon') }}</label>
                                                    <div class="d-flex">
                                                        <input type="text" id="coupon" name="coupon"
                                                            class="theme-input-style"
                                                            placeholder="{{ translate('Coupon') }}">
                                                        <input type="hidden" name="is_redeem_coupon" value="0"
                                                            id="is_redeem_coupon">
                                                        <button type="button" class="btn btn-square sm ml-2"
                                                            onclick="getPackagesAccordingToCoupon()">{{ translate('Apply') }}</button>
                                                    </div>
                                                    <div class="text-danger mt-2" id="coupon_error_msg">
                                                        {{ $errors->first('coupon') }}
                                                    </div>
                                                    @if ($errors->has('coupon'))
                                                        <div class="text-danger mt-2">{{ $errors->first('coupon') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="package"
                                                        class="mb-2 font-14 bold black">{{ translate('Package') }}<span
                                                            class="text-danger"> *
                                                        </span></label>
                                                    <select class="theme-input-style" id="package"
                                                        onchange="getAllPlansOfPackage()">
                                                        <option>{{ translate('Select Package') }}</option>
                                                        @for ($i = 0; $i < sizeof($packages); $i++)
                                                            <option value="{{ $packages[$i]->id }}"
                                                                {{ collect(old('packages'))->contains($packages[$i]->id) ? 'selected' : '' }}>
                                                                {{ $packages[$i]->name }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                    <input type="hidden" id="selected_package" value=""
                                                        name="package_id">
                                                    @if ($errors->has('package_id'))
                                                        <div class="text-danger mt-2">{{ $errors->first('package_id') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group mb-20">
                                                    <label for="plan"
                                                        class="mb-2 font-14 bold black">{{ translate('Plans') }}<span
                                                            class="text-danger"> *
                                                        </span></label>
                                                    <select class="theme-input-style" name="plan_id" id="plans">
                                                        <option value=""> {{ translate('Select Plan') }} </option>
                                                    </select>
                                                    @if ($errors->has('plan_id'))
                                                        <div class="text-danger mt-2">{{ $errors->first('plan_id') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-20 mt-2">
                                            <button type="submit" class="btn long w-100" class="submit-btn"
                                                id="register">
                                                <img src="{{ asset('/public/backend/assets/img/loader-w4.svg') }}"
                                                    alt="" class="d-none" id="loader">
                                                {{ translate('Register') }}
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-center">
                                            <p>{{ translate('Already have account? ') }}
                                                <a href="{{ route('subscriber.login') }}"
                                                    class="font-14">{{ translate('Sign In') }}</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_scripts')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <!--End Select2-->

    <script>
        $(document).ready(function() {
            "use strict";
            $('#registration-form').submit(function(event) {
                event.preventDefault()
                $('#register').prop('disabled', true)
                $('#loader').removeClass('d-none')
                this.submit();
            });
        })

        /**
         *  Will request for all plans of selected package
         */
        function getAllPlansOfPackage() {
            'use strict';
            $("#register").prop("disabled", true);
            let selected_package = $('#package').val();
            $('#selected_package').val(selected_package)
            $.ajax({
                url: '{{ route('plugin.saas.get.plans.according.to.package') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    package_id: selected_package
                },
                success: function(response) {
                    $("#register").prop("disabled", false);
                    if (response.success) {
                        let is_redeem_coupon = $('#is_redeem_coupon').val()
                        let lifetime_plan = '{{ config('saas.plans.lifetime') }}'

                        let plans = response.plans

                        let html = ``

                        for (let i = 0; i < plans.length; i++) {
                            html = html + `<option value='` + plans[i]['id'] + `'>` + plans[i]['name'] +
                                `</option>`
                        }
                        $('#plans').html(html)

                        if (is_redeem_coupon == '1') {
                            $('#plans').html(`<option value="` + lifetime_plan +
                                `" selected> {{ translate('Lifetime Plan') }} </option>`)
                            $('#plans').prop('disabled', true);
                        } else {
                            if (html == '') {
                                $('#plans').html(
                                    `<option value="-1"> {{ translate('Select Plan') }} </option>`)
                            }
                        }
                    } else {
                        toastr.error("{{ translate('No plan found with this package !') }}");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#register").prop("disabled", false);
                    toastr.error("{{ translate('No plan found with this package !') }}");
                }
            });
        }

        /**
         * Will request for package by given coupon
         */
        function getPackagesAccordingToCoupon() {
            'use strict';
            $("#register").prop("disabled", true);
            let coupon = $('#coupon').val();
            $.ajax({
                url: '{{ route('plugin.saas.get.packages.according.to.redeemable.coupon') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon: coupon
                },
                success: function(response) {
                    $("#register").prop("disabled", false);
                    if (response.success) {
                        var selectBox = $('#package');
                        var optionValue = response.package;
                        selectBox.find('option').each(function() {
                            if ($(this).val().toString() == optionValue.toString()) {
                                $(this).prop('selected', true);
                                $('#selected_package').val($(this).val())
                                return false;
                            }
                        });
                        selectBox.prop('disabled', true);
                        $('#is_redeem_coupon').val(1)
                        $('#coupon_error_msg').html('')
                        $('#coupon').prop('readonly', true)
                        getAllPlansOfPackage()
                    } else {
                        $("#register").prop("disabled", false);
                        $('#package').prop('disabled', false);
                        $('#coupon').val('');
                        $('#coupon_error_msg').html('{{ translate('This coupon is not applicable here') }}')
                        $('#coupon').val('')
                    }
                }
            });
        }
    </script>
@endsection
