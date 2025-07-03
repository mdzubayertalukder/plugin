@extends('core::base.layouts.master')
@section('title')
    {{ translate('Transaction history') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/daterangepicker/daterangepicker.css') }}">
    <style>
        .info {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Transaction history') }}</h4>
                    </div>
                </div>
                <div class="px-2 filter-area d-flex align-items-center">
                    <form method="get" action="{{ route('plugin.tlcommercecore.payments.transactions.history') }}">
                        <select class="theme-input-style mb-10" name="payment_method">
                            <option value="">{{ translate('Payment Method') }}</option>
                            @foreach ($payment_methods as $method)
                                <option value="{{ $method['name'] }}" @selected(request()->has('payment_method') && request()->get('payment_method') == $method['name'])>
                                    {{ $method['name'] }}
                                </option>
                            @endforeach
                        </select>

                        <input type="text" name="search" class="theme-input-style mb-10"
                            value="{{ request()->has('search') ? request()->get('search') : '' }}"
                            placeholder="Customer name">
                        <input type="text" class="theme-input-style" id="transactionDateRange"
                            placeholder="Filter by date" name="transaction_date" readonly>

                        <button type="submit" class="btn long mb-1 mt-2 mt-sm-0">{{ translate('Filter') }}</button>
                    </form>
                    <a class="btn btn-danger long mb-auto"
                        href="{{ route('plugin.tlcommercecore.payments.transactions.history') }}">{{ translate('Clear Filter') }}</a>

                </div>
                <div class="table-responsive">
                    <table class="hoverable">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Payment Method') }}</th>
                                <th>{{ translate('Payment For') }}</th>
                                <th>{{ translate('Customer') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Info') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($transactions->count() > 0)
                                @foreach ($transactions as $key => $transaction)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>{{ $transaction->created_at }}</td>
                                        <td>{{ $transaction->payment_method }}</td>
                                        <td>{{ $transaction->payment_for }}</td>
                                        <td>
                                            @if ($transaction->customer_info != null)
                                                <a
                                                    href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $transaction->customer_info->id]) }}">{{ $transaction->customer_info->name }}</a>
                                            @else
                                                @if ($transaction->guest_customer_info != null)
                                                    <p class="text-capitalize">
                                                        {{ $transaction->guest_customer_info->name }}<span
                                                            class="ml-1 badge badge-info">Guest</span>
                                                    </p>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {!! currencyExchange($transaction->paid_amount) !!}
                                        </td>
                                        <td>
                                            <p class="info cursor-pointer" data-info="{{ $transaction->payment_info }}">
                                                {{ $transaction->payment_info }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $transactions->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--view details modal-->
    <div id="details-modal" class="details-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Payment Details') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="content"></div>
                </div>
            </div>
        </div>
    </div>
    <!--End view details modal-->
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/public/backend/assets/plugins/daterangepicker/daterangepicker.js') }}">
    </script>
    <script>
        (function($) {
            "use strict";
            // Filter date range
            function cb(start, end) {

                let initVal = '{{ request()->has('transaction_date') ? request()->get('transaction_date') : '' }}';
                $('#transactionDateRange').val(initVal);
            }

            var start = moment().subtract(0, 'days');
            var end = moment();

            $('#transactionDateRange').on('apply.daterangepicker', function(ev, picker) {
                let val = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
                    'YYYY-MM-DD')
                $('#transactionDateRange').val(val);
            });
            $('#transactionDateRange').daterangepicker({
                startDate: start,
                endDate: end,
                showCustomRangeLabel: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
            /**
             * View payment info
             * 
             */
            $(".info").on('click', function(e) {
                let data = $(this).data('info');
                $("#content").html('<p>' + data + '</p>');
                $("#details-modal").modal('show');
            });
        })(jQuery);
    </script>
@endsection
