@php
    use Plugin\TlcommerceCore\Repositories\SettingsRepository;
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Shipping & Delivery') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/data-table/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
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

        .shipping-profile-info {
            margin-right: 100px;
        }

        .shipping-profile {
            border: 1px solid #dee2e6;
        }

        .new-shipping-area {
            border: 1px dashed;
        }

        @media(max-width:575px) {
            .shipping-profile .info {
                flex-direction: column;
            }

            .shipping-profile-info {
                margin-right: 0px;
                margin-bottom: 20px;
            }

            .profile-zone,
            .border-left2 {
                padding: 0 !important;

                border-left-color: transparent !important;
            }
        }
    </style>
@endsection
@section('main_content')
    <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
        <h4><i class="icofont-vehicle-delivery-van"></i> {{ translate('Shipping & Delivery') }}</h4>
    </div>
    <!--Shipping Options-->
    <div class="row">
        <div class="col-lg-6 col-12" id="ShippingOptions">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Shipping Options') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('plugin.tlcommercecore.shipping.option.update') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="custom-radio mr-3">
                                    <input type="radio" id="flatRate" name="shipping_option"
                                        value="{{ config('tlecommercecore.shipping_cost_options.flat_rate') }}"
                                        @checked(SettingsRepository::getEcommerceSetting('shipping_option') == null ||
                                                SettingsRepository::getEcommerceSetting('shipping_option') ==
                                                    config('tlecommercecore.shipping_cost_options.flat_rate'))>
                                    <label for="flatRate"></label>
                                </div>
                                <label for="flatRate">Flat Rate Shipping Cost</label>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="custom-radio mr-3">
                                    <input type="radio" id="productWise" name="shipping_option"
                                        value="{{ config('tlecommercecore.shipping_cost_options.product_wise_rate') }}"
                                        @checked(SettingsRepository::getEcommerceSetting('shipping_option') ==
                                                config('tlecommercecore.shipping_cost_options.product_wise_rate'))>
                                    <label for="productWise"></label>
                                </div>
                                <label for="productWise">Product Wise Shipping Cost</label>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="custom-radio mr-3">
                                    <input type="radio" id="profileBased" name="shipping_option"
                                        value="{{ config('tlecommercecore.shipping_cost_options.profile_wise_rate') }}"
                                        @checked(SettingsRepository::getEcommerceSetting('shipping_option') ==
                                                config('tlecommercecore.shipping_cost_options.profile_wise_rate'))>
                                    <label for="profileBased"></label>
                                </div>
                                <label for="profileBased">Based on Shipping Profiles</label>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="custom-radio mr-3">
                                    <input type="radio" id="locationBased" name="shipping_option"
                                        value="{{ config('tlecommercecore.shipping_cost_options.location_wise_rate') }}"
                                        @checked(SettingsRepository::getEcommerceSetting('shipping_option') ==
                                                config('tlecommercecore.shipping_cost_options.location_wise_rate'))>
                                    <label for="locationBased"></label>
                                </div>
                                <label for="locationBased">Location Based Shipping Cost</label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <button type="submit" class="btn long">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12" id="ConfigurationNote">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Note') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="note-container">
                        <div class="border-bottom pb-2">
                            <div class="content">
                                <div class="pr-0 pr-sm-4 fz-14">
                                    <strong>Flat Rate Shipping Cost Calculation: </strong>How many products a customer
                                    purchase,
                                    doesn't
                                    matter. Shipping cost is fixed.
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom pb-2 mt-2">
                            <div class="content">
                                <div class="pr-0 pr-sm-4 fz-14">
                                    <strong>Product Wise Shipping Cost Calculation: </strong>Shipping cost is calculate by
                                    addition of each product shipping cost.
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom pb-2">
                            <div class="content">
                                <div class="pr-0 pr-sm-4 fz-14">
                                    <strong>Profile Wise Shipping Cost Calculation: </strong>Shipping cost is calculate by
                                    selection of each product shipping profile.
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 pb-2">
                            <div class="content">
                                <div class="pr-0 pr-sm-4 fz-14">
                                    <strong>Location Based Shipping Cost Calculation: </strong>How many products a customer
                                    purchase,
                                    doesn't
                                    matter. Shipping cost is based on shipping location.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Shipping Options-->

    <!--Location Based Shipping Rates-->
    <div
        class="row {{ SettingsRepository::getEcommerceSetting('shipping_option') == null ||
        SettingsRepository::getEcommerceSetting('shipping_option') ==
            config('tlecommercecore.shipping_cost_options.location_wise_rate')
            ? ''
            : 'd-none' }}">
        <div class="col-lg-12 col-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Location Based Shipping Rates') }}</h4>
                        <button class="btn long open-shipping-rate-modal" data-toggle="modal"
                            data-target="#new-location-shipping-rate">
                            <i class="icofont-plus-circle"></i> {{ translate('Add New Rate') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="shipping-rate-table" class="hoverable text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ translate('Title') }}</th>
                                    <th>{{ translate('Cost') }}</th>
                                    <th>{{ translate('Countries') }}</th>
                                    <th>{{ translate('States') }}</th>
                                    <th>{{ translate('Cities') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($location_based_shipping_rates as $rate)
                                    <tr>
                                        <td>{{ $rate->title }}</td>
                                        <td>{!! currencyExchange($rate->cost) !!}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-10">
                                                @forelse ($rate->countries as $country)
                                                    <span class="badge badge-success">{{ $country->name }}</span>
                                                @empty
                                                    <p class="badge badge-danger">No Country</p>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-10">
                                                @forelse ($rate->states as $state)
                                                    <span class="badge badge-primary">{{ $state->name }}</span>
                                                @empty
                                                    <p class="badge badge-danger">No State</p>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-10">
                                                @forelse ($rate->cities as $city)
                                                    <span class="badge badge-info"> {{ $city->name }}</span>
                                                @empty
                                                    <p class="badge badge-danger">No City</p>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown-button">
                                                <a href="#" class="d-flex align-items-center justify-content-center"
                                                    data-toggle="dropdown">
                                                    <div class="menu-icon mr-0">
                                                        <span></span>
                                                        <span></span>
                                                        <span></span>
                                                    </div>
                                                </a>
                                                <div class="dropdown-menu">
                                                    <a href="#" class="edit-rate" data-id="{{ $rate->id }}">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                    <a href="#" class="delete-rate" data-id="{{ $rate->id }}">
                                                        {{ translate('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <p class="alert">No Items Found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Location Based Shipping Rates-->

    <!--Flat Rate Shipping Options-->
    <div
        class="row {{ SettingsRepository::getEcommerceSetting('shipping_option') == null ||
        SettingsRepository::getEcommerceSetting('shipping_option') == config('tlecommercecore.shipping_cost_options.flat_rate')
            ? ''
            : 'd-none' }}">
        <div class="col-lg-6 col-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Flat Rate Shipping Cost') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('plugin.tlcommercecore.shipping.flat.rate.update') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <input type="number" name="flat_rate_shipping_cost" class="theme-input-style"
                                value="{{ SettingsRepository::getEcommerceSetting('flat_rate_shipping_cost') }}"
                                placeholder="{{ translate('Enter Shipping Cost') }}" required>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <button type="submit" class="btn long">{{ translate('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12" id="ConfigurationNote">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2 py-3">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4>{{ translate('Note') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="note-container">
                        <div class="border-bottom pb-2">
                            <div class="content">
                                <div class="pr-0 pr-sm-4 fz-14">
                                    Flat rate shipping cost is applicable if Flat Rate Shipping Cost is enabled.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Flat Rate Shipping Options-->

    <!--Profile Based Shipping Options-->
    <div
        class="row {{ SettingsRepository::getEcommerceSetting('shipping_option') != null &&
        SettingsRepository::getEcommerceSetting('shipping_option') ==
            config('tlecommercecore.shipping_cost_options.profile_wise_rate')
            ? ''
            : 'd-none' }}">
        <!--Shipping Profiles-->
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ translate('Shipping Profiles') }}</h4>
                        <div class="d-flex align-items-center gap-15">
                            <a href="{{ route('plugin.tlcommercecore.shipping.profile.form') }}"
                                class="btn long">{{ translate('Create new profile') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (count($shipping_profiles))
                        <!--Profile List-->
                        @foreach ($shipping_profiles as $profile)
                            <div class="d-flex justify-content-between p-3 rounded shipping-profile mb-20">
                                <div class="info d-flex">
                                    <div class="shipping-profile-info">
                                        <div class="profile-name">
                                            <h4>{{ $profile->name }}</h4>
                                        </div>
                                        <div class="profile-product-info">
                                            <p class="black font-14 font-weight-lighter">{{ count($profile->products) }}
                                                Product</p>
                                            <a href="#" class="btn-danger btn-sm delete-profile"
                                                data-profile="{{ $profile->id }}">{{ translate('Delete Profile') }}</a>
                                        </div>
                                    </div>
                                    <div class="border-left2 pl-3 profile-zone">
                                        @if (count($profile->zones) > 0)
                                            <div class="zone-info">
                                                <h5>{{ translate('Shipping Zone') }}</h5>
                                                @foreach ($profile->zones as $zone)
                                                    <p class="black font-14 font-weight-lighter m-0"><i
                                                            class="icofont-location-pin"></i>
                                                        {{ $zone->name }}</p>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="black">
                                                {{ translate('No shipping rates available for products in this profile') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="action-area">
                                    <a href="{{ route('plugin.tlcommercecore.shipping.profile.manage', $profile->id) }}"
                                        class="btn long">{{ translate('Manage') }}</a>

                                </div>
                            </div>
                        @endforeach
                        <!--End Profile List-->
                    @else
                        <p class="alert alert-danger text-center">{{ translate('No Profile Created yet') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <!--End Shipping Profiles-->
        <!--Shipping Processing Time-->
        <div class="col-12" id="ShippingTimes">
            <div class="card mb-30">
                <div class="card-header bg-white border-bottom2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ translate('Shipping Time') }}</h4>
                        <div class="d-flex align-items-center gap-15">
                            <a href="#" class="btn long mr-2" data-toggle="modal"
                                data-target="#new-shipping-time-modal">{{ translate('Create new Time') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="dh-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Min Shipping Time') }}</th>
                                    <th>{{ translate('Max Shipping Time') }}</th>
                                    <th class="text-right">{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($shippingTimes) > 0)
                                    @foreach ($shippingTimes as $key => $time)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $time->min_value }} {{ $time->min_unit }}</td>
                                            <td>{{ $time->max_value }} {{ $time->max_unit }}</td>
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
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a href="#" data-item="{{ $time->id }}"
                                                            class="delete-shipping-time">{{ translate('Delete') }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">
                                            <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--End Shipping Processing Time-->
    </div>
    <!--End Profile Based Shipping Options-->

    <!--New Shipping time Modal-->
    <div id="new-shipping-time-modal" class="new-shipping-time-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Add New Shipping Time') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-shipping-time-form">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="form-group col-sm-12">
                                <div>
                                    <label class="font-14 bold black">{{ translate('Minimum Shipping Time') }} </label>
                                </div>
                                <div class="input-group addon">
                                    <input type="text" name="minimmum_shipping_time" value="" placeholder="0"
                                        class="form-control style--two">
                                    <div class="input-group-append">
                                        <select class="form-control" name="minimmum_shipping_time_unit">
                                            <option value="Days">{{ translate('Days') }}</option>
                                            <option value="Hours">{{ translate('Hours') }}</option>
                                            <option value="Minutes">{{ translate('Minutes') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <div>
                                    <label class="font-14 bold black">{{ translate('Maximum Shipping Time') }} </label>
                                </div>
                                <div class="input-group addon">
                                    <input type="text" name="maximum_shipping_time" value="" placeholder="0"
                                        class="form-control style--two">
                                    <div class="input-group-append">
                                        <select class="form-control" name="maximum_shipping_time_unit">
                                            <option value="Days">{{ translate('Days') }}</option>
                                            <option value="Hours">{{ translate('Hours') }}</option>
                                            <option value="Minutes">{{ translate('Minutes') }}</option>
                                        </select>
                                    </div>
                                </div>
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
    <!--End New Shipping time Modal-->

    <!--Delete Shipping Profile Modal-->
    <div id="delete-profile-modal" class="delete-profile-modal modal fade show" aria-modal="true" role="dialog">
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.shipping.profile.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-profile-id" name="id">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Shipping Profile Modal-->

    <!--Delete Shipping Time Modal-->
    <div id="delete-shipping-time-modal" class="delete-shipping-time-modal modal fade show" aria-modal="true"
        role="dialog">
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
                    <form method="POST" action="{{ route('plugin.tlcommercecore.shipping.time.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-time-id" name="id">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Shipping Time Modal-->

    <!--New Location Based Shipping Cost Modal-->
    <div id="new-location-shipping-rate" class="new-location-shipping-rate modal fade show" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Add New Location Based Shipping Rate') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-shipping-rate-form">
                        @csrf
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Title') }} </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" name="title" class="theme-input-style"
                                    placeholder="{{ translate('Enter Title') }}">
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Shipping Cost') }} </label>
                            </div>
                            <div class="col-sm-12">
                                <input type="number" name="cost" class="theme-input-style" placeholder="0.00">
                            </div>
                        </div>

                        <div class="form-row mb-20">
                            <div class="col-sm-12">
                                <label class="font-14 bold black">{{ translate('Locations') }} </label>
                            </div>
                            <div class="form-group w-100">
                                <div class="input-group addon ov-hidden">
                                    <input type="text" name="location_search" id="location_search"
                                        class="form-control style--two" value=""
                                        placeholder="{{ translate('Search Location') }}">
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
                                <button class="btn long create-new-zone" type="submit">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Location Based Shipping Cost Modal-->

    <!--Delete Location Based Rate Modal-->
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
                    <form method="POST"
                        action="{{ route('plugin.tlcommercecore.shipping.location.based.rate.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-rate-id" name="id">
                        <button type="button" class="btn long btn-danger mt-2"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Location Based Rate Modal-->
    <!--Edit Rate Modal-->
    <div id="edit-rate-modal" class="edit-rate-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Rate Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="edit-rate-data">

                </div>
            </div>
        </div>
    </div>
    <!--End Edit Shipping Zone Modal-->
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/data-table/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            let location_page_number = 1;
            let searched_location_page_number = 1;
            let searched_location_all_page_count = 0;

            $(document).ready(function() {
                //Datatable initialization
                $('#shipping-rate-table').DataTable();

                $(document).on('click', '.cl-item:not(.cl-item-no-sub) > .cl-label-wrap .cl-label-tools',
                    function(e) {
                        e.preventDefault();
                        $(this).parent().parent().parent().toggleClass('cl-item-open');
                    });


            });

            $('.open-shipping-rate-modal').on('click', function(e) {
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
             * Store new shipping time
             * 
             **/
            $('.store-shiping-time-btn').on('click', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-shipping-time-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.shipping.time.store') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').next(
                                '.input-group-append').after(
                                '<div class="invalid-input">' + error + '</div>')
                        })
                    }
                });
            })
            /**
             *Delete shipping profile
             *  
             **/
            $('.delete-profile').on('click', function(e) {
                e.preventDefault();
                let $this = $(this);
                let id = $this.data('profile');
                $('#delete-profile-id').val(id);
                $('.delete-profile-modal').modal('show');
            })

            $(".delete-rate").on("click", function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $("#delete-rate-id").val(id);
                $("#delete-rate-modal").modal("show");
            });
            /** 
             * Delete shipping time
             *  
             **/
            $('.delete-shipping-time').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('item');
                $("#delete-time-id").val(id);
                $("#delete-shipping-time-modal").modal('show');
            });

            /** 
             * Store new shipping zone
             * */
            $('#new-shipping-rate-form').on('submit', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-shipping-rate-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.shipping.location.based.rate.store') }}',
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

            $('.edit-rate').on('click', function(e) {
                e.preventDefault();
                location_page_number = 1;
                let id = $(this).data('id');
                let profile_id = null;
                let data = {
                    id: id,
                    type: 'location'
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: data,
                    url: '{{ route('plugin.tlcommercecore.shipping.profile.zones.edit') }}',
                    success: function(data) {
                        if (data.success) {
                            $('#edit-rate-modal').modal('show');
                            $('#edit-rate-data').html(data.view);
                        } else {
                            toastr.error('Something went wrong', "Error!");
                        }
                    },
                    error: function(data) {
                        toastr.error('Something went wrong', "Error!");
                    }
                });
            });

            /** 
             * Update Shipping rate
             * */
            $(document).on('submit', '#edit-shipping-rate-form', function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#edit-shipping-rate-form').serialize(),
                    url: '{{ route('plugin.tlcommercecore.shipping.location.based.rate.update') }}',
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
                        type: 'location'
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
                        type: 'location'
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

        })(jQuery);
    </script>
@endsection
