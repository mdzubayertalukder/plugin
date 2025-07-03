@php
    $active_theme = getActiveTheme();
@endphp
@if ($active_theme->location == 'default')
    @extends('theme/default::frontend.layout.master')

    @section('seo')
        {{-- SEO --}}
        <title>{{ translate('Forgot Password') }}</title>
        <meta name="title" content="Try1 Forgot Password">
        <meta name="description" content="Try1 Forgot Password">
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
                <h3 class="mb-0">{{ translate('Forgot password ?') }}</h3>
                <p class="mb-4 small">{{ translate('Enter your email address to recover your password.') }} </p>

                <form action="{{ route('subscriber.email.reset.password.link') }}" method="post">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="email" class="mb-2 font-14 bold black">{{ translate('Email') }}</label>
                        <input type="email" id="email" name="email" class="bg-light form-control mb-0 border"
                            placeholder="{{ translate('Email Address') }}">
                        @if ($errors->has('email'))
                            <div class="text-danger small">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn-crs s-btn bg-custom w-100">
                            {{ translate('Send Password Reset Link') }}
                        </button>
                    </div>

                    <div class="form-group">
                        <a href="{{ route('subscriber.login') }}"
                            class="btn-link small">{{ translate('Back to Login') }}</a>
                    </div>
                </form>
            </div>
        </div>
    @endsection
@endif
