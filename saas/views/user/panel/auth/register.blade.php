@php
    $active_theme = getActiveTheme();
@endphp
@if ($active_theme->location == 'default')
    @extends('theme/default::frontend.layout.master')

    @section('seo')
        {{-- SEO --}}
        <title>{{ translate('Registration') }}</title>
        <meta name="title" content="Registration">
        <meta name="description" content="Registration">
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
                <h3 class="mb-4">{{ translate('Registration') }}</h3>
                <form action="{{ route('plugin.saas.user.registration') }}" method="post" id="registration-form">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="name">{{ translate('Name') }} <span class="text-danger"> *</span></label>
                        <input type="text" id="name" name="name" class="form-control bg-light border mb-0"
                            placeholder="{{ translate('Name') }}" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <div class="text-danger small">{{ $errors->first('name') }}</div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="email">{{ translate('Email') }} <span class="text-danger"> *</span></label>
                        <input type="email" id="email" name="email" class="form-control mb-0 border bg-light"
                            placeholder="{{ translate('Email Address') }}">
                        @if ($errors->has('email'))
                            <div class="text-danger small">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="password">{{ translate('Password') }}<span class="text-danger"> *</span></label>
                        <input type="password" id="password" name="password" class="form-control mb-0 bg-light border"
                            placeholder="{{ translate('******') }}">
                        @if ($errors->has('password'))
                            <div class="text-danger small">{{ $errors->first('password') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="password_confirmation">{{ translate('Password Confirmation') }}<span
                                class="text-danger"> *</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control bg-light border mb-0" placeholder="{{ translate('******') }}">
                        @if ($errors->has('password_confirmation'))
                            <div class="text-danger small">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn-crs s-btn bg-custom w-100" class="submit-btn" id="register">
                            <img src="{{ asset('/public/backend/assets/img/loader-w4.svg') }}" alt=""
                                class="d-none" id="loader">
                            {{ translate('Register') }}
                        </button>
                    </div>

                    <div class="d-flex justify-content-center">
                        <p class="small">{{ translate('Already have account? ') }}
                            <a href="{{ route('subscriber.login') }}" class="btn-link">{{ translate('Login') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    @endsection
@endif

@section('custom-js')
    <script>
        $(document).ready(function() {
            "use strict";
            $('#registration-form').submit(function(event) {
                event.preventDefault()
                $('#register').prop('disabled', true)
                $('#loader').removeClass('d-none')
                this.submit();
            });
        })
    </script>
@endsection
