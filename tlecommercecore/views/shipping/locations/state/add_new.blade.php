@extends('core::base.layouts.master')
@section('title')
    {{ translate('New State') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
@endsection
@section('main_content')
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('New State') }}</h4>
                <form action="{{ route('plugin.tlcommercecore.shipping.locations.states.new.store') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="theme-input-style" value="{{ old('name') }}"
                                placeholder="{{ translate('Type Name') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Code') }}</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="code" class="theme-input-style" value="{{ old('code') }}"
                                placeholder="{{ translate('Type  Here') }}">
                            @if ($errors->has('code'))
                                <div class="invalid-input">{{ $errors->first('code') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Country') }}</label>
                        </div>
                        <div class="col-sm-8">
                            <select class="countrySelect form-control" name="country"
                                placeholder="{{ translate('Select a option') }}">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('country'))
                                <div class="invalid-input">{{ $errors->first('country') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('.countrySelect').select2({
                    theme: "classic",
                });
            });
        })(jQuery);
    </script>
@endsection
