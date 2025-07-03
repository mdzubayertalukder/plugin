@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Seller Profile') }}
@endsection
@section('custom_css')
    <!-- ======= BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/apex/apexcharts.css') }}">
    <!-- ======= END BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
@endsection

@section('seller_main_content')
    <div class="row">
        <div class="mx-auto col-md-7 mb-30">
            <div class="card">
                <div class="card-header bg-white border-bottom2">
                    <h4 class="py-2">{{ translate('Profile Information') }}</h4>
                </div>
                <div class="card-body">
                    <div>
                        <form action="{{ route('plugin.multivendor.seller.profile.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <input type="hidden" name="is_for_profile" value="true">
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Profile Picture') }}</label>
                                </div>
                                <div class="col-md-8">
                                    @include('core::base.includes.media.media_input', [
                                        'input' => 'pro_pic',
                                        'data' => $user->image,
                                        'user_filter' => true,
                                    ])
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Name') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="name" class="theme-input-style"
                                        value="{{ $user->name }}" placeholder="{{ translate('Give your name') }}">
                                    @if ($errors->has('name'))
                                        <div class="invalid-input">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Email') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="email" name="email" class="theme-input-style"
                                        value="{{ $user->email }}"
                                        placeholder="{{ translate('Give your email address') }}">
                                    @if ($errors->has('email'))
                                        <div class="invalid-input">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Phone') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="phone" class="theme-input-style"
                                        value="{{ $phone }}" placeholder="{{ translate('Give your phone') }}">
                                    @if ($errors->has('phone'))
                                        <div class="invalid-input">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Old Password') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="password" name="old_password" class="theme-input-style"
                                        placeholder="{{ translate('Old password') }}">
                                    @if ($errors->has('old_password'))
                                        <div class="invalid-input">{{ $errors->first('old_password') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Password') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="password" name="password" class="theme-input-style"
                                        placeholder="{{ translate('Give your password') }}">
                                    @if ($errors->has('password'))
                                        <div class="invalid-input">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-md-4">
                                    <label class="font-14 bold black">{{ translate('Confirm Password') }}</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="password" name="password_confirmation" class="theme-input-style"
                                        placeholder="{{ translate('Confirm your password') }}">
                                    @if ($errors->has('password_confirmation'))
                                        <div class="invalid-input">{{ $errors->first('password_confirmation') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn long">{{ translate('Update') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @include('core::base.media.partial.media_modal')

        </div>
    </div>
@endsection

@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            initDropzone();
        })(jQuery);
    </script>
@endsection
