@php
    use Plugin\TlcommerceCore\Repositories\SettingsRepository;
    $active_tab = request()->has('tab') && request()->get('tab') != null ? request()->get('tab') : 'general';
@endphp

@extends('core::base.layouts.master')
@section('title')
    {{ translate('Ecommerce Settings') }}
@endsection
@section('custom_css')
    <style>
        @media only screen and (max-width: 767px) {
            .theme-option-tab-wrap {
                grid-template-columns: 51px 1fr !important;
            }
        }
    </style>
@endsection
@section('main_content')
    <div class="theme-option-container">
        <form id="ecommerce-settings-form">
            <div class="theme-option-sticky d-flex align-items-center justify-content-between bg-white border-bottom2 p-3">
                <div class="theme-option-logo d-none d-sm-block">
                    <h4>{{ translate('Ecommerce Settings') }}</h4>
                </div>
            </div>
            <div class="theme-option-tab-wrap">
                <div class="nav flex-column border-right2 py-3" aria-orientation="vertical">
                    <a class="nav-link {{ $active_tab == 'general' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'general']) }}">
                        <i class="icofont-ui-settings" title="{{ translate('General') }}"></i>
                        <span>{{ translate('General') }}</span>
                    </a>

                    <a class="nav-link {{ $active_tab == 'products' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'products']) }}">
                        <i class="icofont-bucket1" title="{{ translate('Products') }}"></i>
                        <span>{{ translate('Products') }}</span>
                    </a>

                    <a class="nav-link {{ $active_tab == 'checkout' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'checkout']) }}">
                        <i class="icofont-cart" title="{{ translate('Checkout') }}"></i>
                        <span>{{ translate('Checkout') }}</span>
                    </a>

                    <a class="nav-link {{ $active_tab == 'customers' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'customers']) }}">
                        <i class="icofont-people" title="{{ translate('Customers') }}"></i>
                        <span>{{ translate('Customers') }}</span>
                    </a>

                    <a class="nav-link {{ $active_tab == 'orders' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'orders']) }}">
                        <i class="icofont-handshake-deal" title="{{ translate('Orders') }}"></i>
                        <span>{{ translate('Orders') }}</span>
                    </a>

                    <a class="nav-link {{ $active_tab == 'payments' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'payments']) }}">
                        <i class="icofont-pay" title="{{ translate('Payments') }}"></i>
                        <span>{{ translate('Payments') }}</span>
                    </a>

                    @if (isActivePluging('wallet'))
                        <a class="nav-link {{ $active_tab == 'wallet' ? 'active' : '' }}"
                            href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'wallet']) }}">
                            <i class="icofont-wallet" title="{{ translate('Wallet') }}"></i>
                            <span>{{ translate('Wallet') }}</span>
                        </a>
                    @endif

                    <a class="nav-link {{ $active_tab == 'invoice' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'invoice']) }}">
                        <i class="icofont-copy-invert" title="{{ translate('Invoice') }}"></i>
                        <span>{{ translate('Invoice') }}</span>
                    </a>

                    <a class="nav-link {{ $active_tab == 'email-notification' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'email-notification']) }}">
                        <i class="icofont-ui-email" title="{{ translate('Email Notification') }}"></i>
                        <span>{{ translate('Email Notification') }}</span>
                    </a>

                    <a class="nav-link {{ $active_tab == 'tax' ? 'active' : '' }}"
                        href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'tax']) }}">
                        <i class="icofont-money-bag" title="{{ translate('Tax') }}"></i>
                        <span>{{ translate('Tax') }}</span>
                    </a>

                    @if (isActivePluging('multivendor'))
                        <a class="nav-link {{ $active_tab == 'shop' ? 'active' : '' }}"
                            href="{{ route('plugin.tlcommercecore.ecommerce.configuration', ['tab' => 'shop']) }}">
                            <i class="icofont-prestashop" title="{{ translate('Shop Settings') }}"></i>
                            <span>{{ translate('Shop Settings') }}</span>
                        </a>
                    @endif
                </div>
                <div class="tab-content">
                    <!--General Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'general' ? 'show active' : '' }}" id="general">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.general')
                    </div>
                    <!--End General Settings-->
                    <!--Product Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'products' ? 'show active' : '' }}" id="products">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.products')
                    </div>
                    <!--End Product Settings-->
                    <!--Checkout Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'checkout' ? 'show active' : '' }}" id="checkout">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.checkout')
                    </div>
                    <!--End Checkout Settings-->
                    <!--Customer Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'customers' ? 'show active' : '' }}" id="customers">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.customer')
                    </div>
                    <!--End Customer Settings-->
                    <!--Order Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'orders' ? 'show active' : '' }}" id="orders">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.orders')
                    </div>
                    <!--End Order Settings-->
                    <!--Payment Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'payments' ? 'show active' : '' }}" id="payments">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.payment')
                    </div>
                    <!--End Payment Settings-->
                    <!--Wallet Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'wallet' ? 'show active' : '' }}" id="wallet">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.wallet')
                    </div>
                    <!--End Wallet Settings-->
                    <!--Invoice Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'invoice' ? 'show active' : '' }}" id="invoice">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.invoice')
                    </div>
                    <!--End Invoice Settings-->
                    <!--Email Notification Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'email-notification' ? 'show active' : '' }}"
                        id="emailNotification">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.email-notification')
                    </div>
                    <!--End Email Notification Settings-->
                    <!--Tax Settings-->
                    <div class="tab-pane fade {{ $active_tab == 'tax' ? 'show active' : '' }}" id="tax">
                        @includeIf('plugin/tlecommercecore::ecommerce-settings.tax')
                    </div>
                    <!--End Tax Settings-->
                    <!--Shop Settings-->
                    @if (isActivePluging('multivendor'))
                        <div class="tab-pane fade {{ $active_tab == 'shop' ? 'show active' : '' }}" id="shopSettings">
                            @includeIf('plugin/tlecommercecore::ecommerce-settings.shop')
                        </div>
                    @endif
                    <!--End Shop Settings-->
                </div>
            </div>

            <div class="theme-option-sticky d-flex justify-content-end bg-white border-top2 p-3">
                <div class="theme-option-action_bar">
                    <button class="btn long ecommerce-settings-update-btn">
                        {{ translate('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    @include('core::base.media.partial.media_modal')
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            initDropzone();
            /**
             * Enable and disable product review settings
             * 
             **/
            $('.enable-product-review').on('change', function(e) {
                if ($('input[name="enable_product_reviews"]').is(':checked')) {
                    $('.product-review-setting-group').removeClass('d-none');
                } else {
                    $('.product-review-setting-group').addClass('d-none');
                }
            });
            /**
             *Enable and disable order amount
             *  
             **/
            $('.enable-minumun-order-amount').on('change', function(e) {
                if ($('input[name="enable_minumun_order_amount"]').is(":checked")) {
                    $('.minimum-order-amount').removeClass('d-none');
                } else {
                    $('.minimum-order-amount').addClass('d-none');
                }
            });
            /**
             * Enable and disable coupon
             * 
             **/
            $('.enable-coupon-in-checkout').on('change', function(e) {
                if ($('input[name="enable_coupon_in_checkout"]').is(':checked')) {
                    $('.multiple-coupon-checkout').removeClass('d-none')
                } else {
                    $('.multiple-coupon-checkout').addClass('d-none')
                }
            });
            /**
             * Generate shop slug
             * 
             **/
            $(".shop-name").change(function(e) {
                e.preventDefault();
                let name = $(".shop-name").val();
                let permalink = string_to_slug(name);
                $("#permalink").html(permalink);
                $("#permalink_input_field").val(permalink);
                $(".permalink-input-group").removeClass("d-none");
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /*edit permalink*/
            $(".permalink-edit-btn").on("click", function(e) {
                e.preventDefault();
                let permalink = $("#permalink").html();
                $("#permalink-updated-input").val(permalink);
                $(".permalink-edit-btn").addClass("d-none");
                $(".permalink-editor").removeClass("d-none");
            });
            /*Cancel permalink edit*/
            $(".permalink-cancel-btn").on("click", function(e) {
                e.preventDefault();
                $("#permalink-updated-input").val();
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /*Update permalink*/
            $(".permalink-save-btn").on("click", function(e) {
                e.preventDefault();
                let input = $("#permalink-updated-input").val();
                let updated_permalink = string_to_slug(input);
                $("#permalink_input_field").val(updated_permalink);
                $("#permalink").html(updated_permalink);
                $(".permalink-editor").addClass("d-none");
                $(".permalink-edit-btn").removeClass("d-none");
            });
            /**
             * Save ecommmerce settings
             * 
             * 
             **/
            $('.ecommerce-settings-update-btn').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $("#ecommerce-settings-form").serialize(),
                    url: '{{ route('plugin.tlcommercecore.ecommerce.configuration.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ translate('Updated successfully') }}');
                        } else {
                            toastr.error('{{ translate('Update Failed. Please try again') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status == 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                toastr.error(error);
                            })
                        } else {
                            toastr.error('{{ translate('Update Failed. Please try again') }}');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
