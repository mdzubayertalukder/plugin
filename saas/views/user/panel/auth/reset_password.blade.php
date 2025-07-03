@php
    $active_theme = getActiveTheme();
@endphp
@if ($active_theme->location == 'default')
    @extends('theme/default::frontend.layout.master')

    @section('seo')
        {{-- SEO --}}
        <title>{{ translate('Set New Password') }}</title>
        <meta name="title" content="Set New Password">
        <meta name="description" content="Set New Password">
    @endsection

    @section('custom-css')
        <style>
            .bg-custom {
                background-color: #5dd9c1 !important;
            }

            .loginForm {
                margin: 0 auto;
                max-width: 500px;
            }

            .white-box {
                background-color: #fff;
                border: 1px solid #f7f8fa;
                box-shadow: 3px 3px 30px rgba(0, 0, 0, .04);
            }
        </style>
    @endsection

    @section('content')
        <div class="container my-5 pt-5">
            <div class="loginForm white-box px-3 py-4 p-md-5 my-5">
                <h3 class="mb-4">{{ translate('Set New Password') }}</h3>
                <form action="{{ route('subscriber.reset.password.post') }}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group mb-4">
                        <label>{{ translate('Email') }}</label>
                        <input type="email" id="email" name="email" class="form-control bg-light mb-0 border"
                            placeholder="{{ translate('Enter Email Address') }}">
                        @if ($errors->has('email'))
                            <div class="text-danger small">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label>{{ translate('New Password') }}</label>
                        <input type="password" name="password" class="form-control bg-light border mb-0"
                            placeholder="{{ translate('Enter your password') }}">
                        @if ($errors->has('password'))
                            <div class="text-danger small">{{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label>{{ translate('Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control bg-light border mb-0"
                            placeholder="{{ translate('Re-enter  your password') }}">
                        @if ($errors->has('password_confirmation'))
                            <div class="text-danger small">{{ $errors->first('password_confirmation') }}
                            </div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center">
                        <button type="submit" class="btn-crs s-btn w-100 bg-custom">{{ translate('Reset Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
@endif
