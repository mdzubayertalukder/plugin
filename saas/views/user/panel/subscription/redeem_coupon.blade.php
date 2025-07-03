@php
    $plans = getAllPlans();
@endphp
@extends('plugin/saas::user.panel.layouts.user_dashboard_layout')
@section('title')
    {{ translate('Redeem Coupon') }}
@endsection
@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--  End select2  -->
@endsection
@section('main_content')
    <!-- Main Content -->
    <div class="col-5">
        <form class="form-horizontal" action="{{ route('plugin.saas.apply.redeem.coupon') }}" method="post" id="redeem-coupon">
            @csrf
            <div class="card">
                <div class="card-header bg-white py-3 border-0">
                    <h4 class="font-20">{{ translate('Redeem Coupon') }}</h4>
                </div>
                <div class="card-body">
                    <div class="form-group mb-20">
                        <input type="text" name="coupon_code" id="coupon_code" class="theme-input-style"
                            value="{{ old('coupon_code') }}" placeholder="{{ translate('Coupon Code') }}" required>
                        @if ($errors->has('coupon_code'))
                            <p class="text-danger text-left">{{ $errors->first('coupon_code') }}</p>
                        @endif
                    </div>
                    <div class="form-group mb-20">
                        <input type="text" name="store_name" id="store_name" class="theme-input-style"
                            value="{{ old('store_name') }}" placeholder="{{ translate('Store Name') }}" required>
                        @if ($errors->has('store_name'))
                            <p class="text-danger text-left">{{ $errors->first('store_name') }}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <button class="btn sm" type="submit" id="create-store">
                            <img src="{{ asset('/public/backend/assets/img/loader-w4.svg') }}" alt=""
                                class="d-none" id="loader">
                            {{ translate('Redeem') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End Main Content -->
@endsection

@section('custom_scripts')
    <script>
        $(document).ready(function() {
            'use strict'
            $('#redeem-coupon').submit(function(event) {
                event.preventDefault()
                $('#create-store').prop('disabled', true)
                $('#loader').removeClass('d-none')
                this.submit();
            });
        });
    </script>
@endsection
