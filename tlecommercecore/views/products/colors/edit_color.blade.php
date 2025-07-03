@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Color') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-plugin"></i> {{ translate('Edit Color') }}</h4>
    </div>
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="row">
                <div class="col-12 mb-3">
                    <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong> version</p>
                </div>
                <div class="col-12">
                    <ul class="nav nav-tabs nav-fill border-light border-0">
                        @foreach ($languages as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                                    href="{{ route('plugin.tlcommercecore.product.colors.edit', ['id' => $color_details->id, 'lang' => $language->code]) }}">
                                    <img src="{{ asset('/public/flags/') . '/' . $language->code . '.png' }}"
                                        width="20px"> <span>{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="form-element py-30 mb-30">
                <form action="{{ route('plugin.tlcommercecore.product.colors.update') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" name="id" value="{{ $color_details->id }}">
                            <input type="hidden" name="lang" value="{{ $lang }}">
                            <input type="text" name="name" class="theme-input-style"
                                value="{{ $color_details->translation('name', $lang) }}"
                                placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row mb-20 @if (!empty($lang) && $lang != getdefaultlang()) area-disabled @endif">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Code') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group addon">
                                <input type="text" name="code" class="color-input form-control style--two"
                                    placeholder="#fffff" value="{{ $color_details->code }}">
                                <div class="input-group-append">
                                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                                        id="colorPicker" value="{{ $color_details->code }}"
                                        oninput="selectColor(event,this.value)">
                                </div>
                            </div>
                            @if ($errors->has('code'))
                                <div class="invalid-input">{{ $errors->first('code') }}</div>
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
@endsection
@section('custom_scripts')
    <script>
        function selectColor(e, color) {
            "use strict";
            let target = e.target;
            $(target).closest('.addon').find('.color-input').val(color);
        }
    </script>
@endsection
