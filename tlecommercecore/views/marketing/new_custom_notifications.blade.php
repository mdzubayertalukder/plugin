@extends('core::base.layouts.master')
@section('title')
    {{ translate('New Custom Notifications') }}
@endsection
@section('custom_css')
    <!--Select2-->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
    <!--End select2-->
    <!--Editor-->
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <!--End editor-->
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-header border-bottom2 mb-20">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('New Custom Notifications') }}</h4>
                        <a href="{{ route('plugin.tlcommercecore.marketing.custom.notification') }}"
                            class="btn long">{{ translate('All Notifications') }}</a>
                    </div>

                </div>
                <div class="card-body">
                    <div>
                        <form action="{{ route('plugin.tlcommercecore.marketing.custom.notification.send') }}"
                            method="POST">
                            @csrf
                            <div class="form-row mb-20">
                                <div class="col-md-3">
                                    <label class="font-14 bold black">{{ translate('Send To') }} </label>
                                </div>
                                <div class="col-md-9">
                                    <select class="select-notification-user-type theme-input-style select-2" name="send_to">
                                        <option
                                            value="{{ config('tlecommercecore.custom_notification_receiver_type.all_customers') }}"
                                            @selected(old('send_to') == config('tlecommercecore.custom_notification_receiver_type.all_customers'))>
                                            {{ translate('All Customers') }}</option>
                                        <option
                                            value="{{ config('tlecommercecore.custom_notification_receiver_type.specific_customer') }}"
                                            @selected(old('send_to') == config('tlecommercecore.custom_notification_receiver_type.specific_customer'))>
                                            {{ translate('Specific Customers') }}</option>
                                        <option
                                            value="{{ config('tlecommercecore.custom_notification_receiver_type.all_users') }}"
                                            @selected(old('send_to') == config('tlecommercecore.custom_notification_receiver_type.all_users'))>
                                            {{ translate('All Users') }}</option>
                                        <option
                                            value="{{ config('tlecommercecore.custom_notification_receiver_type.specific_user') }}"
                                            @selected(old('send_to') == config('tlecommercecore.custom_notification_receiver_type.specific_user'))>
                                            {{ translate('Specific Users') }}
                                        </option>
                                        <option
                                            value="{{ config('tlecommercecore.custom_notification_receiver_type.specific_user_role') }}"
                                            @selected(old('send_to') == config('tlecommercecore.custom_notification_receiver_type.specific_user_role'))>
                                            {{ translate('Specific User Role') }}
                                        </option>
                                    </select>
                                    @if ($errors->has('send_to'))
                                        <div class="invalid-input">{{ $errors->first('send_to') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-row mb-20 customer-selector {{ old('send_to') == config('tlecommercecore.custom_notification_receiver_type.specific_customer') ? '' : 'd-none' }}">
                                <div class="col-md-3">
                                    <label class="font-14 bold black">{{ translate('Select Customers') }} </label>
                                </div>
                                <div class="col-md-9">
                                    <select class="select-customers theme-input-style" name="customers[]" multiple>
                                        <option>{{ translate('Select Customers') }}</option>
                                    </select>
                                    @if ($errors->has('customers'))
                                        <div class="invalid-input">{{ $errors->first('customers') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="form-row mb-20 user-selector {{ old('send_to') == config('tlecommercecore.custom_notification_receiver_type.specific_user') ? '' : 'd-none' }}">
                                <div class="col-md-3">
                                    <label class="font-14 bold black">{{ translate('Select Users') }} </label>
                                </div>
                                <div class="col-md-9">
                                    <select class="select-users theme-input-style" name="users[]" multiple>
                                        <option>{{ translate('Select Users') }}</option>
                                    </select>
                                    @if ($errors->has('name'))
                                        <div class="invalid-input">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="form-row mb-20 user-roles-selector {{ old('send_to') == config('tlecommercecore.custom_notification_receiver_type.specific_user_role') ? '' : 'd-none' }}">
                                <div class="col-md-3">
                                    <label class="font-14 bold black">{{ translate('Select User Roles') }} </label>
                                </div>
                                <div class="col-md-9">
                                    <select class="select-user-role theme-input-style" name="user_roles[]" multiple>
                                        <option>{{ translate('Select Users') }}</option>
                                    </select>
                                    @if ($errors->has('user_roles'))
                                        <div class="invalid-input">{{ $errors->first('user_roles') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-3">
                                    <label class="font-14 bold black">{{ translate('Notification Type') }} </label>
                                </div>
                                <div class="col-md-9">
                                    <select class="select-notification-notification-type theme-input-style select-2"
                                        name="notification_type">
                                        <option value="{{ config('tlecommercecore.custom_notification_type.dashboard') }}"
                                            @selected(old('notification_type') == config('tlecommercecore.custom_notification_type.dashboard'))>
                                            {{ translate('Dashboard') }}</option>
                                        <option value="{{ config('tlecommercecore.custom_notification_type.email') }}"
                                            @selected(old('notification_type') == config('tlecommercecore.custom_notification_type.email'))>
                                            {{ translate('Email') }}</option>
                                        <option
                                            value="{{ config('tlecommercecore.custom_notification_type.email_dashboard') }}"
                                            @selected(old('notification_type') == config('tlecommercecore.custom_notification_type.email_dashboard'))>
                                            {{ translate('Dashboard & Email') }}</option>
                                    </select>
                                    @if ($errors->has('notification_type'))
                                        <div class="invalid-input">{{ $errors->first('notification_type') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="form-row mb-20 email-subject {{ old('notification_type') == config('tlecommercecore.custom_notification_type.email_dashboard') || old('notification_type') == config('tlecommercecore.custom_notification_type.email') ? '' : 'd-none' }}">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black ">{{ translate('Subject') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="editor-wrap">
                                        <input type="text" class="theme-input-style" name="subject"
                                            value="{{ old('subject') }}"
                                            placeholder="{{ translate('Notification subject') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-sm-3">
                                    <label class="font-14 bold black ">{{ translate('Message') }} </label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="editor-wrap">
                                        <textarea id="message" name="message">{{ old('message') }}</textarea>
                                    </div>
                                    @if ($errors->has('message'))
                                        <div class="invalid-input">{{ $errors->first('message') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Send Now') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('custom_scripts')
    <!--Select2-->
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <!--End Select2-->
    <!--Editor-->
    <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
    <!--End Editor-->
    <script>
        (function($) {
            "use strict";
            $("#message").summernote({
                tabsize: 2,
                height: 200,
                codeviewIframeFilter: false,
                codeviewFilter: true,
                codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                toolbar: [
                    ["style", ["style"]],
                    ["font", ["bold", "underline", "clear"]],
                    ["color", ["color"]],
                    ["para", ["ul", "ol", "paragraph"]],
                    ["table", ["table"]],
                    ["insert", ["link", "video"]],
                    ["view", ["fullscreen", "codeview", "help"]],
                ],
                callbacks: {
                    onChangeCodeview: function(contents, $editable) {
                        let code = $(this).summernote('code')
                        code = code.replace(
                            /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                            '')
                        $(this).val(code)
                    }
                }
            });
            /**
             *  Select notification user tyle
             * 
             */
            $('.select-2').select2({
                theme: "classic",
            });
            /**
             * Select customer
             * 
             */
            $('.select-customers').select2({
                theme: "classic",
                placeholder: '{{ translate('Select customers') }}',
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.marketing.custom.notification.customer.options') }}',
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
             *Select users 
             */
            $('.select-users').select2({
                theme: "classic",
                placeholder: '{{ translate('Select users') }}',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.marketing.custom.notification.users.options') }}',
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
             *Select users  role
             */
            $('.select-user-role').select2({
                theme: "classic",
                placeholder: '{{ translate('Select user roles') }}',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('plugin.tlcommercecore.marketing.custom.notification.user.roles.options') }}',
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
             * Select notification user type
             * 
             */
            $(".select-notification-user-type").on('change', function(e) {
                e.preventDefault();
                let type = $(this).val();
                $('.select2-container--classic').addClass('w-100');
                if (type ==
                    {{ config('tlecommercecore.custom_notification_receiver_type.specific_customer') }}) {
                    $('.customer-selector').removeClass('d-none');
                    $('.user-selector').addClass('d-none');
                    $('.user-roles-selector').addClass('d-none');
                } else if (type ==
                    {{ config('tlecommercecore.custom_notification_receiver_type.specific_user') }}) {
                    $('.customer-selector').addClass('d-none');
                    $('.user-roles-selector').addClass('d-none');
                    $('.user-selector').removeClass('d-none');
                } else if (type ==
                    {{ config('tlecommercecore.custom_notification_receiver_type.specific_user_role') }}) {
                    $('.customer-selector').addClass('d-none');
                    $('.user-selector').addClass('d-none');
                    $('.user-roles-selector').removeClass('d-none');
                } else {
                    $('.customer-selector').addClass('d-none');
                    $('.user-selector').addClass('d-none');
                    $('.user-roles-selector').addClass('d-none');

                }
            });
            /**
             * Select notification type
             * 
             */
            $('.select-notification-notification-type').on('change', function(e) {
                e.preventDefault();
                let type = $(this).val();
                $('.select2-container--classic').addClass('w-100');
                if (type == {{ config('tlecommercecore.custom_notification_type.email') }} || type ==
                    {{ config('tlecommercecore.custom_notification_type.email_dashboard') }}) {
                    $('.email-subject').removeClass('d-none');
                } else {
                    $('.email-subject').addClass('d-none');
                }
            });
        })(jQuery);
    </script>
@endsection
