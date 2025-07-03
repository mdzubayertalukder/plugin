<div class="row">
    <div class="col-12">
        <form id="payout-request-status-update-form">
            <div class="form-row mb-20">
                <table class="table table-bordered">
                    <tr>
                        <td>{{ translate('Seller') }}</td>
                        <td>{{ $request_details->seller != null ? $request_details->seller->name : '' }}</td>
                    </tr>
                    <tr>
                        <td>{{ translate('Avaiable Balance') }}</td>
                        <td>
                            @if ($request_details->seller != null)
                                {!! currencyExchange($request_details->seller->sellerWithdrawableBalance()) !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ translate('Request Amount') }}</td>
                        <td>
                            @if ($request_details->seller != null)
                                {!! currencyExchange($request_details->amount) !!}
                            @endif
                        </td>
                    </tr>
                    @if ($request_details->seller != null && $request_details->seller->sellerPayoutInfo != null)
                        <tr>
                            <td>{{ translate('Bank') }}</td>
                            <td>{{ $request_details->seller->sellerPayoutInfo->bank_name }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('Bank Code') }}</td>
                            <td>{{ $request_details->seller->sellerPayoutInfo->bank_code }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('Account Name') }}</td>
                            <td>{{ $request_details->seller->sellerPayoutInfo->account_name }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('Account Number') }}</td>
                            <td>{{ $request_details->seller->sellerPayoutInfo->account_number }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('Account Holder Name') }}</td>
                            <td>{{ $request_details->seller->sellerPayoutInfo->account_holder_name }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('Bank Routing Number') }}</td>
                            <td>{{ $request_details->seller->sellerPayoutInfo->bank_routing_number }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="form-row mb-20">
                <div class="col-sm-3">
                    <label class="font-14 bold black">{{ translate('Amount') }} </label>
                </div>
                <div class="col-sm-9">
                    <input type="hidden" name="id" value="{{ $request_details->id }}">
                    <input type="text" class="theme-input-style" name="amount"
                        placeholder="{{ translate('Enter Amount') }}" value="{{ $request_details->amount }}" readonly>
                </div>
            </div>
            <div class="form-row mb-20">
                <div class="col-sm-3">
                    <label class="font-14 bold black">{{ translate('Status') }} </label>
                </div>
                <div class="col-sm-9">
                    <select class="theme-input-style mb-10 request_status" name="status">
                        <option value="{{ config('multivendor.payout_request_status.pending') }}"
                            @selected($request_details->status == config('multivendor.payout_request_status.pending'))>
                            {{ translate('Pending') }}
                        </option>
                        <option value="{{ config('multivendor.payout_request_status.accepted') }}"
                            @selected($request_details->status == config('multivendor.payout_request_status.accepted'))>
                            {{ translate('Accepted') }}
                        </option>
                        <option value="{{ config('multivendor.payout_request_status.cancelled') }}"
                            @selected($request_details->status == config('multivendor.payout_request_status.cancelled'))>
                            {{ translate('Cancelled') }}
                        </option>
                    </select>
                </div>
            </div>
            <div
                class="payment-info {{ $request_details->status == config('multivendor.payout_request_status.accepted') ? '' : 'd-none' }}">
                <div class="form-row mb-20">
                    <div class="col-sm-3">
                        <label class="font-14 bold black">{{ translate('Payment Method') }} </label>
                    </div>
                    <div class="col-sm-9">
                        <select class="theme-input-style mb-10" name="payment_method">
                            <option value="{{ config('multivendor.seller_payment_methods.bank_transfer') }}"
                                @selected(request()->has('status') && request()->get('status') == config('multivendor.seller_payment_methods.bank_transfer'))>
                                {{ translate('Bank Transfer') }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-row mb-20">
                    <div class="col-sm-3">
                        <label class="font-14 bold black">{{ translate('Transaction Number') }} </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="theme-input-style" name="transaction_number"
                            placeholder="{{ translate('Enter Transaction Number') }}">
                    </div>
                </div>
                <div class="form-row mb-20">
                    <div class="col-sm-3">
                        <label class="font-14 bold black">{{ translate('Description') }} </label>
                    </div>
                    <div class="col-sm-9">
                        <textarea name="payment_description" class="theme-input-style"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-row d-none mb-20 cancelled-notification">
                <div class="col-md-12">
                    <p class="alert alert-danger text-center">
                        {{ translate('Once canceled the payout request further you can not update the status') }}
                    </p>
                </div>
            </div>

            @if ($request_details->status != config('multivendor.payout_request_status.cancelled'))
                <div class="form-row mb-20">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn long update-payment-request-status">
                            {{ translate('Save Change') }}
                        </button>
                    </div>
                </div>
            @else
                <div class="form-row mb-20">
                    <div class="col-md-12">
                        <p class="alert alert-danger text-center">
                            {{ translate('This payout request is cancelled. You can not change the status') }}
                        </p>
                    </div>
                </div>
            @endif

        </form>
    </div>
</div>
<script>
    (function($) {
        "use strict";
        //Select status
        $('.request_status').on('change', function(e) {
            e.preventDefault();
            let status = $(this).val();
            if (status == {{ config('multivendor.payout_request_status.accepted') }}) {
                $('.payment-info').removeClass('d-none');
            } else {
                $('.payment-info').addClass('d-none');
            }

            if (status == {{ config('multivendor.payout_request_status.cancelled') }}) {
                $('.cancelled-notification').removeClass('d-none');
            } else {
                $('.cancelled-notification').addClass('d-none');
            }
        });
        //Update payment request status
        $('.update-payment-request-status').on('click', function(e) {
            e.preventDefault();
            $(document).find('.invalid-input').remove();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                type: "POST",
                data: $('#payout-request-status-update-form').serialize(),
                url: '{{ route('plugin.multivendor.admin.seller.payout.requests.status.update') }}',
                success: function(response) {
                    if (response.success) {
                        toastr.success(
                            '{{ translate('Payout request status updated successfully') }}'
                        );
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    if (response.status == 422) {
                        $.each(response.responseJSON.errors, function(field_name, error) {
                            $(document).find('[name=' + field_name + ']').closest(
                                '.theme-input-style').after(
                                '<div class="invalid-input d-flex">' + error +
                                '</div>')
                        })
                    } else {
                        toastr.error('{{ translate('Payout status upadte failed') }}');
                    }
                }
            });
        });
    })(jQuery);
</script>
