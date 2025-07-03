@extends('core::base.layouts.master')
@section('title')
    {{ translate('Wallet Transactions') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Wallet Transactions') }}</h4>
                    </div>
                </div>

                <div class="px-2 filter-area d-flex align-items-center">
                    <!--Bulk actions-->
                    <select class="theme-input-style mb-10" id="bulkActionSelector">
                        <option value="">
                            {{ translate('Bulk Action') }}
                        </option>
                        <option value="{{ config('tlecommercecore.wallet_transaction_status.pending') }}">
                            {{ translate('Change status to pending') }}
                        </option>
                        <option value="{{ config('tlecommercecore.wallet_transaction_status.accept') }}">
                            {{ translate('Change status to accept') }}
                        </option>
                        <option value="{{ config('tlecommercecore.wallet_transaction_status.declined') }}">
                            {{ translate('Change status to decline') }}
                        </option>

                        </option>
                    </select>
                    <button class="btn btn-warning fire-bulk-action long mb-10"
                        href="{{ route('plugin.tlcommercecore.orders.inhouse') }}" type="submit">{{ translate('Apply') }}
                    </button>
                    <!--End bulk actions-->
                    <form method="get" action="{{ route('plugin.wallet.transaction.list') }}">
                        <select class="theme-input-style mb-10" name="transaction_type">
                            <option value="">{{ translate('Transaction Type') }}</option>

                            <option value="{{ config('tlecommercecore.wallet_entry_type.credit') }}"
                                @selected(request()->has('transaction_type') && request()->get('transaction_type') == config('tlecommercecore.wallet_entry_type.credit'))>
                                {{ translate('Credited') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_entry_type.debit') }}"
                                @selected(request()->has('transaction_type') && request()->get('transaction_type') == config('tlecommercecore.wallet_entry_type.debit'))>
                                {{ translate('Debited') }}
                            </option>
                        </select>

                        <select class="theme-input-style mb-10" name="payment_option">
                            <option value="">{{ translate('Payment options') }}</option>

                            <option value="{{ config('tlecommercecore.wallet_recharge_type.online') }}"
                                @selected(request()->has('payment_option') && request()->get('payment_option') == config('tlecommercecore.wallet_recharge_type.online'))>
                                {{ translate('Online') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_recharge_type.offline') }}"
                                @selected(request()->has('payment_option') && request()->get('payment_option') == config('tlecommercecore.wallet_recharge_type.offline'))>
                                {{ translate('Offline') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_recharge_type.manual') }}"
                                @selected(request()->has('payment_option') && request()->get('payment_option') == config('tlecommercecore.wallet_recharge_type.manual'))>
                                {{ translate('Manual') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_recharge_type.cart') }}"
                                @selected(request()->has('payment_option') && request()->get('payment_option') == config('tlecommercecore.wallet_recharge_type.cart'))>
                                {{ translate('Cart') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_recharge_type.cashback') }}"
                                @selected(request()->has('payment_option') && request()->get('payment_option') == config('tlecommercecore.wallet_recharge_type.cashback'))>
                                {{ translate('Cashback') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_recharge_type.refund') }}"
                                @selected(request()->has('payment_option') && request()->get('payment_option') == config('tlecommercecore.wallet_recharge_type.refund'))>
                                {{ translate('Refund') }}
                            </option>

                        </select>

                        <select class="theme-input-style mb-10" name="status">
                            <option value="">{{ translate('Status') }}</option>
                            <option value="{{ config('tlecommercecore.wallet_transaction_status.pending') }}"
                                @selected(request()->has('status') && request()->get('status') == config('tlecommercecore.wallet_transaction_status.pending'))>
                                {{ translate('Pending') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_transaction_status.accept') }}"
                                @selected(request()->has('status') && request()->get('status') == config('tlecommercecore.wallet_transaction_status.accept'))>
                                {{ translate('Accept') }}
                            </option>
                            <option value="{{ config('tlecommercecore.wallet_transaction_status.declined') }}"
                                @selected(request()->has('status') && request()->get('status') == config('tlecommercecore.wallet_transaction_status.declined'))>
                                {{ translate('Declined') }}
                            </option>
                        </select>

                        <input type="text" name="search" class="theme-input-style mb-10"
                            value="{{ request()->has('search') ? request()->get('search') : '' }}"
                            placeholder="Tranaction id or customer name">
                        <button type="submit" class="btn long mb-1">{{ translate('Filter') }}</button>
                    </form>
                    <a class="btn btn-danger long mb-2"
                        href="{{ route('plugin.wallet.transaction.list') }}">{{ translate('Clear Filter') }}</a>

                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Customer') }}</th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Transaction Type') }}</th>
                                <th>{{ translate('Payment Options') }}</th>
                                <th>{{ translate('Tranaction Id') }}</th>
                                <th>{{ translate('Executed by') }}</th>
                                <th>{{ translate('Document') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th class="text-right">{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (collect($recharges)->count() > 0)
                                @foreach ($recharges as $key => $transaction)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="position-relative mr-2">
                                                    <input type="checkbox" name="items[]" class="item-id"
                                                        value="{{ $transaction->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $transaction->customer->id]) }}">
                                                {{ $transaction->customer->name }}
                                            </a>
                                        </td>
                                        <td>{{ $transaction->created_at->format('d M y') }}</td>
                                        <td>{!! currencyExchange($transaction->recharge_amount) !!}</td>
                                        <td>
                                            @if ($transaction->entry_type == config('tlecommercecore.wallet_entry_type.credit'))
                                                <p class="badge badge-success">{{ translate('Credited') }}</p>
                                            @else
                                                <p class="badge badge-danger">{{ translate('Debited') }}</p>
                                            @endif
                                        </td>

                                        <td>
                                            <p class="text-capitalize"> {{ $transaction->payment_method }}</p>
                                        </td>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>
                                            @if ($transaction->modifier != null)
                                                {{ $transaction->modifier->name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->document != null)
                                                <a href="{{ getFilePath($transaction->document) }}"target="_blank">
                                                    {{ translate('View') }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->status == config('tlecommercecore.wallet_transaction_status.accept'))
                                                <p class="badge badge-success">{{ translate('Accepted') }}</p>
                                            @elseif($transaction->status == config('tlecommercecore.wallet_transaction_status.declined'))
                                                <p class="badge badge-danger">{{ translate('Declined') }}</p>
                                            @else
                                                <p class="badge badge-info">{{ translate('Pending') }}</p>
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
                                                    <a href="#" class="action-btn" data-id="{{ $transaction->id }}"
                                                        data-action="{{ config('tlecommercecore.wallet_transaction_status.pending') }}">
                                                        {{ translate('Pending') }}
                                                    </a>
                                                    <a href="#" data-id="{{ $transaction->id }}" class="action-btn"
                                                        data-action="{{ config('tlecommercecore.wallet_transaction_status.accept') }}">
                                                        {{ translate('Accept') }}
                                                    </a>
                                                    <a href="#" class="action-btn" data-id="{{ $transaction->id }}"
                                                        data-action="{{ config('tlecommercecore.wallet_transaction_status.declined') }}">
                                                        {{ translate('Declined') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                <tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $recharges->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            /**
             * 
             * Select all Items for bulk action
             **/
            $('.select-all').click('change', function(e) {
                if ($('.select-all').is(":checked")) {
                    $(".item-id").prop("checked", true);
                } else {
                    $(".item-id").prop("checked", false);
                }
            })
            /**
             * Bulk actions
             * 
             **/
            $('.fire-bulk-action').on('click', function(e) {
                let action = $("#bulkActionSelector").val();
                if (action != "") {
                    let selected_items = [];
                    $('input[name^="items"]:checked').each(function() {
                        selected_items.push($(this).val());
                    });
                    let data = {
                        'action': action,
                        'selected_items': selected_items
                    }
                    if (selected_items.length > 0) {
                        $.post('{{ route('plugin.wallet.bulk.action') }}', {
                            _token: '{{ csrf_token() }}',
                            data: data
                        }, function(data) {
                            if (data.success) {
                                toastr.success('{{ translate('Action applied successfully') }}');
                                location.reload();
                            } else {
                                toastr.error('{{ translate('Semething wrong') }}');
                            }
                        })
                    } else {
                        toastr.error('{{ translate('No Item Selected') }}', "Error!");
                    }
                } else {
                    toastr.error('{{ translate('No Action Selected') }}', "Error!");
                }

            });
            /**
             * Change status
             * 
             **/
            $(".action-btn").on("click", function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let action = $(this).data('action');
                let data = {
                    'action': action,
                    'selected_items': [id]
                }
                $.post('{{ route('plugin.wallet.bulk.action') }}', {
                    _token: '{{ csrf_token() }}',
                    data: data
                }, function(data) {
                    if (data.success) {
                        toastr.success('{{ translate('Action applied successfully') }}');
                        location.reload();
                    } else {
                        toastr.error('{{ translate('Semething wrong') }}');
                    }
                })
            });
        })(jQuery);
    </script>
@endsection
