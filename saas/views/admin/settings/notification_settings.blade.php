@extends('core::base.layouts.master')
@section('title')
    {{ translate('Saas Notification Settings') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-7 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="post-head d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="content">
                                <h4 class="mb-1">{{ translate('Saas Notification Settings') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div>
                        <form action="{{ route('plugin.saas.admin.notification.settings') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-row mb-20">
                                <div class="col-md-12">
                                    <label
                                        class="font-14 bold black">{{ translate('Select user group to send "New Registration" Notification') }}</label>
                                    <select id='roles_of_new_reg_noti' name="roles_of_new_reg_noti[]"
                                        class="theme-input-style" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" class="text-uppercase"
                                                @selected(in_array($role->id, $settings_data['roles_of_new_reg_noti']))>{{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('roles_of_new_reg_noti'))
                                        <div class="invalid-input">{{ $errors->first('roles_of_new_reg_noti') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-12">
                                    <label
                                        class="font-14 bold black">{{ translate('Select user group to send "New Subscription" Notification') }}</label>
                                    <select id='roles_of_new_subs_noti' name="roles_of_new_subs_noti[]"
                                        class="theme-input-style" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" class="text-uppercase"
                                                @selected(in_array($role->id, $settings_data['roles_of_new_subs_noti']))>{{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('roles_of_new_subs_noti'))
                                        <div class="invalid-input">{{ $errors->first('roles_of_new_subs_noti') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-12">
                                    <label
                                        class="font-14 bold black">{{ translate('Select user group to send "Change Subscription Plan" Notification') }}</label>
                                    <select id='roles_of_chng_subs_noti' name="roles_of_chng_subs_noti[]"
                                        class="theme-input-style" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" class="text-uppercase"
                                                @selected(in_array($role->id, $settings_data['roles_of_chng_subs_noti']))>{{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('roles_of_chng_subs_noti'))
                                        <div class="invalid-input">{{ $errors->first('roles_of_chng_subs_noti') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-12">
                                    <label
                                        class="font-14 bold black">{{ translate('Select user group to send "New Custom Domain Request" Notification') }}</label>
                                    <select id='roles_of_new_custom_domain_req_noti'
                                        name="roles_of_new_custom_domain_req_noti[]" class="theme-input-style" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" class="text-uppercase"
                                                @selected(in_array($role->id, $settings_data['roles_of_new_custom_domain_req_noti']))>{{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('roles_of_new_custom_domain_req_noti'))
                                        <div class="invalid-input">
                                            {{ $errors->first('roles_of_new_custom_domain_req_noti') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Submit') }}</button>
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
            $(document).ready(function() {
                is_for_browse_file = true
                filtermedia()
                $('#roles_of_new_subs_noti').select2({
                    theme: "classic",
                    placeholder: '{{ translate('No Option Selected') }}',
                })
                $('#roles_of_chng_subs_noti').select2({
                    theme: "classic",
                    placeholder: '{{ translate('No Option Selected') }}',
                })
                $('#roles_of_new_reg_noti').select2({
                    theme: "classic",
                    placeholder: '{{ translate('No Option Selected') }}',
                })
                $('#roles_of_new_custom_domain_req_noti').select2({
                    theme: "classic",
                    placeholder: '{{ translate('No Option Selected') }}',
                })
            });
        })(jQuery);
    </script>
@endsection
