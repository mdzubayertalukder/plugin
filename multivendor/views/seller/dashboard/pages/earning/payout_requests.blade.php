@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Payout Request') }}
@endsection
@section('custom_css')
@endsection

@section('seller_main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Payout Requests') }}</h4>
                        <div class="d-flex flex-wrap">
                            <button class="btn long" data-toggle="modal"
                                data-target="#payout-request-create-modal">{{ translate('Send Payout Request') }}</button>
                        </div>
                    </div>
                </div>

                <div class="px-2 filter-area d-flex align-items-center">
                    <form method="get"
                        action="{{ route('plugin.multivendor.seller.dashboard.earning.payout.requests') }}">
                        <select class="theme-input-style mb-10" name="status">
                            <option value="">{{ translate('Status') }}</option>
                            <option value="{{ config('multivendor.payout_request_status.pending') }}"
                                @selected(request()->has('status') && request()->get('status') == config('multivendor.payout_request_status.pending'))>
                                {{ translate('Pending') }}
                            </option>
                            <option value="{{ config('multivendor.payout_request_status.accepted') }}"
                                @selected(request()->has('status') && request()->get('status') == config('multivendor.payout_request_status.accepted'))>
                                {{ translate('Accepted') }}
                            </option>
                            <option value="{{ config('multivendor.payout_request_status.cancelled') }}"
                                @selected(request()->has('status') && request()->get('status') == config('multivendor.payout_request_status.cancelled'))>
                                {{ translate('Cancelled') }}
                            </option>
                        </select>
                        <button type="submit" class="btn long mb-1">{{ translate('Filter') }}</button>
                    </form>
                    <a class="btn btn-danger long mb-auto"
                        href="{{ route('plugin.multivendor.seller.dashboard.earning.payout.requests') }}">{{ translate('Clear Filter') }}</a>

                </div>

                <div class="table-responsive">
                    <table class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Mesage') }}</th>
                                <th>{{ translate('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($payout_requests->count() > 0)
                                @foreach ($payout_requests as $key => $request)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>{!! currencyExchange($request->amount) !!} </td>
                                        <td class="text-wrap">{{ $request->message }}</td>
                                        <td>
                                            @if ($request->status == config('multivendor.payout_request_status.accepted'))
                                                <p class="badge badge-success">{{ translate('Accepted') }}</p>
                                            @endif
                                            @if ($request->status == config('multivendor.payout_request_status.pending'))
                                                <p class="badge badge-info">{{ translate('pending') }}</p>
                                            @endif
                                            @if ($request->status == config('multivendor.payout_request_status.cancelled'))
                                                <p class="badge badge-danger">{{ translate('cancelled') }}</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $payout_requests->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--Payout request create modal-->
    <div id="payout-request-create-modal" class="payout-request-create-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Send Payout Request') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <div class="col-12">
                            <div class="project-box three mb-30">
                                <div class="d-flex justify-content-center align-items-center p-3 gap-20 flex-column">
                                    <div class="title">
                                        <h4>{{ translate('Avaiable Balance') }}</h4>
                                    </div>
                                    <div class="amount">
                                        <h3>{!! currencyExchange(
                                            auth()->user()->sellerWithdrawableBalance(),
                                        ) !!}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            @if (auth()->user()->sellerWithdrawableBalance() > 0)
                                <form id="payout-request-form">
                                    <div class="form-row mb-20">
                                        <div class="col-sm-3">
                                            <label class="font-14 bold black">{{ translate('Amount') }} </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="number" class="theme-input-style" name="amount"
                                                placeholder="{{ translate('Enter Amount') }}">
                                        </div>
                                    </div>

                                    <div class="form-row mb-20">
                                        <div class="col-sm-3">
                                            <label class="font-14 bold black">{{ translate('Message') }} </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea name="message" class="theme-input-style"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-12 text-right">
                                            <button
                                                class="btn long payout-request-submit-btn">{{ translate('Send Request') }}</button>
                                        </div>
                                    </div>

                                </form>
                            @else
                                <p class="alert alert-info">
                                    {{ translate('You do not have sufficient balance to make payout request') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Payout request create modal-->
@endsection

@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            /**
             * Send payout request
             * 
             **/
            $(".payout-request-submit-btn").on('click', function(e) {
                e.preventDefault();
                $(document).find('.invalid-input').remove();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: $('#payout-request-form').serialize(),
                    url: '{{ route('plugin.multivendor.seller.dashboard.earning.payout.requests.send') }}',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ translate('Payout request send successfully') }}');
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
                            toastr.error('{{ translate('Payout request sending failed') }}');
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
