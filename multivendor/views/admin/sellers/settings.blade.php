@extends('core::base.layouts.master')
@section('title')
    {{ translate('Settings') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <style>
        .first-commision-option span.select2 {
            width: 100% !important;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-7 mb-30 mx-auto">
            <div class="card">
                <div class="card-header bg-white border-bottom2 pb-0">
                    <div class="post-head d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="content">
                                <h4 class="py-2">{{ translate('Settings') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <form action="{{ route('plugin.multivendor.admin.seller.settings.update') }}" method="POST">
                            @csrf
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Seller Default Commission Fee') }}
                                        (%)</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" name="seller_default_commission" class="theme-input-style"
                                        value="{{ getGeneralSetting('seller_default_commission') }}" placeholder="0.00">
                                    @if ($errors->has('seller_default_commission'))
                                        <div class="invalid-input">{{ $errors->first('seller_default_commission') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Category wise commission') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <label class="switch glow primary medium">
                                        <input type="checkbox" name="category_wise_seller_commission"
                                            class="category_wise_commission_switcher"
                                            {{ getGeneralSetting('category_wise_seller_commission') == config('settings.general_status.active') ? 'checked' : '' }}>
                                        <span class="control"></span>
                                    </label>
                                    @if ($errors->has('category_wise_seller_commission'))
                                        <div class="invalid-input">{{ $errors->first('category_wise_seller_commission') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="mb-20 categories-wise-commission-options {{ getGeneralSetting('category_wise_seller_commission') == config('settings.general_status.active') ? '' : 'd-none' }}">
                                @php
                                    $position = 0;
                                @endphp

                                @if ($categories_commissions->count() > 0)
                                    @foreach ($categories_commissions as $rate => $commissions)
                                        @php
                                            $position = $position + 1;
                                        @endphp
                                        <div class="commission-list form-row mb-20">
                                            <div class="col-md-3">
                                                <label class="black fz-12">{{ translate('Commission Fee') }} (%)</label>
                                                <input type="number" class="theme-input-style"
                                                    name="commission[{{ $position }}][rate]"
                                                    value="{{ $rate }}" placeholder="0.00" />
                                            </div>
                                            <div class="col-md-8">
                                                <label class="black fz-12">{{ translate('Categories') }}</label>
                                                <select class="theme-input-style select-categories" multiple
                                                    name="commission[{{ $position }}][categories][]">
                                                    @foreach ($commissions as $commission)
                                                        @if ($commission['category'] != null)
                                                            <option selected value="{{ $commission['category_id'] }}">
                                                                {{ $commission['category']->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button"
                                                    class="btn-danger mt-30 p-2 remove-commission rounded"> <i
                                                        class="icofont-ui-delete"></i></button>

                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="commission-list form-row mb-20">
                                        <div class="col-md-3">
                                            <label class="black fz-12">{{ translate('Commission Fee') }} (%)</label>
                                            <input type="number" class="theme-input-style"
                                                name="commission[{{ $position }}][rate]" placeholder="0.00" />
                                        </div>
                                        <div class="col-md-8 first-commision-option">
                                            <label class="black fz-12">{{ translate('Categories') }}</label>
                                            <select class="theme-input-style select-categories w-100" multiple
                                                name="commission[{{ $position }}][categories][]">
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn-danger mt-30 p-2 remove-commission rounded">
                                                <i class="icofont-ui-delete"></i></button>

                                        </div>
                                    </div>
                                @endif
                                <input type="hidden" id="position" value="{{ $position }}" />

                            </div>

                            <div
                                class="form-row mb-20 categories-wise-commission-options-btn {{ getGeneralSetting('category_wise_seller_commission') == config('settings.general_status.active') ? '' : 'd-none' }}">
                                <button type="button"
                                    class="btn rounded sm add-new-category-wise-commission">{{ translate('Add New') }}</button>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Seller Auto Verification') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <label class="switch glow primary medium">
                                        <input type="checkbox" name="seller_auto_verification"
                                            {{ getGeneralSetting('seller_auto_verification') == config('settings.general_status.active') ? 'checked' : '' }}>
                                        <span class="control"></span>
                                    </label>
                                    @if ($errors->has('seller_auto_verification'))
                                        <div class="invalid-input">{{ $errors->first('seller_auto_verification') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Product Auto Approve') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <label class="switch glow primary medium">
                                        <input type="checkbox" name="product_auto_approve"
                                            {{ getGeneralSetting('product_auto_approve') == config('settings.general_status.active') ? 'checked' : '' }}>
                                        <span class="control"></span>
                                    </label>
                                    @if ($errors->has('product_auto_approve'))
                                        <div class="invalid-input">{{ $errors->first('product_auto_approve') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label
                                        class="font-14 bold black">{{ translate('Seller Min Withdrawal Amount') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" name="seller_min_withdrawal_amount" class="theme-input-style"
                                        value="{{ getGeneralSetting('seller_min_withdrawal_amount') }}" placeholder="0.00">
                                    @if ($errors->has('seller_min_withdrawal_amount'))
                                        <div class="invalid-input">{{ $errors->first('seller_min_withdrawal_amount') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Shop Default Background') }}</label>
                                </div>
                                <div class="col-md-8">
                                    @include('core::base.includes.media.media_input', [
                                        'input' => 'shop_default_bg_image',
                                        'data' => getGeneralSetting('shop_default_bg_image'),
                                    ])
                                    @if ($errors->has('shop_default_bg_image'))
                                        <div class="invalid-input">{{ $errors->first('shop_default_bg_image') }}</div>
                                    @endif
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
        </div>
        @include('core::base.media.partial.media_modal')
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {

            "use strict";
            initDropzone()
            /**
             *  product category
             * 
             */
            $('.select-categories').select2({
                theme: "classic",
                placeholder: '{{ translate('Select  Categories') }}',
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.product.category.option') }}',
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
             * Category wise commission switcher
             * 
             **/
            $('.category_wise_commission_switcher').on('change', function(e) {
                if ($(this).is(":checked")) {
                    $('.categories-wise-commission-options').removeClass('d-none');
                    $('.categories-wise-commission-options-btn').removeClass('d-none');
                } else {
                    $('.categories-wise-commission-options').addClass('d-none');
                    $('.categories-wise-commission-options-btn').addClass('d-none');
                }
            });

            /**
             * 
             * Add new commission rate
             **/
            $('.add-new-category-wise-commission').on('click', function(e) {
                e.preventDefault();
                let node = $("#position").val();
                node = parseInt(node) + 1;
                $("#position").val(node);
                let html =
                    "<div class='commission-list form-row mb-20'> <div class ='col-md-3'><label class = 'black fz-12'> {{ translate('Commission Fee') }}( % ) </label> <input type = 'number' class = 'theme-input-style' name = 'commission[" +
                    node +
                    "][rate]' placeholder = '0.00' / ></div> <div class ='col-md-8'><label class ='black fz-12' > {{ translate('Categories') }} </label> <select class = 'theme-input-style select-categories' multiple name ='commission[" +
                    node +
                    "][categories][]'></select> </div> <div class='col-md-1'><button type='button' class='btn-danger mt-30 p-2 remove-commission rounded'> <i class='icofont-ui-delete'></i></button></div></div>";

                $('.categories-wise-commission-options').append(html);

                $('.select-categories').select2({
                    theme: "classic",
                    placeholder: '{{ translate('Select  Categories') }}',
                    closeOnSelect: false,
                    ajax: {
                        url: '{{ route('plugin.tlcommercecore.product.category.option') }}',
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
            });

            /**
             * Remove items
             * 
             **/
            $('.remove-commission').on('click', function(e) {
                e.preventDefault();
                $(this).parents(':eq(1)').remove();
            });
        })(jQuery);
    </script>
@endsection
