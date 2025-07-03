@extends('core::base.layouts.master')
@section('title')
    {{ translate('Saas General Settings') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-7 mb-30">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-1">{{ translate('Saas General Settings') }}</h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('plugin.saas.admin.store.general.settings') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Select Default Currency') }}</label>
                            </div>
                            <div class="col-md-8">
                                <select id='selectCurrency' name="default_currency" class="theme-input-style">
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" class="text-uppercase"
                                            @selected($currency->id == $settings_data['default_currency'])>{{ $currency->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('currency'))
                                    <div class="invalid-input">{{ $errors->first('currency') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Email varification') }}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="email_verification" @checked($settings_data['email_verification'] == 1)>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label
                                    class="font-14 bold black">{{ translate('Auto approve store creation request') }}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="auto_approve_subscription_request"
                                        @checked($settings_data['auto_approve_subscription_request'] == 1)>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label
                                    class="font-14 bold black">{{ translate('Days between initial warning and subscription ends') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" min="1" name="notify_before_expired_days"
                                    class="theme-input-style" type="number"
                                    min="1"value="{{ $settings_data['notify_before_expired_days'] }}">
                                @if ($errors->has('notify_before_expired_days'))
                                    <div class="invalid-input">{{ $errors->first('notify_before_expired_days') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Interval days between warnings') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" min="1" name="notify_before_expired_interval_days"
                                    class="theme-input-style"
                                    value="{{ $settings_data['notify_before_expired_interval_days'] }}">
                                @if ($errors->has('notify_before_expired_interval_days'))
                                    <div class="invalid-input">
                                        {{ $errors->first('notify_before_expired_interval_days') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label
                                    class="font-14 bold black">{{ translate('Maximum Free Store A Subscriber Can Create') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" min="1" name="maximum_free_store" class="theme-input-style"
                                    value="{{ $settings_data['maximum_free_store'] }}">
                                @if ($errors->has('maximum_free_store'))
                                    <div class="invalid-input">{{ $errors->first('maximum_free_store') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Database Prefix') }}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="teext" name="database_prefix" class="theme-input-style"
                                    value="{{ $settings_data['database_prefix'] }}">
                                @if ($errors->has('database_prefix'))
                                    <div class="invalid-input">{{ $errors->first('database_prefix') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn long">{{ translate('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5 mb-30">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-1">{{ translate('Tenant Payment Methods') }}</h4>
                </div>
                <div class="card-body py-0">
                    <div class="note info mt-2">
                        <p class="media align-items-sm-center black font-14 flex-column flex-sm-row">
                            <span class="mb-2 mb-sm-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25"
                                    class="svg replaced-svg">
                                    <g id="info2" transform="translate(1 1)">
                                        <circle id="Ellipse_76" data-name="Ellipse 76" cx="11.5" cy="11.5"
                                            r="11.5" fill="#ccf5f8" stroke="#09d1de" stroke-width="2"></circle>
                                        <path id="Path_480" data-name="Path 480"
                                            d="M109.658,13.319l-.218.89q-.98.387-1.563.589a4.123,4.123,0,0,1-1.356.2,2.706,2.706,0,0,1-1.844-.578,1.872,1.872,0,0,1-.658-1.469,5.2,5.2,0,0,1,.049-.707q.05-.362.159-.816l.816-2.889q.109-.415.184-.787a3.418,3.418,0,0,0,.074-.677,1.047,1.047,0,0,0-.228-.772,1.29,1.29,0,0,0-.873-.218,2.287,2.287,0,0,0-.649.1c-.222.066-.412.129-.571.188l.218-.891q.8-.327,1.535-.559a4.59,4.59,0,0,1,1.388-.233,2.64,2.64,0,0,1,1.816.569,1.889,1.889,0,0,1,.638,1.479c0,.126-.014.347-.044.663a4.434,4.434,0,0,1-.164.871l-.813,2.877a7.641,7.641,0,0,0-.179.793,4.079,4.079,0,0,0-.079.672.969.969,0,0,0,.256.782,1.445,1.445,0,0,0,.889.208,2.567,2.567,0,0,0,.672-.1A3.759,3.759,0,0,0,109.658,13.319Zm.206-11.5A1.684,1.684,0,0,1,109.3,3.1a1.935,1.935,0,0,1-1.369.53,1.957,1.957,0,0,1-1.376-.53,1.68,1.68,0,0,1-.574-1.281,1.7,1.7,0,0,1,.574-1.286,2.031,2.031,0,0,1,2.745,0A1.7,1.7,0,0,1,109.864,1.819Z"
                                            transform="translate(-95.039 4)" fill="#09d1de"></path>
                                    </g>
                                </svg>
                            </span>
                            <span class="media-body">
                                This active payment methods are visible on the package create and edit page on Payment
                                Gateways feature.
                            </span>
                        </p>
                    </div>
                    <ul class="p-0">
                        @foreach ($tenant_payment_methods as $payment_method)
                            <li class="align-items-center d-flex justify-content-between border-bottom py-3">
                                <label class="font-16 bold black text-capitalize">{{ $payment_method->name }}</label>
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="payment_method" class="payment-method"
                                        data-id="{{ $payment_method->id }}" @checked($payment_method->status == 1)>
                                    <span class="control"></span>
                                </label>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>
        @include('core::base.media.partial.media_modal')
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initDropzone()
            $(document).ready(function() {
                is_for_browse_file = true
                filtermedia()
                $('#seectRole').select2()
            });
            //Update status
            $('.payment-method').on('change', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        id: id
                    },
                    url: '{{ route('plugin.saas..tenant.payments.methods.status.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ translate('Status Updated Successfully') }}',
                                "Success");
                            location.reload();
                        } else {
                            toastr.error('{{ translate('Status update failed') }}', "Error!");
                        }
                    },
                    error: function(response) {
                        toastr.error('{{ translate('Status update failed') }}', "Error!");
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
