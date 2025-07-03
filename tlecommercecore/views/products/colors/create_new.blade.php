@extends('core::base.layouts.master')
@section('title')
    {{ translate('New Color') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="mb-3">
                <p class="alert alert-info">You are inserting <strong>"{{ getLanguageNameByCode(getDefaultLang()) }}"</strong> version</p>
            </div>
            <div class="form-element py-30 mb-30">
                <h4 class="font-20 mb-30">{{ translate('New Color') }}</h4>
                <form action="{{ route('plugin.tlcommercecore.product.colors.store') }}" method="POST">
                    @csrf
                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="name" class="theme-input-style " value="{{ old('name') }}"
                                placeholder="{{ translate('Type here') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row mb-20">
                        <div class="col-sm-4">
                            <label class="font-14 bold black">{{ translate('Code') }} </label>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group addon">
                                <input type="text" name="code" class="color-input form-control style--two"
                                    placeholder="#fffff" value="#FFFFF">
                                <div class="input-group-append">
                                    <input type="color" class="input-group-text theme-input-style2 color-picker"
                                        id="colorPicker" value="#a21010" oninput="selectColor(event,this.value)">
                                </div>
                            </div>
                            @if ($errors->has('code'))
                                <div class="invalid-input">{{ $errors->first('code') }}</div>
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
    <script>
        function selectColor(e, color) {
            "use strict";
            let target = e.target;
            $(target).closest('.addon').find('.color-input').val(color);
        }
    </script>
@endsection
