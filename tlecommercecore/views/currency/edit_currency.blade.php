@php
    $currency_position = getCurrencyPosition();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Currency') }}
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h4>{{ translate('Edit Currency') }}</h4>
                </div>
                <div class="card-body">
                    <div>
                        <form action="{{ route('plugin.tlcommercecore.ecommerce.update.currency') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $currency->id }}">
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Name') }} </label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="name" class="theme-input-style"
                                        value="{{ $currency->name }}" placeholder="{{ translate('Type Name') }}">
                                    @if ($errors->has('name'))
                                        <div class="invalid-input">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Symbol') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="symbol" class="theme-input-style currency-font"
                                        value="{{ $currency->symbol }}" placeholder="{{ translate('Symbol') }}">
                                    @if ($errors->has('symbol'))
                                        <div class="invalid-input">{{ $errors->first('symbol') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Code') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="code" class="theme-input-style"
                                        value="{{ $currency->code }}" placeholder="{{ translate('Code') }}">
                                    @if ($errors->has('code'))
                                        <div class="invalid-input">{{ $errors->first('code') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Exchange Rate With USD') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="exchange_rate" class="theme-input-style"
                                        value="{{ $currency->conversion_rate }}"
                                        placeholder="{{ translate('Exchange Rate With USD') }}">
                                    @if ($errors->has('exchange_rate'))
                                        <div class="invalid-input">{{ $errors->first('exchange_rate') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Currency Position') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="currency-position form-control" name="position" id="currency_position"
                                        placeholder="{{ translate('Select currency position') }}">
                                        @foreach ($currency_position as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ $currency->position == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('position'))
                                        <div class="invalid-input">{{ $errors->first('position') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Thousand separator') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="thousand_separator" class="theme-input-style"
                                        value="{{ $currency->thousand_separator }}"
                                        placeholder="{{ translate('Thousand separator') }}">
                                    @if ($errors->has('thousand_separator'))
                                        <div class="invalid-input">{{ $errors->first('thousand_separator') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Decimal separator') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="decimal_separator" class="theme-input-style"
                                        value="{{ $currency->decimal_separator }}"
                                        placeholder="{{ translate('Decimal separator') }}">
                                    @if ($errors->has('decimal_separator'))
                                        <div class="invalid-input">{{ $errors->first('decimal_separator') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Number of decimals') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" name="number_of_decimal" class="theme-input-style"
                                        value="{{ $currency->number_of_decimal }}" placeholder="0">
                                    @if ($errors->has('number_of_decimal'))
                                        <div class="invalid-input">{{ $errors->first('number_of_decimal') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Status') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <label class="switch medium">
                                        <input type="checkbox" {{ $currency->status == 1 ? 'checked' : '' }}
                                            name="status">
                                        <span class="control"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
