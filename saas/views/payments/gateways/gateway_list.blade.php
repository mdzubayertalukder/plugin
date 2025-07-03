@extends('core::base.layouts.master')
@section('title')
    {{ translate('Payment Methods') }}
@endsection
@section('custom_css')
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
    <style>
        .payment-method-item-body {
            gap: 2rem !important;
        }
    </style>
@endsection
@section('main_content')
    <div class="border-bottom2 pb-3 mb-4">
        <h4><i class="icofont-pay"></i> {{ translate('Payment Methods') }}</h4>
    </div>
    @if (count($payment_methods) > 0)
        @foreach ($payment_methods as $key => $method)
            <div class="card mb-30">
                <div class="card-bod">
                    <div class="payment-method-items">
                        <div class="payment-method-item">
                            <!--Payment title-->
                            <div class="payment-method-item-header px-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon ">
                                            <i class="icofont-pay"></i>
                                        </div>
                                    </div>
                                    <div class="payment-logo">
                                        <h4 class="black">{{ $method->name }}</h4>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-15">
                                    <label class="switch glow primary medium">
                                        <input type="checkbox" data-payment="{{ $method->id }}"
                                            class="payment-method-status"
                                            @if ($method->status == config('settings.general_status.active')) checked @endif />
                                        <span class="control"></span>
                                    </label>
                                    <button class="btn sm get-configuration" data-id="{{ $method->id }}"><i
                                            class="icofont-settings"></i> Configuration
                                    </button>
                                </div>
                            </div>
                            <!--End payment title-->
                            <!--Payment Configuration-->
                            <div id="item-body-{{ $method->id }}" class="hidden configuration">
                            </div>
                            <!--End payment Configuration-->
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p class="alert alert-danger">{{ translate('No payment method found') }}</p>
    @endif
    @include('core::base.media.partial.media_modal')
    <!--Edit Modal-->
    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="title" class="mr-1"></span>{{ translate('Configuration') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body form-container">

                </div>
            </div>
        </div>
    </div>
    <!--End Edit Modal-->
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            initDropzone()
            $(document).ready(function() {
                is_for_browse_file = true
                filtermedia()
            });
            /**
             *Active and deactive product review
             *
             **/
            $('.payment-method-status').on('change', function(e) {
                e.preventDefault();
                let id = $(this).data('payment');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        id: id
                    },
                    url: '{{ route('plugin.saas.payments.methods.status.update') }}',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        location.reload();
                    }
                });
            });
            /**
             * Paddle recurring switcher
             * 
             **/
            $(document).on('change', '.paddle-recurring-switcher', function(e) {
                e.preventDefault();
                var isChecked = $(this).is(':checked');
                if (isChecked) {
                    $('.paddle-credentails').addClass('d-none');
                    $('.paddle-recurring-credentails').removeClass('d-none');
                } else {
                    $('.paddle-recurring-credentails').addClass('d-none');
                    $('.paddle-credentails').removeClass('d-none');
                }
            });
            ///will get configuration
            $(".get-configuration").on("click", function(e) {
                e.preventDefault();
                $('.configuration').fadeOut("slow");
                $('.configuration').removeClass('border-top2');
                $(".configuration").html('');
                let id = $(this).data("id");
                let body_id = "item-body-" + id;
                if ($("#" + body_id).css('display') === 'block') {
                    return 0;
                }
                $.ajax({
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    url: '{{ route('plugin.saas.payments.methods.credential.edit') }}',
                    success: function(response) {
                        if (response.success) {

                            $("#" + body_id).html(response.html);
                            $("#" + body_id).addClass('border-top2');
                            $("#" + body_id).fadeIn('slow');
                            initSummerNote();
                        } else {
                            toastr.error('{{ translate('No configuration found') }}');
                        }
                    },
                    error: function(response) {
                        toastr.error('{{ translate('No configuration found') }}');
                    }
                });
            });
            /**
             * Update payment method credential
             *
             **/
            $(document).on("submit", "#credential-form", function(e) {
                e.preventDefault();
                $(document).find(".invalid-input").remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $("#credential-form").serialize(),
                    url: '{{ route('plugin.saas.payments.methods.credential.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ translate('Credential updated successfully') }}');
                            $("#edit-modal").modal("hide");
                        } else {
                            toastr.error('{{ translate('Update Failed ') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status == 422) {
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').closest(
                                    '.input-option').after(
                                    '<div class="invalid-input">' + error + '</div>')
                            })
                        } else {
                            toastr.error('{{ translate('Update Failed ') }}');
                        }
                    }
                });

            });

            function initSelect2() {
                $('.selectCurrency').select2({
                    theme: 'classic'
                });
            }

            function initSummerNote() {
                $('#instruction').summernote({
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
                        ["view", ["fullscreen", "help"]],
                    ],
                    placeholder: 'Write Instructions',
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
            }
        })(jQuery);
    </script>
@endsection
