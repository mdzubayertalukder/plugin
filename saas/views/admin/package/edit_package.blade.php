@php
    $plans = getAllPlans();
    $plugins = availablePluginsForTenant();
    $payment_methods = getTenantPaymentGateways();
@endphp
@extends('core::base.layouts.master')

@section('title')
    {{ translate('Edit Package') }}
@endsection
@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--  End select2  -->
@endsection
@section('main_content')
    <!-- Main Content -->
    <form class="form-horizontal my-4 mb-4" id="update-package-form" action="{{ route('plugin.saas.update.package') }}"
        method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $package->id }}">
        <input type="hidden" name="lang" value="{{ $lang }}">
        <div class="row">
            <div class="col-md-12">
                {{-- Languages --}}
                <div class="row">
                    <div class="col-12 mb-3">
                        <p class="alert alert-info">You are editing <strong>"{{ getLanguageNameByCode($lang) }}"</strong>
                            version</p>
                    </div>
                    <div class="col-12">
                        <ul class="nav nav-tabs nav-fill border-light border-0">
                            @foreach ($languages as $key => $language)
                                <li class="nav-item">
                                    <a class="nav-link @if ($language->code == $lang) active border-0 @else bg-light @endif py-3"
                                        href="{{ route('plugin.saas.edit.package', ['id' => $package->id, 'lang' => $language->code]) }}">
                                        <img src="{{ asset('/public/flags/') . '/' . $language->code . '.png' }}"
                                            width="20px">
                                        <span>{{ $language->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Package Details --}}
                <div class="card mb-30">
                    <div class="d-sm-flex justify-content-between align-items-center card-header bg-white py-3">
                        <h4>{{ translate('Package Details') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="name" class="font-14 bold black mb-2">{{ translate('Name') }}<span
                                        class="text-danger"> * </span>
                                </label>
                                <input type="text" name="name" id="name" class="theme-input-style"
                                    value="{{ $package->translation('name', $lang) }}"
                                    placeholder="{{ translate('Name') }}" required>
                                @if ($errors->has('name'))
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                @endif
                            </div>

                            <div
                                class="form-group col-md-4 {{ !empty($lang) && $lang != getdefaultlang() ? 'area-disabled' : '' }}">
                                <label class="font-14 bold black mb-2">{{ translate('Type') }} </label>
                                <select class="theme-input-style" id="type" name="type">
                                    <option value="paid" {{ $package->type == 'paid' ? 'selected' : '' }}>
                                        {{ translate('Paid') }}
                                    </option>
                                    <option value="free" {{ $package->type == 'free' ? 'selected' : '' }}>
                                        {{ translate('Free') }}
                                    </option>
                                </select>
                            </div>

                            <div
                                class="form-group col-md-4 {{ !empty($lang) && $lang != getdefaultlang() ? 'area-disabled' : '' }}">
                                <label class="font-14 bold black mb-2">{{ translate('Featured') }}
                                    <span class="text-danger">
                                        *
                                    </span>
                                </label>
                                <select class="theme-input-style" name="is_featured">
                                    <option value="0" {{ $package->is_featured == '0' ? 'selected' : '' }}>
                                        {{ translate('NO') }}
                                    </option>
                                    <option value="1" {{ $package->is_featured == '1' ? 'selected' : '' }}>
                                        {{ translate('Yes') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="row {{ !empty($lang) && $lang != getdefaultlang() ? 'area-disabled' : '' }}">
                            <div class="form-group col-md-4">
                                <label class="font-14 bold black mb-2">{{ translate('Status') }} </label>
                                <select class="theme-input-style" name="status">
                                    <option value="1" {{ $package->status == '1' ? 'selected' : '' }}>
                                        {{ translate('Enabled') }}
                                    </option>
                                    <option value="0" {{ $package->status == '0' ? 'selected' : '' }}>
                                        {{ translate('Disabled') }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-4" id="trail_period_div">
                                <label class="font-14 bold black mb-2">{{ translate('Trial Period') }} <span
                                        class="text-danger"> *
                                    </span></label>
                                <input type="text" name="trail_period" id="trail_period" class="theme-input-style"
                                    value="{{ $package->trail_period }}" placeholder="{{ translate('Trail Period') }}">
                                @if ($errors->has('trail_period'))
                                    <p class="text-danger">{{ $errors->first('trail_period') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Package Details --}}


                {{-- Pricing Plans --}}
                <div
                    class="card mb-30 pricing_plan {{ !empty($lang) && $lang != getdefaultlang() ? 'area-disabled' : '' }}">
                    <div class="d-sm-flex justify-content-between align-items-center card-header py-3 bg-white">
                        <h4>{{ translate('Package  Plans & Pricing') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12" id="select_plans">
                                <label class="font-14 bold black mb-2">{{ translate('Select Plan') }}<span
                                        class="text-danger"> * </span></label>
                                <select class="theme-input-style" name="plans[]" multiple="multiple" id="multi_plan_input"
                                    onchange="addPlanPrice()">
                                    @for ($i = 0; $i < sizeof($plans); $i++)
                                        <option value="{{ $plans[$i]->id }}" data-name="{{ $plans[$i]->name }}"
                                            {{ in_array($plans[$i]->id, $selected_planing_id) ? 'selected' : '' }}>
                                            {{ translate($plans[$i]->name) }}
                                        </option>
                                    @endfor
                                </select>
                                @if ($errors->has('plans'))
                                    <p class="text-danger">{{ translate('Valid applicable plan is required') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="row" id="pricing_plans">
                            @foreach ($selected_planing as $plan)
                                <div class="form-group col-md-4">
                                    <label for="name_{{ strtolower($plan->name) }}"
                                        class="font-14 bold black mb-2">{{ translate($plan->name) }}
                                        {{ translate('Cost') }}
                                    </label>
                                    <input type="number" name="cost[]" id="{{ strtolower($plan->name) }}_cost"
                                        class="form-control" value="{{ $plan->cost }}"
                                        placeholder="{{ translate($plan->name) }} {{ translate('Cost') }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- Pricing Plans --}}

                {{-- Features --}}
                <div class="row {{ !empty($lang) && $lang != getdefaultlang() ? 'area-disabled' : '' }}">
                    <div class="col-md-4">
                        <div class="card mb-30 plan_features">
                            <div class="d-sm-flex justify-content-between align-items-center card-header py-3 bg-white">
                                <h4>{{ translate('Choose Features') }}</h4>
                            </div>
                            <div class="card-body">
                                @foreach ($plugins as $plugin)
                                    @if ($plugin->type != 'saas' && $plugin->location != 'tlecommercecore')
                                        <div class="form-group d-flex justify-content-between">
                                            <label class="font-14 bold black mb-2">{{ $plugin->name }}</label>
                                            <label class="switch glow primary medium">
                                                <input type="checkbox" name="plugins[]" value="{{ $plugin->id }}"
                                                    @checked(in_array($plugin->id, array_column($package_features, 'plugin_id')))>
                                                <span class="control"></span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-30 plan_features">
                            <div class="d-sm-flex justify-content-between align-items-center card-header py-3 bg-white">
                                <h4>{{ translate('Access Privileges') }}</h4>
                            </div>
                            <div class="card-body">
                                @php
                                    $privileges = json_decode($package_privileges, true);
                                @endphp
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Create Page') }}</label>
                                    <input type="number" name="package_privileges_create_page" class="theme-input-style"
                                        value="{{ isset($privileges) ? $privileges['package_privileges_create_page'] : 0 }}"
                                        placeholder="{{ translate('Number of pages can create') }}" min="-1"
                                        required>
                                    <label class="font-12 bold">{{ translate('-1 For Unlimitted') }}</label>
                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Create Blog') }}</label>
                                    <input type="number" name="package_privileges_create_blog" class="theme-input-style"
                                        value="{{ isset($privileges) ? $privileges['package_privileges_create_blog'] : 0 }}"
                                        placeholder="{{ translate('Number of blogs can create') }}" min="-1"
                                        required>
                                    <label class="font-12 bold">{{ translate('-1 For Unlimitted') }}</label>
                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Store Product') }}</label>
                                    <input type="number" name="package_privileges_create_product"
                                        class="theme-input-style"
                                        value="{{ isset($privileges) ? $privileges['package_privileges_create_product'] : 0 }}"
                                        placeholder="{{ translate('Number of products can upload') }}" min="-1"
                                        required>
                                    <label class="font-12 bold">{{ translate('-1 For Unlimitted') }}</label>
                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Customers') }}</label>
                                    <input type="number" name="package_privileges_customers" class="theme-input-style"
                                        value="{{ isset($privileges) ? $privileges['package_privileges_customers'] : 0 }}"
                                        placeholder="{{ translate('Number of customers can have') }}" min="-1"
                                        required>
                                    <label class="font-12 bold">{{ translate('-1 For Unlimitted') }}</label>
                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Custom Domain') }}</label>
                                    <input type="number" name="package_privileges_custom_domain"
                                        class="theme-input-style"
                                        value="{{ isset($privileges) ? $privileges['package_privileges_custom_domain'] : 0 }}"
                                        placeholder="{{ translate('Number of custom domains') }}" min="-1"
                                        required>
                                    <label class="font-12 bold">{{ translate('-1 For Unlimitted') }}</label>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="font-14 bold black mb-2">{{ translate('Maximum Allocated Storage (MB)') }}</label>
                                    <input type="number" name="package_privileges_allocated_storage"
                                        class="theme-input-style"
                                        value="{{ isset($privileges) ? $privileges['package_privileges_allocated_storage'] : 0 }}"
                                        placeholder="{{ translate('Maximum Allocated Storage') }}" min="-1"
                                        required>
                                    <label class="font-12 bold">{{ translate('-1 For Unlimitted') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-30 plan_features">
                            <div class="d-sm-flex justify-content-between align-items-center card-header py-3 bg-white">
                                <h4>{{ translate('Payment Gateways') }}</h4>
                            </div>
                            <div class="card-body">
                                @foreach ($payment_methods as $method => $id)
                                    <div class="form-group d-flex justify-content-between">
                                        @if ($method == 'cod')
                                            <label
                                                class="font-14 bold black mb-2">{{ translate('Cash On Delivery') }}</label>
                                        @else
                                            <label class="font-14 bold black mb-2">{{ ucfirst($method) }}</label>
                                        @endif
                                        <label class="switch glow primary medium">
                                            <input type="checkbox" name="payment_methods[]" value="{{ $id }}"
                                                @checked(in_array($id, array_column($package_payment_methods, 'payment_method')))>
                                            <span class="control"></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Features --}}

                <div
                    class="bottom-button d-flex align-items-center justify-content-sm-end gap-10 flex-wrap justify-content-center">
                    <button type="submit" class="btn btn-outline-primary" tabindex="4" id="update-package-btn">
                        <img src="{{ asset('/public/backend/assets/img/loader-w4.svg') }}" alt="" class="d-none"
                            id="loader">
                        {{ translate('Update Package') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <!-- End Main Content -->
@endsection
@section('custom_scripts')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <!--End Select2-->
    <script>
        $(document).ready(function() {
            'use strict'
            $('#multi_plan_input').select2({
                theme: "classic",
                placeholder: "{{ translate('No Option Selected') }}",
            });

            $('#update-package-form').submit(function(event) {
                event.preventDefault()
                $('#update-package-btn').prop('disabled', true)
                $('#loader').removeClass('d-none')
                this.submit();
            });
        })

        showPlan()
        $('#type').change(function() {
            'use strict'
            showPlan()
        })

        /**
         * Showing pricing plan depending on package type
         */
        function showPlan() {
            'use strict'
            let package_type = $('#type').val()
            if (package_type == 'paid') {
                $('.pricing_plan').show()
                $('#trail_period_div').show()
            } else {
                $('.pricing_plan').hide()
                $('#trail_period_div').hide()
            }
        }

        /**
         * Add price giving options based on selected plans
         */
        function addPlanPrice(event) {
            'use strict'
            let selectedOptions = $('#multi_plan_input option:selected');
            let selectedOptionsValue = [];
            selectedOptions.each(function() {
                selectedOptionsValue.push($(this).val());
            });

            let selectedTexts = [];
            selectedOptions.each(function() {
                selectedTexts.push($(this).data('name'));
            });

            let selectedPlannings = JSON.parse('<?php echo json_encode($selected_planing); ?>');


            let html = ``
            for (let i = 0; i < selectedOptionsValue.length; i++) {
                let selected_index_in_planings = searchById(selectedPlannings, selectedOptionsValue[i])
                if (selected_index_in_planings != -1) {
                    let lowercaseText = selectedPlannings[selected_index_in_planings]["name"].toLowerCase();
                    html = html + `<div class="form-group col-md-4">
                                <label for="name_` + lowercaseText + `" class="font-14 bold black mb-2">` +
                        selectedPlannings[selected_index_in_planings]["name"] + ` {{ translate('Cost') }}<span
                                        class="text-danger"> * </span>
                                </label>
                                <input type="number" name="cost[]" id="` + lowercaseText + `_cost" class="form-control"
                                    value="` + selectedPlannings[selected_index_in_planings]["cost"] +
                        `" placeholder="` + selectedPlannings[selected_index_in_planings]["name"] +
                        ` {{ translate('Cost') }}">
                            </div>`
                } else {
                    let lowercaseText = selectedTexts[i].toLowerCase();
                    html = html + `<div class="form-group col-md-4">
                                <label for="name_` + lowercaseText + `" class="font-14 bold black mb-2">` +
                        selectedTexts[i] + ` {{ translate('Cost') }}<span
                                        class="text-danger"> * </span>
                                </label>
                                <input type="number" name="cost[]" id="` + lowercaseText + `_cost" class="form-control"
                                    value="" placeholder="` + selectedTexts[i] + ` {{ translate('Cost') }}">
                            </div>`
                }
            }
            $('#pricing_plans').html(html)
        }

        // Function to search for an ID in the data array and return its index
        function searchById(selectedPlannings, id) {
            'use strict'
            for (var i = 0; i < selectedPlannings.length; i++) {
                if (selectedPlannings[i].id + '' == id + '') {
                    return i;
                }
            }
            return -1;
        }
    </script>
@endsection
