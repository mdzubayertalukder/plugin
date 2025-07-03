@php
    $active_theme = getActiveTheme();
@endphp
@if ($active_theme->location == 'default')
    @extends('theme/default::frontend.layout.master')

    @section('seo')
        {{-- SEO --}}
        <title>{{ translate('User Login') }}</title>
        <meta name="title" content="Login">
        <meta name="description" content="Login">
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
                <h3 class="mb-4">{{ translate('Login') }}</h3>
                <form action="{{ route('subscriber.attemptLogin') }}" method="post" id="login-form">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="email">{{ translate('Email') }}</label>
                        <input type="email" id="email" name="email" class="bg-light form-control border mb-0"
                            placeholder="{{ translate('Email Address') }}" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <div class="text-danger mt-2 small">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="password">{{ translate('Password') }}</label>
                        <input type="password" id="password" name="password" class="bg-light form-control border mb-0"
                            placeholder="{{ translate('********') }}">
                        @if ($errors->has('password'))
                            <div class="text-danger mt-2 small">{{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn-crs s-btn w-100 bg-custom" id="login">
                            <img src="{{ asset('/public/backend/assets/img/loader-w4.svg') }}" alt=""
                                class="d-none" id="loader">
                            {{ translate('Login') }}
                        </button>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a href="{{ route('subscriber.password.reset.link') }}"
                            class="btn-link small">{{ translate('Forgot Password?') }}</a>
                    </div>
                    <div class="d-flex justify-content-center">
                        <span class="small">{{ translate('Not registered yet? ') }}</span>
                        <a href="{{ route('plugin.saas.user.registration') }}"
                            class="btn-link small ml-1">{{ translate('Register Now') }}</a>
                    </div>
                    @if (env('APP_DEMO'))
                        <div class="d-flex justify-content-center mt-4">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td class="demo-cred"><span>subscriber@example.com</span></td>
                                        <td class="demo-cred"><span>111111</span></td>
                                        <td class="demo-cred">
                                            <button type="button" class="copy" data-email="subscriber@example.com"
                                                data-password="111111"><i class="fa fa-clone"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @endsection
@endif
@section('custom-js')
    <script>
        $(document).ready(function() {
            "use strict";
            $('#login-form').submit(function(event) {
                event.preventDefault()
                $('#login').prop('disabled', true)
                $('#loader').removeClass('d-none')
                this.submit();
            });

            $(".copy").on("click", function() {
                let email = $(this).data('email')
                let password = $(this).data('password')
                $('#email').val(email)
                $('#password').val(password)
            })
        })
    </script>
@endsection
