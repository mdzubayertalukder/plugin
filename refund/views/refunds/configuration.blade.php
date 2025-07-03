@extends('core::base.layouts.master')
@section('title')
    {{ translate('Refund Configuration') }}
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-5 mx-auto">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Refund Configuration') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('plugin.tlcommercecore.refunds.configuration.update') }}">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-sm-6">
                                <label class="font-14 bold black ">{{ translate('Enable Refund System') }} </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="switch glow primary medium">
                                    <input type="checkbox" name="is_active" class="refund-system" onchange="refundConfig()"
                                        {{ $config->is_active == '1' ? 'checked' : '' }}>
                                    <span class="control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="refund-config @if ($config->is_active != '1') d-none @endif">
                            <div class="form-row mb-20">
                                <label class="font-14 bold black  col-sm-4">{{ translate('Refund Time') }} </label>
                                <div class="input-group addon col-sm-8">
                                    <input type="text" name="refund_time" class="form-control style--two"
                                        value="{{ $config->refund_time }}">
                                    <div class="input-group-append">
                                        <div class="input-group-text px-3  bold">Days</div>
                                    </div>
                                </div>
                                @if ($errors->has('refund_time'))
                                    <div class="invalid-input">{{ $errors->first('refund_time') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn long">{{ translate('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_scripts')
    <script>
        /**
         * Enable and disable attachment on purchase
         * 
         */
        function refundConfig() {
            "use strict";
            if ($('.refund-system').is(":checked")) {
                $('.refund-config').removeClass('d-none')
            } else {
                $('.refund-config').addClass('d-none')
            }
        }
    </script>
@endsection
