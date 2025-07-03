@php
    $languages = \Core\Models\Language::where('status', config('settings.general_status.active'))
        ->select('id', 'name', 'code', 'native_name')
        ->get();
    $lang = request()->get('lang');
@endphp

@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Country') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
@endsection
@section('main_content')
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="mb-3">
                <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong>
                    version
                </p>
            </div>
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <h4>{{ translate('Country Information') }}</h4>
                </div>
                <div class="card-body">
                    <!--Language Switcher-->
                    <ul class="nav nav-tabs nav-fill border-light border-0 mb-20">
                        @foreach ($languages as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                                    href="{{ route('plugin.tlcommercecore.shipping.locations.country.edit', ['id' => $countryDetails->id, 'lang' => $language->code]) }}">
                                    <img src="{{ asset('/public/flags/') . '/' . $language->code . '.png' }}"
                                        width="20px">
                                    <span>{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <!--End Language Switcher--->
                    <form action="{{ route('plugin.tlcommercecore.shipping.locations.country.update') }}" method="POST">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-sm-4">
                                <label class="font-14 bold black">{{ translate('Name') }} </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="hidden" name="id" value="{{ $countryDetails->id }}">
                                <input type="hidden" name="lang" value="{{ $lang }}">
                                <input type="text" name="name" class="theme-input-style"
                                    value="{{ $countryDetails->translation('name', $lang) }}"
                                    placeholder="{{ translate('Type Name') }}">
                                @if ($errors->has('name'))
                                    <div class="invalid-input">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>

                        <div
                            class="form-row mb-20 {{ !empty($lang) && $lang != getdefaultlang() ? 'area-disabled' : '' }}">
                            <div class="col-sm-4">
                                <label class="font-14 bold black">{{ translate('Code') }}</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" name="code" class="theme-input-style"
                                    value="{{ $countryDetails->code }}" placeholder="{{ translate('Enter Code') }}">
                                @if ($errors->has('code'))
                                    <div class="invalid-input">{{ $errors->first('code') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn long">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
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
                $('.countryCodeSelect').select2({
                    theme: "classic",
                    templateResult: formatState,
                    templateSelection: formatState
                });
            });
            //Generate country code select options
            function formatState(opt) {
                var base_path = "{{ url('/') }}";
                if (!opt.id) {
                    return opt.text.toUpperCase();
                }
                var image = $(opt.element).attr('data-image');
                var optimage = base_path + '/public/flags/' + image + '.png';
                if (!optimage) {
                    return opt.text.toUpperCase();
                } else {
                    var $opt = $(
                        '<span><img src="' + optimage + '" width="20px" class="mr-2" /> ' + opt.text
                        .toUpperCase() + '</span>'
                    );
                    return $opt;
                }
            };
        })(jQuery);
    </script>
@endsection
