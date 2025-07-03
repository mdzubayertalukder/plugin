@extends('core::base.auth.auth_layout')
@section('title')
    {{ translate('Reset Password') }}
@endsection
@section('main_content')
    <div class="mn-vh-100 d-flex align-items-center">
        <div class="container">
            <!-- Card -->
            <div class="card justify-content-center auth-card">
                <div class="row justify-content-center">
                    <div class="col-xl-7 col-lg-9">
                        <h4 class="mb-5 font-20">{{ translate('Reset Password') }}</h4>
                        <form action="{{ route('plugin.multivendor.seller.password.reset.update') }}" method="post">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-row mb-20">
                                <div class="col-sm-4">
                                    <label class="font-14 bold black">{{ translate('Email') }}</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="email" id="email" name="email" class="theme-input-style"
                                        placeholder="{{ translate('Email Address') }}">
                                    @if ($errors->has('email'))
                                        <div class="text-danger fz-12">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-sm-4">
                                    <label class="font-14 bold black">{{ translate('Password') }}</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="password" name="password" class="theme-input-style"
                                        placeholder="{{ translate('Give your password') }}">
                                    @if ($errors->has('password'))
                                        <div class="text-danger fz-12">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row mb-20">
                                <div class="col-sm-4">
                                    <label class="font-14 bold black">{{ translate('Confirm Password') }}</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="password" name="password_confirmation" class="theme-input-style"
                                        placeholder="{{ translate('Confirm your password') }}">
                                    @if ($errors->has('password_confirmation'))
                                        <div class="text-danger fz-12">{{ $errors->first('password_confirmation') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <button type="submit" class="btn long mr-20">{{ translate('Reset Password') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Card -->
        </div>
    </div>
@endsection
