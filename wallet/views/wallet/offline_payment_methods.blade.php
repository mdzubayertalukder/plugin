@extends('core::base.layouts.master')
@section('title')
    {{ translate('Offline Payment Methods') }}
@endsection
@section('custom_css')
    <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2">
                    <div class="align-items-between d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20 mb-2">{{ translate('Offline Payment Methods') }}</h4>
                        <button class="btn long mb-auto" data-toggle="modal"
                            data-target="#new-payment-method-modal">{{ translate('Add New Payment Method') }}</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>

                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Logo') }}</th>
                                <th>{{ translate('Type') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th class="text-right">{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($payment_methods->count() > 0)
                                @foreach ($payment_methods as $key => $method)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            {{ $method->name }}
                                        </td>
                                        <td>
                                            <img src="{{ asset(getFilePath($method->logo)) }}" class="img-45">
                                        </td>
                                        <td>
                                            @if ($method->type == config('tlecommercecore.offline_payment_type.bank'))
                                                <p class="badge badge-info">{{ translate('Bank') }}</p>
                                            @elseif($method->type == config('tlecommercecore.offline_payment_type.custom'))
                                                <p class="badge badge-info">{{ translate('Custom') }}</p>
                                            @else
                                                <p class="badge badge-info">{{ translate('Cheque') }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($method->status == config('settings.general_status.active'))
                                                <p class="badge badge-success">{{ translate('Active') }}</p>
                                            @else
                                                <p class="badge badge-danger">{{ translate('InAactive') }}</p>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="dropdown-button">
                                                <a href="#" class="d-flex align-items-center justify-content-end"
                                                    data-toggle="dropdown">
                                                    <div class="menu-icon mr-0">
                                                        <span></span>
                                                        <span></span>
                                                        <span></span>
                                                    </div>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="payment-method-details"
                                                        data-method="{!! htmlspecialchars($method, ENT_QUOTES, 'UTF-8') !!}"
                                                        data-bank="{!! htmlspecialchars($method->bank_info, ENT_QUOTES, 'UTF-8') !!}"
                                                        data-logo="{{ asset(getFilePath($method->logo)) }}">
                                                        {{ translate('Details') }}
                                                    </a>
                                                    <a href="#" class="delete-payment-method"
                                                        data-method="{{ $method->id }}">{{ translate('Delete') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing Found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $payment_methods->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--New payment method modal-->
    <div id="new-payment-method-modal" class="new-payment-method-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Add New Payment Method') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="new-payment-method-form">
                        <div class="form-row mb-20">
                            <label class="black bold col-lg-3 col-12">{{ translate('Type') }}</label>
                            <select class="theme-input-style col-lg-9 col-12 method_type" name="method_type">
                                <option value="{{ config('tlecommercecore.offline_payment_type.custom') }}">
                                    {{ translate('Custom') }}</option>
                                <option value="{{ config('tlecommercecore.offline_payment_type.bank') }}">
                                    {{ translate('Bank') }}
                                </option>
                                <option value="{{ config('tlecommercecore.offline_payment_type.cheque') }}">
                                    {{ translate('Cheque') }}</option>
                            </select>
                        </div>
                        <div class="form-row mb-20">
                            <label class="black bold col-lg-3 col-12">{{ translate('Name') }}</label>
                            <input type="text" class="theme-input-style col-lg-9 col-12" name="name"
                                placeholder="{{ translate('Name') }}">
                        </div>

                        <div class="form-row mb-20">
                            <label class="black bold col-lg-3 col-12">{{ translate('Instruction') }}</label>
                            <div class="editor-wrap col-lg-9 cl-12">
                                <textarea name="instruction" id="instruction" class="instruction theme-input-style h-25" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-row mb-20 bank-information d-none">
                            <label class="black bold col-lg-3 col-12">{{ translate('Bank Information') }}</label>
                            <div class="col-12 col-lg-9 d-flex flex-wrap gap-10">
                                <input type="text" class="theme-input-style" name="bank_name"
                                    placeholder="{{ translate('Bank Name') }}">
                                <input type="text" class="theme-input-style" name="account_name"
                                    placeholder="{{ translate('Account Name') }}">
                                <input type="text" class="theme-input-style" name="account_number"
                                    placeholder="{{ translate('Account Number') }}">
                                <input type="text" class="theme-input-style" name="routing_number"
                                    placeholder="{{ translate('Routing Number') }}">
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <label class="black bold col-12 col-lg-3 col-12">{{ translate('Logo') }}</label>
                            @include('core::base.includes.media.media_input', [
                                'input' => 'payment_image',
                                'data' => null,
                            ])
                        </div>
                        <div class="form-row">
                            <div class="col-12 d-flex justify-content-end">
                                <input type="submit" name="action" value="Submit"
                                    class="btn long btn-success store-new-payment-method">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End  new payment method modal-->
    <!--Edit payment method modal-->
    <div id="edit-payment-method-modal" class="edit-payment-method-modal modal fade show" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Payment Method Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-payment-method-form">
                        <input type="hidden" name="id">
                        <div class="form-row mb-20">
                            <label class="black bold col-lg-3 col-12">{{ translate('Type') }}</label>
                            <select class="theme-input-style col-lg-9 col-12 method_type" name="method_type">
                                <option value="{{ config('tlecommercecore.offline_payment_type.custom') }}">
                                    {{ translate('Custom') }}</option>
                                <option value="{{ config('tlecommercecore.offline_payment_type.bank') }}">
                                    {{ translate('Bank') }}
                                </option>
                                <option value="{{ config('tlecommercecore.offline_payment_type.cheque') }}">
                                    {{ translate('Cheque') }}</option>
                            </select>
                        </div>
                        <div class="form-row mb-20">
                            <label class="black bold col-lg-3 col-12">{{ translate('Name') }}</label>
                            <input type="text" class="theme-input-style col-lg-9 col-12" name="name"
                                placeholder="{{ translate('Name') }}">
                        </div>

                        <div class="form-row mb-20">
                            <label class="black bold col-lg-3 col-12">{{ translate('Instruction') }}</label>
                            <div class="editor-wrap col-lg-9 cl-12">
                                <textarea name="instruction" id="edit_instruction" class="instruction theme-input-style h-25" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-row mb-20 bank-information">
                            <label class="black bold col-lg-3 col-12">{{ translate('Bank Information') }}</label>
                            <div class="col-12 col-lg-9 d-flex flex-wrap gap-10">
                                <input type="text" class="theme-input-style" name="bank_name"
                                    placeholder="{{ translate('Bank Name') }}">
                                <input type="text" class="theme-input-style" name="account_name"
                                    placeholder="{{ translate('Account Name') }}">
                                <input type="text" class="theme-input-style" name="account_number"
                                    placeholder="{{ translate('Account Number') }}">
                                <input type="text" class="theme-input-style" name="routing_number"
                                    placeholder="{{ translate('Routing Number') }}">
                            </div>
                        </div>
                        <div class="form-row mb-20">
                            <label class="black bold col-12 col-lg-3 col-12">{{ translate('Logo') }}</label>
                            @include('core::base.includes.media.media_input', [
                                'input' => 'edit_payment_image',
                                'data' => null,
                            ])
                        </div>
                        <div class="form-row mb-20">
                            <label class="black bold col-lg-3 col-12">{{ translate('Status') }}</label>
                            <select class="theme-input-style col-lg-9 col-12" name="status">
                                <option value="{{ config('settings.general_status.active') }}">
                                    {{ translate('Active') }}</option>
                                <option value="{{ config('settings.general_status.in_active') }}">
                                    {{ translate('Inactive') }}
                                </option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="col-12 d-flex justify-content-end">
                                <input type="submit" name="action" value="Save Changes"
                                    class="btn long btn-success update-payment-method">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End  edit payment method modal-->

    <!--Delete Modal-->
    <div id="delete-modal" class="delete-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to delete this') }}?</p>
                    <form method="POST" action="{{ route('plugin.wallet.recharge.offline.payment.methods.delete') }}">
                        @csrf
                        <input type="hidden" id="delete-id" name="id">
                        <button type="button" class="btn long mt-2 btn-danger"
                            data-dismiss="modal">{{ translate('cancel') }}</button>
                        <button type="submit" class="btn long mt-2">{{ translate('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->
    @include('core::base.media.partial.media_modal')
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
             * Payment instruction text editor
             * 
             **/
            $(".instruction").summernote({
                tabsize: 2,
                height: 200,
                codeviewIframeFilter: false,
                codeviewFilter: true,
                codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                toolbar: [
                    ["style", ["style"]],
                    ["font", ["fontname", "bold", "underline", "clear"]],
                    ["color", ["color"]],
                    ['insert', ['link']],
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
             * Select method type
             * 
             **/
            $(".method_type").on('change', function(e) {
                e.preventDefault();
                let val = $(this).val();
                if (val == {{ config('tlecommercecore.offline_payment_type.bank') }}) {
                    $(".bank-information").removeClass('d-none');
                } else {
                    $(".bank-information").addClass('d-none');
                }
            });
            /**
             *
             * Store offline payment method
             *  
             **/
            $(".store-new-payment-method").on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').html("");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#new-payment-method-form').serialize(),
                    url: '{{ route('plugin.wallet.recharge.offline.payment.methods.store') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ translate('New method added successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ translate('New method adding Failed ') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status == 422) {

                            $.each(response.responseJSON.errors, function(field_name, error) {
                                $(document).find('[name=' + field_name + ']').closest(
                                    '.theme-input-style').after(
                                    '<div class="d-flex invalid-input justify-content-end">' +
                                    error + '</div>')
                            })
                        } else {
                            toastr.error('{{ translate('New method adding Failed') }}');
                        }
                    }
                });
            });
            /**
             *Delete payment method
             *  
             **/
            $(".delete-payment-method").on('click', function(e) {
                e.preventDefault();

                var id = $(this).data('method');
                $("#delete-id").val(id);
                $("#delete-modal").modal('show');
            });
            /**
             * 
             * Load method details modal
             * 
             **/
            $(".payment-method-details").on('click', function(e) {
                e.preventDefault();
                let details = $(this).data('method');

                let bank_info = $(this).data('bank');
                let logo = $(this).data('logo');

                let form = $(document).find("#edit-payment-method-form");
                form.find('[name=name').val(details.name);
                form.find('[name=id').val(details.id);
                form.find('[name=method_type').val(details.type);
                form.find('[name=status').val(details.status);
                form.find('[name=edit_payment_image').val(details.logo);
                form.find('#edit_payment_image_preview').attr('src', logo);

                if (bank_info) {
                    form.find('.bank-information').removeClass('d-none');
                    form.find('[name=bank_name').val(bank_info.bank_name);
                    form.find('[name=account_name').val(bank_info.account_name);
                    form.find('[name=account_number').val(bank_info.account_number);
                    form.find('[name=routing_number').val(bank_info.routing_number);

                } else {
                    form.find('.bank-information').addClass('d-none');
                }
                form.find('.instruction').summernote('code', details.instruction);

                $("#edit-payment-method-modal").modal('show');
            });

            /**
             * Will update payment details
             * 
             **/
            $('.update-payment-method').on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').html("");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#edit-payment-method-form').serialize(),
                    url: '{{ route('plugin.wallet.recharge.offline.payment.methods.update') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(
                                '{{ translate('Payment method updated successfully') }}');
                            location.reload();
                        } else {
                            toastr.error('{{ translate('Update Failed ') }}');
                        }
                    },
                    error: function(response) {
                        if (response.status == 422) {
                            let form = $(document).find("#edit-payment-method-form");
                            $.each(response.responseJSON.errors, function(field_name, error) {
                                form.find('[name=' + field_name + ']').closest(
                                    '.theme-input-style').after(
                                    '<div class="d-flex invalid-input justify-content-end">' +
                                    error + '</div>')
                            })
                        } else {
                            toastr.error('{{ translate('Update Failed ') }}');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
