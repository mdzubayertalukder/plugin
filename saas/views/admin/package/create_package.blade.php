@php
    $plans = getAllPlans();
    $plugins = availablePluginsForTenant();
    $payment_methods = getTenantPaymentGateways();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Create New Package') }}
@endsection
@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--  End select2  -->
@endsection
@section('main_content')
    <!-- Main Content -->
    <form class="form-horizontal my-4 mb-4" id="create_package" action="{{ route('plugin.saas.store.package') }}" method="post"
        enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                {{-- Package Details --}}
                <div class="card mb-30">
                    <div class="d-sm-flex justify-content-between align-items-center card-header py-3 bg-white">
                        <h4>{{ translate('Package Details') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="name" class="font-14 bold black mb-2">{{ translate('Name') }}<span
                                        class="text-danger"> * </span>
                                </label>
                                <input type="text" name="name" id="name" class="theme-input-style"
                                    value="{{ old('name') }}" placeholder="{{ translate('Name') }}" required>
                                @if ($errors->has('name'))
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                @endif
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-14 bold black mb-2">{{ translate('Type') }} </label>
                                <select class="theme-input-style" id="type" name="type">
                                    <option value="paid">{{ translate('Paid') }}</option>
                                    <option value="free">{{ translate('Free') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4 mb-2">
                                <label class="font-14 bold black">{{ translate('Featured') }} <span class="text-danger"> *
                                    </span></label>
                                <select class="theme-input-style" name="is_featured">
                                    <option value="0">{{ translate('NO') }}</option>
                                    <option value="1">{{ translate('Yes') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4 mb-2">
                                <label class="font-14 bold black">{{ translate('Status') }} </label>
                                <select class="theme-input-style" name="status">
                                    <option value="1">{{ translate('Enabled') }}</option>
                                    <option value="0">{{ translate('Disabled') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-2" id="trail_period_div">
                                <label class="font-14 bold black">{{ translate('Trial Period') }}</label>
                                <input type="text" name="trail_period" id="trail_period" class="theme-input-style"
                                    value="{{ old('trail_period') }}" placeholder="{{ translate('Trail Period') }}">
                                @if ($errors->has('trail_period'))
                                    <p class="text-danger">{{ $errors->first('trail_period') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Package Details --}}

                {{-- Pricing Plans --}}
                <div class="card mb-30 pricing_plan">
                    <div class="card pricing_plan">
                        <div class="d-sm-flex justify-content-between align-items-center card-header py-3 bg-white">
                            <h4>{{ translate('Package Plans & Pricing') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12" id="select_plans">
                                    <label class="font-14 bold black mb-2">{{ translate('Select Plan') }}<span
                                            class="text-danger"> * </span></label>
                                    <select class="theme-input-style" name="plans[]" multiple="multiple"
                                        id="multi_plan_input" onchange="addPlanPrice()">
                                        @for ($i = 0; $i < sizeof($plans); $i++)
                                            <option value="{{ $plans[$i]->id }}" data-name="{{ $plans[$i]->name }}"
                                                {{ collect(old('plans'))->contains($plans[$i]->id) ? 'selected' : '' }}>
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

                            </div>
                        </div>
                    </div>
                </div>
                {{-- Pricing Plans --}}

                {{-- Features --}}
                <div class="row">
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
                                                    checked>
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
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Create Page') }}</label>
                                    <input type="number" name="package_privileges_create_page" class="theme-input-style"
                                        placeholder="{{ translate('Number of pages can create') }}" min="-1"
                                        value="{{ old('package_privileges_create_page') ? old('package_privileges_create_page') : -1 }}"
                                        required>
                                    <small class="font-12 bold">{{ translate('-1 For Unlimitted') }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Create Blog') }}</label>
                                    <input type="number" name="package_privileges_create_blog" class="theme-input-style"
                                        placeholder="{{ translate('Number of blogs can create') }}" min="-1"
                                        value="{{ old('package_privileges_create_blog') ? old('package_privileges_create_blog') : -1 }}"
                                        required>
                                    <small class="font-12 bold">{{ translate('-1 For Unlimitted') }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Store Product') }}</label>
                                    <input type="number" name="package_privileges_create_product"
                                        class="theme-input-style"
                                        placeholder="{{ translate('Number of products can upload') }}" min="-1"
                                        value="{{ old('package_privileges_create_product') ? old('package_privileges_create_product') : -1 }}"
                                        required>
                                    <small class="font-12 bold">{{ translate('-1 For Unlimitted') }}</small>

                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Customers') }}</label>
                                    <input type="number" name="package_privileges_customers" class="theme-input-style"
                                        placeholder="{{ translate('Number of customers can have') }}" min="-1"
                                        value="{{ old('package_privileges_customers') ? old('package_privileges_customers') : -1 }}"
                                        required>
                                    <small class="font-12 bold">{{ translate('-1 For Unlimitted') }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="font-14 bold black mb-2">{{ translate('Custom Domain') }}</label>
                                    <input type="number" name="package_privileges_custom_domain"
                                        class="theme-input-style"
                                        placeholder="{{ translate('Number of custom domains') }}" min="-1"
                                        value="{{ old('package_privileges_custom_domain') ? old('package_privileges_custom_domain') : -1 }}"
                                        required>
                                    <small class="font-12 bold">{{ translate('-1 For Unlimitted') }}</small>

                                </div>
                                <div class="form-group">
                                    <label
                                        class="font-14 bold black mb-2">{{ translate('Maximum Allocated Storage (MB)') }}</label>
                                    <input type="number" name="package_privileges_allocated_storage"
                                        class="theme-input-style"
                                        placeholder="{{ translate('Maximum Allocated Storage') }}" min="-1"
                                        value="{{ old('package_privileges_allocated_storage') ? old('package_privileges_allocated_storage') : -1 }}"
                                        required>
                                    <small class="font-12 bold">{{ translate('-1 For Unlimitted') }}</small>
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
                                            <label class="font-14 bold black mb-2">Cash On Delivery</label>
                                        @else
                                            <label class="font-14 bold black mb-2">{{ ucfirst($method) }}</label>
                                        @endif

                                        <label class="switch glow primary medium">
                                            <input type="checkbox" name="payment_methods[]" value="{{ $id }}"
                                                checked>
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
                    <button type="submit" name="status" value="{{ config('settings.general_status.active') }}"
                        class="btn btn-outline-primary" tabindex="4">
                        {{ translate('Create Package') }}
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
                placeholder: '{{ translate('No Option Selected') }}',
            });
        })

        $('#type').change(function() {
            'use strict'
            showPlan()
        })

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
            let selectedTexts = [];
            selectedOptions.each(function() {
                selectedTexts.push($(this).data('name'));
            });

            let html = ``

            for (let i = 0; i < selectedTexts.length; i++) {
                let lowercaseText = selectedTexts[i].toLowerCase();
                html = html + `<div class="form-group col-md-4">
                                <label for="name_` + lowercaseText + `" class="font-14 bold black mb-2">{{ translate('`+selectedTexts[i]+` Cost') }}<span
                                        class="text-danger"> * </span>
                                </label>
                                <input type="number" name="cost[]" id="` + lowercaseText + `_cost" class="form-control"
                                    value="{{ old('`+lowercaseText+`_cost') }}" placeholder="{{ translate('`+selectedTexts[i]+` Cost') }}">
                                @if ($errors->has('`+lowercaseText+`_cost'))
                                    <p class="text-danger">{{ $errors->first('`+lowercaseText+`_cost') }}</p>
                                @endif
                            </div>`
            }
            $('#pricing_plans').html(html)
        }
    </script>
@endsection
