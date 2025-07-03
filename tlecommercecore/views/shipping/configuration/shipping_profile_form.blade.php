@extends('core::base.layouts.master')
@section('title')
    {{ translate('Create Shipping Profile') }}
@endsection
@section('custom_css')
    <!--Select2-->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--End select2-->
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}" class="black"><i
                    class="icofont-long-arrow-left"></i></a>
            {{ translate('Create Shipping Profile') }}</h4>
    </div>
    <div class="row">
        <div class="mx-auto col-lg-6">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Profile Information') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('plugin.tlcommercecore.shipping.profile.store') }}">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Profile Name') }} </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" name="profile_name" class="theme-input-style"
                                    value="{{ old('profile_name') }}" placeholder="{{ translate('Type Name') }}">
                                @if ($errors->has('profile_name'))
                                    <div class="invalid-input">{{ $errors->first('profile_name') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Products') }} </label>
                            </div>
                            <div class="col-sm-12">
                                @php
                                    $products = Plugin\TlcommerceCore\Models\Product::whereNotIn('id', Plugin\TlcommerceCore\Models\ShippingProfileProducts::pluck('product_id'))
                                        ->select('name', 'id')
                                        ->get();
                                @endphp
                                <select class="product-select w-100" name="products[]" multiple>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->translation('name', getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('products'))
                                    <div class="invalid-input">{{ $errors->first('products') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Shipping From') }} </label>
                            </div>
                            <div class="col-sm-12 mb-20">
                                <input type="text" name="location" class="theme-input-style"
                                    value="{{ old('location') }}" placeholder="{{ translate('Location') }}">
                                @if ($errors->has('location'))
                                    <div class="invalid-input">{{ $errors->first('location') }}</div>
                                @endif
                            </div>
                            <div class="col-sm-12 mb-20">
                                <textarea name="address" placeholder="{{ translate('Address') }}" class="theme-input-style"></textarea>
                                @if ($errors->has('address'))
                                    <div class="invalid-input">{{ $errors->first('address') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit"
                                    class="btn long store-shiping-time-btn">{{ translate('Save') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('custom_scripts')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('.product-select').select2({
                    theme: "classic",
                    placeholder: '{{ translate('Select Product') }}',
                });
            });
        })(jQuery);
    </script>
@endsection
