@php
    $shippingZones = Plugin\TlcommerceCore\Models\ShippingZone::get();
    $languages = getAllLanguages();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Pickup Point Information') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-handshake-deal"></i> {{ translate('Pickup Point Information') }}</h4>
    </div>
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="row">
                <div class="col-12 mb-3">
                    <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong> version
                    </p>
                </div>
                <div class="col-12">
                    <ul class="nav nav-tabs nav-fill border-light border-0">
                        @foreach ($languages as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                                    href="{{ route('plugin.pickuppoint.edit.pickup.point', ['id' => $pick_up_point->id, 'lang' => $language->code]) }}">
                                    <img src="{{ project_asset('/flags/') . '/' . $language->code . '.png' }}"
                                        width="20px">
                                    <span>{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="form-element py-30 mb-30">
                <form action="{{ route('plugin.pickuppoint.update.pickup.point') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $pick_up_point->id }}">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <div class="form-row mb-20">
                        <div class="col-md-4">
                            <label class="font-14 bold black">{{ translate('Pickup Point Name') }}</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="name" class="theme-input-style"
                                value="{{ $pick_up_point->name }}"
                                placeholder="{{ translate('Give Pickup Point Name') }}">
                            @if ($errors->has('name'))
                                <div class="invalid-input">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row mb-20 {{ $lang != getdefaultlang() ? 'area-disabled' : '' }}">
                        <div class="col-md-4">
                            <label class="font-14 bold black">{{ translate('Pickup Point Phone') }}</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="phone" class="theme-input-style"
                                value="{{ $pick_up_point->phone }}"
                                placeholder="{{ translate('Give Pickup Point Phone') }}">
                            @if ($errors->has('phone'))
                                <div class="invalid-input">{{ $errors->first('phone') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="{{ $lang != getDefaultLang() ? 'area-disabled' : '' }}">
                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('Country') }}</label>
                            </div>
                            <div class="col-md-8">
                                <select name="country" id="country" class="theme-input-style country-select">
                                    @if ($pick_up_point->country != null)
                                        <option value="{{ $pick_up_point->country->id }}" selected>
                                            {{ $pick_up_point->country->translation('name') }}
                                        </option>
                                    @endif
                                </select>
                                @if ($errors->has('country'))
                                    <div class="invalid-input">{{ $errors->first('country') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('State') }}</label>
                            </div>
                            <div class="col-md-8">
                                <select id='state' name="state" class="theme-input-style state-select">
                                    @if ($pick_up_point->state != null)
                                        <option value="{{ $pick_up_point->state->id }}" selected>
                                            {{ $pick_up_point->state->translation('name') }}
                                        </option>
                                    @endif
                                </select>
                                @if ($errors->has('state'))
                                    <div class="invalid-input">{{ $errors->first('state') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-md-4">
                                <label class="font-14 bold black">{{ translate('City') }}</label>
                            </div>
                            <div class="col-md-8">
                                <select id='city' name="city" class="theme-input-style city-select">
                                    @if ($pick_up_point->city != null)
                                        <option value="{{ $pick_up_point->city->id }}" selected>
                                            {{ $pick_up_point->city->translation('name') }}
                                        </option>
                                    @endif
                                </select>
                                @if ($errors->has('city'))
                                    <div class="invalid-input">{{ $errors->first('city') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-20 {{ $lang != getDefaultLang() ? 'area-disabled' : '' }}">
                        <div class="col-md-4">
                            <label class="font-14 bold black">{{ translate('Location') }}</label>
                        </div>
                        <div class="col-md-8">
                            <textarea type="text" name="location" class="theme-input-style"
                                placeholder="{{ translate('Give pickup point location') }}">{{ $pick_up_point->location }}</textarea>
                            @if ($errors->has('location'))
                                <div class="invalid-input">{{ $errors->first('location') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row mb-20 {{ $lang != getDefaultLang() ? 'area-disabled' : '' }}">
                        <div class="col-md-4">
                            <label class="font-14 bold black">{{ translate('Status') }}</label>
                        </div>
                        <div class="col-md-8">
                            <label class="switch success">
                                <input type="checkbox" name="status" @checked($pick_up_point->status == config('settings.general_status.active'))>
                                <span class="control"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn long">{{ translate('Save Changes') }}</button>
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
                /**
                 * select country
                 * 
                 */
                $('.country-select').select2({
                    theme: "classic",
                    placeholder: '{{ translate('Select Country') }}',
                    ajax: {
                        url: '{{ route('plugin.tlcommercecore.product.cod.countries.dropdown.option') }}',
                        dataType: 'json',
                        method: "GET",
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    }
                });
                /**
                 * select  state
                 *
                 */
                $('.state-select').select2({
                    theme: "classic",
                    placeholder: '{{ translate('Select State') }}',
                    ajax: {
                        url: '{{ route('plugin.tlcommercecore.product.cod.state.dropdown.option') }}',
                        dataType: 'json',
                        method: "GET",
                        delay: 250,
                        data: function(params) {
                            return {
                                country: $("#country").val(),
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    }
                });
                /**
                 * select city 
                 *
                 */
                $('.city-select').select2({
                    theme: "classic",
                    placeholder: '{{ translate('Select City') }}',
                    ajax: {
                        url: '{{ route('plugin.tlcommercecore.product.cod.city.dropdown.option') }}',
                        dataType: 'json',
                        method: "GET",
                        delay: 250,
                        data: function(params) {
                            return {
                                state: $("#state").val(),
                                term: params.term || '',
                                page: params.page || 1
                            }
                        },
                        cache: true
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
