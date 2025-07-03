@php
    $package_repository = new \Plugin\Saas\Repositories\PackageRepository();
    $packages = $package_repository->getAllPaidPackages();
@endphp
@extends('core::base.layouts.master')
@section('title')
    {{ translate('Edit Coupon') }}
@endsection
@section('custom_css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--  End select2  -->
@endsection
@section('main_content')
    <!-- Edit Coupon -->

    <form method="post" class="form-horizontal mb-4" autocomplete="off" action="{{ route('plugin.saas.update.coupon') }}"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{ $coupon->id }}" name="id">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-30">
                    <div class="card-body row">
                        <div class="d-sm-flex justify-content-between align-items-center m-3">
                            <h4 class="font-20">{{ translate('Edit Coupon') }}</h4>
                        </div>
                        <input type="hidden" name="is_for_download" id="is_for_download" value="0" />
                        <div class="my-3 col-md-12">
                            <label class="font-14 bold black mb-2">{{ translate('Coupon Name') }} <span class="required">
                                    *</span></label>
                            <input type="text" class="theme-input-style" name="coupon_name" id="coupon_name"
                                value="{{ $coupon->coupon_name }}" readonly>
                            @if ($errors->has('coupon_name'))
                                <p class="text-danger">{{ $errors->first('coupon_name') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12">
                            <input type="hidden" name="coupon_type" value="{{ $coupon->coupon_type }}">
                            <label class="font-14 bold black mb-2">{{ translate('Coupon Type') }} <span class="required">
                                    *</span></label>
                            <select class="theme-input-style" name="coupon_type" id="coupon_type"
                                onchange="togglePackageSelectionInputs()" disabled>
                                <option value="discount" {{ $coupon->coupon_type == 'discount' ? 'selected' : '' }}>
                                    {{ translate('Discount') }}
                                </option>
                                <option value="redeemable" {{ $coupon->coupon_type == 'redeemable' ? 'selected' : '' }}>
                                    {{ translate('Redeemable') }}
                                </option>
                            </select>
                            @if ($errors->has('coupon_type'))
                                <p class="text-danger">{{ $errors->first('coupon_type') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12" id="single_packages">
                            <label class="font-14 bold black mb-2">{{ translate('Applicable Package') }} <span
                                    class="required">
                                    *</span></label>
                            <select class="theme-input-style" name="package">
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}" @selected($coupon->packages->contains('id', $package->id))>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('package'))
                                <p class="text-danger">{{ translate('Valid applicable package is required') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12" id="multi_packages">
                            <label class="font-14 bold black mb-2">{{ translate('Applicable Packages') }} <span
                                    class="required">
                                    *</span></label>
                            <select class="theme-input-style" name="packages[]" multiple="multiple"
                                id="multi_package_input">
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}" @selected($coupon->packages->contains('id', $package->id))>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('packages'))
                                <p class="text-danger">{{ translate('Valid applicable package is required') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12">
                            <label
                                class="font-14 bold black mb-2">{{ translate('Number Of Times This Coupon Can Be Used') }}
                                <span class="required">
                                    *</span></label>
                            <input type="number" min="1" class="theme-input-style" name="coupon_usable_times"
                                id="coupon_usable_times" value="{{ $coupon->coupon_usable_times }}">
                            @if ($errors->has('coupon_usable_times'))
                                <p class="text-danger">{{ $errors->first('coupon_usable_times') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12">
                            <label class="font-14 bold black mb-2">{{ translate('Coupon Code') }} <span class="required">
                                    *</span></label>
                            <input type="text" class="theme-input-style" name="coupon_code" id="coupon_code"
                                value="{{ $coupon->coupon_code }}" readonly>
                            @if ($errors->has('coupon_code'))
                                <p class="text-danger">{{ $errors->first('coupon_code') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12" id="discount">
                            <label class="font-14 bold black mb-2">{{ translate('Discount(%)') }} <span class="required">
                                    *</span></label>
                            <input type="number" class="theme-input-style" name="discount" min="0"
                                value="{{ $coupon->discount }}">
                            @if ($errors->has('discount'))
                                <p class="text-danger">{{ $errors->first('discount') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12">
                            <label class="font-14 bold black mb-2">{{ translate('Days(Valid For)') }} <span
                                    class="required">
                                    *</span></label>
                            <input type="number" class="theme-input-style" name="valid_days" min="1"
                                value="{{ $coupon->valid_for_days }}">
                            @if ($errors->has('valid_days'))
                                <p class="text-danger">{{ $errors->first('valid_days') }}</p>
                            @endif
                        </div>

                        <div class="my-3 col-md-12">
                            <button class="btn sm" onclick="save(event)">{{ translate('Save Changes') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- End Edit Coupon -->
@endsection
@section('custom_scripts')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <!--End Select2-->
    <script>
        $(document).ready(function() {
            'use strict';
            $('#multi_package_input').select2({
                theme: "classic",
                placeholder: '{{ translate('No Option Selected ') }}',
            });

            $('#single_packages').hide()

            $("#bulk_generator").prop("checked", false);
            $('.bulk_coupon').hide()
            togglePackageSelectionInputs()
        })

        // toggle package selection based on coupon type
        function togglePackageSelectionInputs() {
            'use strict';
            let coupon_type = $('#coupon_type').val()

            if (coupon_type == 'discount') {
                $('#multi_packages').show()
                $('#single_packages').hide()
                $('#discount').show()
            } else {
                $('#multi_packages').hide()
                $('#single_packages').show()
                $('#discount').hide()
            }
        }

        // toggle bulk generator
        function toggleBulkGenerator(params) {
            'use strict';
            if ($('#bulk_generator').is(":checked")) {
                $('.bulk_coupon').show()
                $('#coupon_code').attr('disabled', 'disabled')
                $('#coupon_name').attr('disabled', 'disabled')
                $('#coupon_code').val('')
                $('#coupon_name').val('')
            } else {
                $('.bulk_coupon').hide()
                $('#coupon_code').removeAttr('disabled')
                $('#coupon_name').removeAttr('disabled')
            }
        }

        //request for update coupon
        function save(event) {
            'use strict';
            event.closest('form').submit()
        }
    </script>
@endsection
