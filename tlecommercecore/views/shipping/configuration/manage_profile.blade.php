@extends('core::base.layouts.master')
@section('title')
    {{ translate('Manage Profile') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    @include('core::base.includes.data_table.css')
    <style>
        .select2-container {
            width: 100% !important;
        }

        .item-toggle-btn.collapsed {
            transform: rotate(180deg);
        }

        .input-group {
            min-width: 150px !important;
        }

        .location-box {
            min-height: 400px;
        }

        .edit-location-box {
            min-height: 400px;
        }
    </style>
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}" class="black"><i
                    class="icofont-long-arrow-left"></i></a> {{ $profile_info->name }}</h4>
    </div>
    <div class="row">
        <div class="col-12">
            <!--Profile Info-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Profile Information') }}</h4>
                        <div class="d-flex flex-wrap mt-3 mt-sm-0">
                            <button data-toggle="modal" data-target="#manage-profile"
                                class="btn long">{{ translate('Update Profile') }}</button>
                            <span class="btn-home ml-2 item-toggle-btn" data-toggle="collapse"
                                data-target="profile-name-body">
                                <a href="#"><i class="icofont-simple-up"></i></i></a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="profile-name-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="black mb-1">{{ translate('Profile') }}</h5>
                            <p class="black mb-0">{{ translate('Name') }} : {{ $profile_info->name }}</p>
                            <p class="black mb-0">{{ translate('Products') }} : {{ count($profile_info->products) }}</p>
                        </div>
                        <div class="col-lg-6">
                            <h5 class="black mb-1">{{ translate('Shipping From') }}:</h5>
                            <p class="black mb-0"><i class="icofont-location-pin"></i>{{ $profile_info->location }}</p>
                            <p class="black mb-0">{{ $profile_info->address }}</p>
                        </div>
                    </div>

                </div>
            </div>
            <!--End Profile Info-->
            <!--Products-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Products') }}</h4>
                        <div class="d-flex flex-wrap mt-3  mt-sm-0">
                            <button data-toggle="modal" data-target="#manage-products-modal"
                                class="btn long">{{ translate('Add New Products') }}</button>
                            <span class="btn-home ml-2 item-toggle-btn" data-toggle="collapse" data-target="product-body">
                                <a href="#"><i class="icofont-simple-down"></i></i></a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body {{ request()->has('module') && request()->has('module') == 'products' ? '' : 'hidden' }}"
                    id="product-body">
                    @if (count($profile_info->products) > 0)
                        <div class="table-responsive">
                            <table id="productTable" class="hoverable text-nowrap border-top2">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>{{ translate('Product') }}</th>
                                        <th class="text-right">{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($profile_info->products as $key => $product)
                                        @if ($product->product_details != null)
                                            <tr>
                                                <td>
                                                    {{ $key + 1 }}
                                                </td>
                                                <td>
                                                    <img src="{{ asset(getFilePath($product->product_details->thumbnail_image)) }}"
                                                        class="img-45" alt="{{ $product->product_details->name }}">
                                                    {{ $product->product_details->name }}
                                                </td>
                                                <td class="text-right">
                                                    <button class="btn-danger btn-sm remove-product"
                                                        data-product="{{ $product->product_details->id }}">
                                                        {{ translate('Remove Item') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="alert alert-danger">{{ translate('No products') }}</p>
                    @endif
                </div>
            </div>
            <!--End Products-->
            <!--Shipping To-->
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Shipping Zone & Rate') }}</h4>
                        @if (getEcommerceSetting('enable_carrier_in_checkout') != config('settings.general_status.active'))
                            <a href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'checkout']) }}"
                                class="btn-link">
                                {{ translate('Enable Carrier Rate') }}
                            </a>
                        @endif
                        <div class="d-flex flex-wrap mt-3 mt-sm-0">
                            <button
                                class="btn long open-zone-create-modal">{{ translate('Create Shipping Zone') }}</button>
                            <span class="btn-home ml-2 item-toggle-btn" data-toggle="collapse" data-target="shipping-to">
                                <a href="#"><i class="icofont-simple-up"></i></i></a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="shipping-to">
                    @if (count($profile_info->zones) > 0)
                        @foreach ($profile_info->zones as $key => $zone)
                            <div class="shipping-zone mb-2 border-bottom2">
                                <div class="d-flex justify-content-between align-items-center py-2 zone-header">
                                    <div class="d-block">
                                        <h4><i class="icofont-location-pin"></i> {{ $zone->name }}</h4>
                                        <p class="ml-4">
                                            <span>{{ count($zone->cities) }} {{ translate('Cities') }}</span>
                                        </p>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        <div class="dropdown-button">
                                            <a href="#" class="d-flex align-items-center justify-content-end"
                                                data-toggle="dropdown">
                                                <div class="menu-icon mr-0">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="#" class="edit-shipping-zone"
                                                    data-zone="{{ $zone->id }}"
                                                    data-profile="{{ $profile_info->id }}">
                                                    {{ translate('Edit Zone') }}
                                                </a>
                                                <a href="#" class="delete-zone"
                                                    data-zone="{{ $zone->id }}">{{ translate('Delete Zone') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="zone-rates pb-3">
                                    @if (count($zone->rates) > 0)
                                        @if (isActivePluging('carrier'))
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="own-rate-tab" data-toggle="tab"
                                                        href="#ownRate{{ $zone->id }}" role="tab"
                                                        aria-controls="ownRate{{ $zone->id }}"
                                                        aria-selected="true">{{ translate('Own Rates') }}</a>
                                                </li>
                                                @if (getEcommerceSetting('enable_carrier_in_checkout') == config('settings.general_status.active'))
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="carrier-rate-tab" data-toggle="tab"
                                                            href="#carrierRate{{ $zone->id }}" role="tab"
                                                            aria-controls="carrierRate{{ $zone->id }}"
                                                            aria-selected="false">{{ translate('Carrier Rates') }}</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif
                                        <div class="tab-content">
                                            <!--Own Rate-->
                                            <div class="tab-pane fade show active" id="ownRate{{ $zone->id }}">
                                                @if (count($zone->own_rates) > 0)
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ translate('Rate Name') }}</th>
                                                                    <th>{{ translate('Shipping Time') }}</th>
                                                                    <th>{{ translate('Condition') }}</th>
                                                                    <th>{{ translate('Shipping Cost') }}</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($zone->own_rates as $rate)
                                                                    <tr>
                                                                        <td>{{ $rate->name }}</td>
                                                                        <td>
                                                                            @if ($rate->shipping_time != null)
                                                                                {{ $rate->shipping_time->min_value }}
                                                                                {{ translate($rate->shipping_time->min_unit) }}
                                                                                -
                                                                                {{ $rate->shipping_time->max_value }}
                                                                                {{ translate($rate->shipping_time->max_unit) }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($rate->has_condition == config('settings.general_status.active'))
                                                                                @if ($rate->based_on == config('tlecommercecore.shipping_based_on.weight_based'))
                                                                                    <span>{{ $rate->min_limit }}{{ $rate->condition_unit }}</span>
                                                                                    -
                                                                                    <span>{{ $rate->max_limit }}{{ $rate->condition_unit }}</span>
                                                                                @else
                                                                                    <span>{!! currencyExchange($rate->min_limit) !!}</span>
                                                                                    -
                                                                                    <span>{!! currencyExchange($rate->max_limit) !!}</span>
                                                                                @endif
                                                                            @else
                                                                                <span> -</span>
                                                                            @endif
                                                                        <td>
                                                                            <span>{!! currencyExchange($rate->shipping_cost) !!}</span>
                                                                        </td>

                                                                        <td>
                                                                            <div class="dropdown-button">
                                                                                <a href="#"
                                                                                    class="d-flex align-items-center justify-content-end"
                                                                                    data-toggle="dropdown">
                                                                                    <div class="menu-icon mr-0">
                                                                                        <span></span>
                                                                                        <span></span>
                                                                                        <span></span>
                                                                                    </div>
                                                                                </a>
                                                                                <div
                                                                                    class="dropdown-menu dropdown-menu-right">
                                                                                    <a href="#" class="edit-rate"
                                                                                        data-rate="{{ $rate->id }}">
                                                                                        {{ translate('Edit Rate') }}
                                                                                    </a>
                                                                                    <a href="#" class="delete-rate"
                                                                                        data-rate="{{ $rate->id }}">{{ translate('Delete Rate') }}</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="alert alert-danger">
                                                        {{ translate('No own rates') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <!--End Own Rate-->
                                            <!--Carrier Rate-->
                                            @if (getEcommerceSetting('enable_carrier_in_checkout') == config('settings.general_status.active'))
                                                <div class="tab-pane fade show" id="carrierRate{{ $zone->id }}">
                                                    @if (count($zone->carrier_rates) > 0)
                                                        <div class="table-responsive">
                                                            <table>
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ translate('Carrier') }}</th>
                                                                        <th>{{ translate('Shipping Time') }}</th>
                                                                        <th>{{ translate('Shipping Medium') }}</th>
                                                                        <th>{{ translate('Weight Range') }}</th>
                                                                        <th>{{ translate('Shipping Cost') }}</th>
                                                                        <th>{{ translate('Action') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($zone->carrier_rates as $rate)
                                                                        <tr>
                                                                            <td>
                                                                                @if ($rate->carrier() != null)
                                                                                    {{ $rate->carrier->name }}
                                                                                @else
                                                                                    <span>N/A</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($rate->shipping_time != null)
                                                                                    {{ $rate->shipping_time->min_value }}
                                                                                    {{ translate($rate->shipping_time->min_unit) }}
                                                                                    -
                                                                                    {{ $rate->shipping_time->max_value }}
                                                                                    {{ translate($rate->shipping_time->max_unit) }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ $rate->shippied_by() }}</td>
                                                                            <td>
                                                                                @if ($rate->has_condition == config('settings.general_status.active'))
                                                                                    @if ($rate->based_on == config('tlecommercecore.shipping_based_on.weight_based'))
                                                                                        <span>{{ $rate->min_limit }}{{ $rate->condition_unit }}</span>
                                                                                        -
                                                                                        <span>{{ $rate->max_limit }}{{ $rate->condition_unit }}</span>
                                                                                    @else
                                                                                        <span>{!! currencyExchange($rate->min_limit) !!}</span>
                                                                                        -
                                                                                        <span>{!! currencyExchange($rate->max_limit) !!}</span>
                                                                                    @endif
                                                                                @else
                                                                                    <span> -</span>
                                                                                @endif
                                                                            <td>
                                                                                <span>{!! currencyExchange($rate->shipping_cost) !!}</span>
                                                                            </td>

                                                                            <td>
                                                                                <div class="dropdown-button">
                                                                                    <a href="#"
                                                                                        class="d-flex align-items-center justify-content-end"
                                                                                        data-toggle="dropdown">
                                                                                        <div class="menu-icon mr-0">
                                                                                            <span></span>
                                                                                            <span></span>
                                                                                            <span></span>
                                                                                        </div>
                                                                                    </a>
                                                                                    <div
                                                                                        class="dropdown-menu dropdown-menu-right">
                                                                                        <a href="#"
                                                                                            class="edit-rate"
                                                                                            data-rate="{{ $rate->id }}">
                                                                                            {{ translate('Edit Rate') }}
                                                                                        </a>
                                                                                        <a href="#"
                                                                                            class="delete-rate"
                                                                                            data-rate="{{ $rate->id }}">{{ translate('Delete Rate') }}</a>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <p class="alert alert-danger">
                                                            {{ translate('No carrier rates') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                            <!--End Carrier Rate-->
                                        </div>
                                    @else
                                        <p class="alert alert-danger">
                                            {{ translate("No rates. Customers in this zone won't be able to complete checkout") }}
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <button class="btn-info btn-sm new-rate"
                                            data-zone="{{ $zone->id }}">{{ translate('Add New Rate') }}
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    @else
                        <div>
                            <p class="alert alert-danger">{{ translate('No shipping zone availble') }}</p>
                        </div>
                    @endif
                </div>

            </div>
            <!--End Shipping To-->
        </div>
    </div>
    <!--Delete Zone Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.shipping.zones.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-zone-id" name="id">
                        <input type="hidden" name="profile_id" value="{{ $profile_info->id }}">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete  Zone Modal-->
    <!--Delete Rate Modal-->
    <div id="delete-rate-modal" class="delete-rate-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.shipping.delete.rate') }}">
                        @csrf
                        <input type="hidden" id="delete-rate-id" name="id">
                        <input type="hidden" name="profile_id" value="{{ $profile_info->id }}">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Rate Modal-->
    <!--Zone Create Modal-->
    <div id="zone-create-modal" class="zone-create-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Create New Shipping Zone') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-zone-create-form">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Zone Name') }} </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="hidden" name="profile_id" value="{{ $profile_info->id }}">
                                <input type="text" name="name" class="theme-input-style"
                                    value="{{ old('name') }}" placeholder="{{ translate('Enter name') }}">
                                <small>{{ translate('Not visible to customers') }}</small>
                                @if ($errors->has('name'))
                                    <div class="invalid-input">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-2 my-auto">
                                <label class="font-14 bold black">{{ translate('Search') }} </label>
                            </div>
                            <div class="col-sm-10">
                                <div class="input-group addon radius-50 ov-hidden">
                                    <input type="text" name="location_search" id="location_search"
                                        class="form-control style--two" value=""
                                        placeholder="{{ translate('Search') }}">
                                    <div class="input-group-append search-btn">
                                        <span class="input-group-text bg-light pointer">
                                            <i class="icofont-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-12 location-box">
                                <ul class="cl-start-wrap pl-1 location-options">

                                </ul>
                                <div class="d-flex justify-content-center loader">
                                    <button type="button" class="btn sm">{{ translate('Load More') }}</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button class="btn long create-new-zone">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Zone Create Modal-->
    <!--Edit Shipping Zone Modal-->
    <div id="edit-zone-modal" class="edit-zone-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Shipping Zone Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="edit-zone-data">

                </div>
            </div>
        </div>
    </div>
    <!--End Edit Shipping Zone Modal-->
    <!--New Rate Modal-->
    <div id="new-rate-modal" class="new-rate-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Add New Rate') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="shipping-rate-form">
                        @if (isActivePluging('carrier'))
                            <!--Shipping rate type-->
                            <div class="form-row mb-20">
                                <div class="d-flex w-100">
                                    <div class="align-items-center d-flex mr-30">
                                        <div class="custom-radio mr-1">
                                            <input type="radio" name="rate_type" id="own_rate"
                                                value="{{ config('tlecommercecore.shipping_rate_type.own_rate') }}"
                                                class="rate-type-switcher" checked>
                                            <label for="own_rate"></label>
                                        </div>
                                        <label for="own_rate"
                                            class="black font-14 mb-0">{{ translate('Own Rate') }}</label>
                                    </div>
                                    @if (getEcommerceSetting('enable_carrier_in_checkout') == config('settings.general_status.active'))
                                        <div class="align-items-center d-flex">
                                            <div class="custom-radio mr-1">
                                                <input type="radio" name="rate_type" id="courier_rate"
                                                    value="{{ config('tlecommercecore.shipping_rate_type.carrier_rate') }}"
                                                    class="rate-type-switcher">
                                                <label for="courier_rate"></label>
                                            </div>
                                            <label for="courier_rate"
                                                class="black font-14 mb-0">{{ translate('Carrier Rate') }}</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!--End Shipping rate type-->
                        @endif
                        <!--Shipping time-->
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Shipping Time') }}</label>
                                @if (count($shipping_time) > 0)
                                    <select class="theme-input-style time-select" name="shipping_time">
                                        @foreach ($shipping_time as $time)
                                            <option data-min="{{ $time->min_value }}"
                                                data-min-unit="{{ $time->min_unit }}"
                                                data-max="{{ $time->max_value }}"
                                                data-max-unit="{{ $time->max_unit }}" value="{{ $time->id }}">
                                                {{ $time->min_value }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="alert alert-danger">{{ translate('No shipping time found') }}</p>
                                    <a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}#ShippingTimes"
                                        class="btn-link">{{ translate('Add new shipping time') }}</a>
                                @endif
                            </div>
                        </div>
                        <!--End Shipping Time-->

                        <div class="carrier-shipping-rate d-none">
                            <!--Carrier Selector-->
                            <div class="form-row mb-20 courier-selector">
                                <div class="col-sm-12">
                                    <label class="font-14 bold black">{{ translate('Carrier') }} <span
                                            class="text text-danger">*</span></label>
                                    @if (count($couriers) > 0)
                                        <select name="courier" class="theme-input-style">
                                            <option>{{ translate('Select a carrier') }}</option>
                                            @foreach ($couriers as $courier)
                                                <option value="{{ $courier['id'] }}">{{ $courier['name'] }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <p class="alert alert-danger">{{ translate('No carrier found') }}</p>
                                        <a href="{{ route('plugin.tlcommercecore.shipping.configuration') }}#ShippingCarriers"
                                            class="btn-link">{{ translate('Add new carrier') }}</a>
                                    @endif
                                </div>
                            </div>
                            <!--End Carrier Selector-->
                            <!--Shipped By-->
                            <div class="form-row mb-20 courier-selector">
                                <label class="font-14 bold black">{{ translate('Shipped By') }}<span
                                        class="text text-danger">*</span> </label>
                                <select name="shipped_by" class="theme-input-style">
                                    <option>{{ translate('Select a medium') }}</option>
                                    <option value="{{ config('tlecommercecore.shipped_by.by_air') }}">
                                        {{ translate('Air Freight') }}</option>
                                    <option value="{{ config('tlecommercecore.shipped_by.by_ship') }}">
                                        {{ translate('Ocean Freight') }}</option>
                                    <option value="{{ config('tlecommercecore.shipped_by.by_rail') }}">
                                        {{ translate('Rail Freight') }}</option>
                                    <option value="{{ config('tlecommercecore.shipped_by.by_train') }}">
                                        {{ translate('Road Freight') }}</option>
                                </select>
                            </div>
                            <!--End Shipped By-->
                            <!--Carrier Shipping Condition-->
                            <div class="form-row mb-20">
                                <table class="table table-responsive">
                                    <thead>
                                        <th>
                                            <label
                                                class="font-14 bold black">{{ translate('Minimum Volumetric Weight') }}</label>
                                        </th>
                                        <th>
                                            <label
                                                class="font-14 bold black">{{ translate('Maximum Volumetric Weight') }}</label>
                                        </th>
                                        <th>
                                            <label class="font-14 bold black">{{ translate('Shipping Cost') }}</label>
                                        </th>
                                        <th>
                                            <label class="font-14 bold black">{{ translate('Action') }}</label>
                                        </th>
                                    </thead>
                                    <tbody id="carrier-shipping-weight-range">
                                        <tr>
                                            <td>
                                                <div class="input-group addon">
                                                    <input type="text" name="carrier_condition[0][min_weight]"
                                                        class="form-control style--two" placeholder="0.00">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text px-3 bold">{{ translate('Kg') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group addon">
                                                    <input type="text" name="carrier_condition[0][max_weight]"
                                                        class="form-control style--two" placeholder="0.00">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text px-3 bold">{{ translate('Kg') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group addon">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text px-3 bold">{{ currencySymbol() }}
                                                        </div>
                                                    </div>
                                                    <input type="text" name="carrier_condition[0][cost]"
                                                        class="form-control" placeholder="0.00">

                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" class="area-disabled delete-weight-range text-danger">
                                                    <i class="icofont-ui-delete"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-row mb-20">
                                <a href="#"
                                    class="add-new-weight-range btn-link">{{ translate('Add new range') }}</a>
                            </div>
                            <!--End carrier shipping conditon-->
                        </div>

                        <div class="own-shipping-rate">
                            <!--Rate Name-->
                            <div class="form-row mb-20">
                                <div class="col-sm-12">
                                    <label class="font-14 bold black">{{ translate('Rate Name') }} </label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="hidden" id="new-rate-zone-id" name="zone_id">
                                    <input type="hidden" id="is-active-condition" name="is_active_condition"
                                        value="0">
                                    <input type="text" name="rate_name" class="theme-input-style"
                                        value="{{ old('rate_name') }}" placeholder="{{ translate('Type Name') }}">
                                </div>
                            </div>
                            <!--End Rate Name-->
                            <!--Shipping Cost-->
                            <div class="form-row mb-20">
                                <div class="col-sm-12">
                                    <label class="font-14 bold black">{{ translate('Shipping Cost') }}</label>
                                    <input type="text" name="shipping_cost" class="theme-input-style"
                                        value="{{ old('shipping_cost') }}" placeholder="0.00">
                                </div>
                            </div>
                            <!--End Shipping Cost-->
                            <!--Own rate condition add button-->
                            <div class="form-row mb-20">
                                <a href="#"
                                    class="btn-link new-condition-btn">{{ translate('Add New Condition') }}</a>
                            </div>
                            <!--End Custom Shipping condition add button-->
                            <!--Own rate conditions-->
                            <div class="conditions d-none mb-20">
                                <div class="form-row mb-20">
                                    <div class="d-flex w-100">
                                        <div class="align-items-center d-flex mr-30">
                                            <div class="custom-radio mr-1">
                                                <input type="radio" id="weight_based" value="weight_based"
                                                    name="condionType" class="rate-condition-type-switcher" checked>
                                                <label for="weight_based"></label>
                                            </div>
                                            <label for="weight_based"
                                                class="black font-14 mb-0">{{ translate('Based on item weight') }}</label>
                                        </div>
                                        <div class="align-items-center d-flex">
                                            <div class="custom-radio mr-1">
                                                <input type="radio" id="price_based" value="price_based"
                                                    name="condionType" class="rate-condition-type-switcher">
                                                <label for="price_based"></label>
                                            </div>
                                            <label for="price_based"
                                                class="black font-14 mb-0">{{ translate('Based on order price') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row weight-based">
                                    <div class="col-sm-6">
                                        <label class="font-14 bold black">{{ translate('Minimum Weight') }}</label>
                                        <div class="input-group addon">
                                            <input type="text" name="min_weight" class="form-control style--two"
                                                value="{{ old('min_weight') }}" placeholder="0.00">
                                            <div class="input-group-append">
                                                <div class="input-group-text px-3 bold">gm</div>
                                            </div>
                                            @if ($errors->has('min_weight'))
                                                <div class="invalid-input">{{ $errors->first('min_weight') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="font-14 bold black">{{ translate('Maximum Weight') }}</label>
                                        <div class="input-group addon">
                                            <input type="text" name="max_weight" class="form-control style--two"
                                                value="{{ old('max_weight') }}" placeholder="0.00">
                                            <div class="input-group-append">
                                                <div class="input-group-text px-3 bold">gm</div>
                                            </div>
                                            @if ($errors->has('max_weight'))
                                                <div class="invalid-input">{{ $errors->first('max_weight') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row price-based d-none">
                                    <div class="col-sm-6">
                                        <label class="font-14 bold black">{{ translate('Minimum Price') }}</label>
                                        <div class="input-group addon">
                                            <input type="text" name="min_price" class="form-control style--two"
                                                value="{{ old('min_price') }}" placeholder="0.00">
                                            <div class="input-group-append">
                                                <div class="input-group-text px-3 bold">{{ currencySymbol() }}</div>
                                            </div>
                                            @if ($errors->has('min_price'))
                                                <div class="invalid-input">{{ $errors->first('min_price') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="font-14 bold black">{{ translate('Maximum Price') }}</label>
                                        <div class="input-group addon">
                                            <input type="text" name="max_price" class="form-control style--two"
                                                value="{{ old('max_price') }}" placeholder="0.00">
                                            <div class="input-group-append">
                                                <div class="input-group-text px-3 bold">{{ currencySymbol() }}</div>
                                            </div>
                                            @if ($errors->has('max_price'))
                                                <div class="invalid-input">{{ $errors->first('max_price') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End Own rate condition-->
                            <!--Own rate condition remove button-->
                            <div class="form-row mb-20">
                                <a href="#"
                                    class="btn-link remove-condition-btn d-none text-danger">{{ translate('Remove Condition') }}</a>
                            </div>
                            <!--End Custom Shipping condition remove button-->
                        </div>

                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn long store-rate-btn">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Rate Modal-->
    <!--Edit Rate Modal-->
    <div id="edit-rate-modal" class="edit-rate-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Update Rate') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="edit-rate-data">

                </div>
            </div>
        </div>
    </div>
    <!--End Edit Rate Modal-->
    <!--Manage Profile-->
    <div id="manage-profile" class="manage-profile modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Update Profile') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="shipping-profile-from">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Name') }} </label>
                            <input type="hidden" name="profile_id" value="{{ $profile_info->id }}">
                            <input type="text" name="profile_name" class="theme-input-style"
                                value="{{ $profile_info->name }}">
                            @if ($errors->has('profile_name'))
                                <div class="invalid-input">{{ $errors->first('profile_name') }}</div>
                            @endif
                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Location') }} </label>
                            <input type="text" name="location" class="theme-input-style"
                                value="{{ $profile_info->location }}" placeholder="{{ translate('Location') }}">
                            @if ($errors->has('location'))
                                <div class="invalid-input">{{ $errors->first('location') }}</div>
                            @endif
                        </div>
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Address') }} </label>
                            <textarea name="address" placeholder="{{ translate('Address') }}" class="theme-input-style">{{ $profile_info->address }}</textarea>
                            @if ($errors->has('address'))
                                <div class="invalid-input">{{ $errors->first('address') }}</div>
                            @endif
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit"
                                    class="btn long update-shipping-profile">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Manage Profile-->
    <!--Manage Products Modal-->
    <div id="manage-products-modal" class="manage-products-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Add Products') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="products-from">
                        <div class="form-row mb-20">
                            <label class="font-14 bold black">{{ translate('Products') }} </label>
                            <input type="hidden" name="profile_id" value="{{ $profile_info->id }}">
                            @php
                                $products = Plugin\TlcommerceCore\Models\Product::whereNotIn('id', Plugin\TlcommerceCore\Models\ShippingProfileProducts::pluck('product_id'))
                                    ->select('id', 'name')
                                    ->where('status', config('settings.general_status.active'))
                                    ->get();
                            @endphp
                            <select class="product-select w-100" name="products[]" multiple>
                                @foreach ($products as $product)
                                    <option
                                        {{ $profile_info->products->contains('product_id', $product->id) ? 'selected' : '' }}
                                        value="{{ $product->id }}">
                                        {{ $product->translation('name', getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="col-12 text-right">
                                <button type="submit"
                                    class="btn long update-product-list">{{ translate('Add Products') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Manage Products Modal-->
    <!--Delete product Modal-->
    <div id="remove-product-modal" class="remove-product-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Remove Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to remove this product') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.shipping.profile.product.remove') }}">
                        @csrf
                        <input type="hidden" id="remove-product-id" name="id">
                        <input type="hidden" name="profile_id" value="{{ $profile_info->id }}">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}
                        </button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete  Zone Modal-->
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    @include('core::base.includes.data_table.script')
    <script type="text/javascript">
        //Global variable
        let location_page_number = 1;
        let searched_location_page_number = 1;
        let searched_location_all_page_count = 0;

        (function($) {
            "use strict";

            /**
             * 
             * Product data table
             */
            $("#productTable").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
            });
            /**
             * Select product
             * 
             **/
            $('.product-select').select2({
                theme: "classic",
                placeholder: '{{ translate('Select Product') }}',
                closeOnSelect: false,
            });
            /**
             * Select shipping time
             * */
            $('.time-select').select2({
                theme: "classic",
                templateResult: formatTime,
                templateSelection: formatTime,
                placeholder: '{{ translate('Select Shipping Time') }}',
            });

            /**
             * Generate select shipping time options
             **/
            function formatTime(opt) {
                if (!opt.id) {
                    return opt.text;
                }
                var min = $(opt.element).attr('data-min');
                var min_unit = $(opt.element).attr('data-min-unit');
                var max = $(opt.element).attr('data-max');
                var max_unit = $(opt.element).attr('data-max-unit');
                var opt = min + ' ' + min_unit + ' - ' + max + ' ' + max_unit;
                return opt;
            };

            //Open create zone modal
            $('.open-zone-create-modal').on('click', function(e) {
                e.preventDefault();
                location_page_number = 1;
                searched_location_page_number = 1;
                searched_location_all_page_count = 0;
                $('.location-options').html('');
                getCountriesOptions();
            });

            // Search field keyup event ajax call
            $('#location_search').on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    let value = $(this).val();
                    searched_location_page_number = 1;
                    if (value && value.length > 0) {
                        getSearchedLocations(value);
                    } else {
                        getCountriesOptions();
                    }
                }
            });

            // search button click ajax call
            $('.search-btn').on('click', function() {
                let value = $('#location_search').val();
                searched_location_page_number = 1;
                if (value && value.length > 0) {
                    getSearchedLocations(value);
                }
            })
            /**
             * Load location box
             * 
             **/
            $(document).on('click', '.loader button', function() {
                let searchKey = $('#location_search').val();
                if (searchKey && searchKey.length > 0) {
                    if (searched_location_all_page_count == 0 || searched_location_page_number <=
                        searched_location_all_page_count) {
                        getSearchedLocations(searchKey);
                    }
                } else {
                    getCountriesOptions();
                }
            });
            /**
             * Get Location options
             * 
             **/
            function getCountriesOptions() {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        page: location_page_number,
                        perPage: 1,
                        profile_id: '{{ $profile_info->id }}'
                    },
                    url: '{{ route('plugin.tlcommercecore.shipping.location.ul.list') }}',
                    success: function(response) {
                        if (response.success) {
                            $('.location-options').append(response.list);
                            location_page_number = location_page_number + 1;
                            $('.zone-create-modal').modal('show');
                        } else {
                            toastr.error('Request Failed', "Error!");
                        }
                    },
                    error: function() {
                        toastr.error('Request Failed', "Error!");
                    }
                });
            }
            /**
             * Get Searched Location options
             * 
             **/
            function getSearchedLocations(searchKey) {
                if (searched_location_page_number == 1) {
                    $('.location-options').html('');
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        page: searched_location_page_number,
                        perPage: 1,
                        key: searchKey,
                        profile_id: '{{ $profile_info->id }}'
                    },
                    url: '{{ route('plugin.tlcommercecore.shipping.search.location.ul.list') }}',
                    success: function(response) {
                        if (response.success) {
                            if (response.found) {
                                $('.location-options').append(response.list);
                                searched_location_page_number = searched_location_page_number + 1;
                                searched_location_all_page_count = response.totalPage;

                                if (searched_location_page_number > response.totalPage) {
                                    $('.loader > button').prop('disabled', true);
                                } else {
                                    $('.loader > button').prop('disabled', false);
                                }
                            } else {
                                let notFoundKey = "{{ translate('Not Found') }}";
                                $('.location-options').html(`
                                <div class="text-center mt-5"> ${notFoundKey} </div>
                            `);
                            }
                        }
                    }
                });
            }
            /** 
             * Store new shipping zone
             * */
            $('.create-new-zone').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-zone-create-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.shipping.profile.zones.store') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            });

            /**
             *Update shipping location
             * 
             **/
            $('.update-shipping-profile').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#shipping-profile-from').serialize(),
                    url: '{{ route('plugin.tlcommercecore.shipping.profile.update') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            });
            /**
             * Remove product
             * 
             **/
            $('.remove-product').on('click', function(e) {
                e.preventDefault();
                let product = $(this).data('product');
                $("#remove-product-id").val(product);
                $("#remove-product-modal").modal('show');
            });
            /**
             * Update product list
             * 
             **/
            $('.update-product-list').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#products-from').serialize(),
                    url: '{{ route('plugin.tlcommercecore.shipping.profile.update.product.list') }}',
                    success: function(response) {
                        location.reload();
                    },
                    errors: function(response) {
                        location.reload();
                    }
                });
            });
            /**
             * Edit shipping zone
             * 
             **/
            $('.edit-shipping-zone').click('on', function(e) {
                e.preventDefault();
                location_page_number = 1;
                let id = $(this).data('zone');
                let profile_id = $(this).data('profile');
                let data = {
                    id: id,
                    profile: profile_id
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: data,
                    url: '{{ route('plugin.tlcommercecore.shipping.profile.zones.edit') }}',
                    success: function(data) {
                        $('#edit-zone-modal').modal('show')
                        $('#edit-zone-data').html(data)
                    }
                });
            });
            /**
             * 
             * Delete zone
             * 
             * */
            $('.delete-zone').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('zone');
                $("#delete-zone-id").val(id);
                $('#delete-modal').modal('show');
            });
            /**
             * Edit rate
             * 
             * */
            $('.edit-rate').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('rate');
                let data = {
                    id: id
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: data,
                    url: '{{ route('plugin.tlcommercecore.shipping.rate.edit') }}',
                    success: function(data) {
                        $('#edit-rate-modal').modal('show');
                        $('#edit-rate-data').html(data)
                    }
                });
            });

            /**
             *Delete rate 
             *
             **/
            $('.delete-rate').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let rate_id = $this.data('rate');
                $("#delete-rate-id").val(rate_id);
                $('#delete-rate-modal').modal('show');
            });
            /**
             * 
             * Add new Rate
             * 
             * */
            $('.new-rate').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('zone');
                $("#new-rate-zone-id").val(id);
                $('#new-rate-modal').modal('show');
            });
            /**
             * 
             * New Condition
             * */
            $('.new-condition-btn').on('click', function(e) {
                $("#is-active-condition").val(1);
                $('.conditions').removeClass('d-none');
                $('.new-condition-btn').addClass('d-none');
                $('.remove-condition-btn').removeClass('d-none');
            });
            /**
             * 
             * remove Condition 
             * */
            $('.remove-condition-btn').on('click', function(e) {
                $("#is-active-condition").val(0);
                $('.conditions').addClass('d-none');
                $('.new-condition-btn').removeClass('d-none');
                $('.remove-condition-btn').addClass('d-none');
            });
            /**
             * Store zone shipping rate
             * 
             **/
            $('.store-rate-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#shipping-rate-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.shipping.store.rate') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            });
            /** 
             *Add new carrier shipping weight range
             *  
             **/
            $('.add-new-weight-range').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "GET",
                    url: '{{ route('plugin.tlcommercecore.shipping.carrier.weight.range.input') }}',
                    success: function(data) {
                        var carrierNewWeightRange = document.createElement('tr');
                        carrierNewWeightRange.innerHTML = data;
                        document.getElementById("carrier-shipping-weight-range").appendChild(
                            carrierNewWeightRange);
                    }
                });
            });
            /**
             * Choose shipping rate type
             *  
             **/
            $('.rate-type-switcher').on('change', function(e) {
                let selected_rate_type = $('input[name="rate_type"]:checked').val();
                if (selected_rate_type === 'carrier_rate') {
                    $('.carrier-shipping-rate').removeClass('d-none')
                    $('.own-shipping-rate').addClass('d-none')
                } else {
                    $('.carrier-shipping-rate').addClass('d-none')
                    $('.own-shipping-rate').removeClass('d-none')
                }
            })
            /**
             * 
             * Condition Type
             */
            $('.rate-condition-type-switcher').on('change', function(e) {
                let condition_type = $('input[name="condionType"]:checked').val();
                if (condition_type === 'weight_based') {
                    $('.weight-based').removeClass('d-none')
                    $('.price-based').addClass('d-none')
                } else {
                    $('.weight-based').addClass('d-none')
                    $('.price-based').removeClass('d-none')
                }
            });


            $(document).ready(function() {
                // Shipping Location List Expand
                $(document).on('click', '.cl-item:not(.cl-item-no-sub) > .cl-label-wrap .cl-label-tools',
                    function() {
                        $(this).parent().parent().parent().toggleClass('cl-item-open');
                    });
            });

        })(jQuery);
        /**
         * Remove weight range
         * 
         * 
         **/
        function removeWeightRange(element) {
            "use strict";
            element.closest('tr').remove();
        }
    </script>
@endsection
