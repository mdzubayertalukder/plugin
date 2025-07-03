@extends('core::base.layouts.master')
@section('title')
    {{ translate('Payouts') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/select2/select2.min.css') }}">
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Payouts') }}</h4>
                    </div>
                </div>

                <div class="px-2 filter-area d-flex align-items-center">
                    <form method="get" action="{{ route('plugin.multivendor.admin.seller.payouts.list') }}">
                        <select class="select_seller form-control col-12" name="seller">
                            @if (request()->has('seller') && request()->get('seller') != null)
                                @php
                                    $seller = \Core\Models\User::where('id', request()->get('seller'))->first();
                                @endphp
                                @endphp
                                @if ($seller != null)
                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                @endif
                            @endif
                        </select>
                        <button type="submit" class="btn long mb-1">{{ translate('Filter') }}</button>
                    </form>
                    <a class="btn btn-danger long mb-auto"
                        href="{{ route('plugin.multivendor.admin.seller.payouts.list') }}">{{ translate('Clear Filter') }}</a>

                </div>

                <div class="table-responsive">
                    <table class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>{{ translate('Request Date') }}</th>
                                <th>{{ translate('Payment Date') }}</th>
                                <th>{{ translate('Seller') }}</th>
                                <th>{{ translate('Paid Amount') }}</th>
                                <th>{{ translate('Paid By') }}</th>
                                <th>{{ translate('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($payouts->count() > 0)
                                @foreach ($payouts as $key => $request)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>{{ $request->payment_date->format('d M Y') }}</td>
                                        <td>
                                            @if ($request->seller != null)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar img-50 mr-10">
                                                        <a
                                                            href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $request->seller->id]) }}">
                                                            <img src="{{ asset(getFilePath($request->seller->image)) }}"
                                                                alt="{{ $request->seller->name }}"
                                                                class="img-fluid radius-50 w-100">
                                                        </a>
                                                    </div>
                                                    <div class="info">
                                                        <h4 class="name"><a
                                                                href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $request->seller->id]) }}">{{ $request->seller->name }}</a>
                                                        </h4>
                                                        <p class="email mb-0">{{ translate('Email') }}:
                                                            {{ $request->seller->email }}
                                                        </p>
                                                        <p class="phone mb-0">{{ translate('Balance') }}:
                                                            {!! currencyExchange($request->seller->sellerWithdrawableBalance()) !!}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{!! currencyExchange($request->amount) !!} </td>
                                        <td class="text-wrap">
                                            @if ($request->payment_method == config('multivendor.seller_payment_methods.bank_transfer'))
                                                {{ translate('Bank Payment') }}
                                                [{{ translate('TRX ID : ') }} {{ $request->transaction_number }}]
                                            @endif
                                        </td>
                                        <td class="text-wrap">{{ $request->description }}</td>
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
                        {!! $payouts->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            /**
             *  Select seller
             * 
             */
            $('.select_seller').select2({
                theme: "classic",
                placeholder: '{{ translate('Seller') }}',
                ajax: {
                    url: '{{ route('plugin.multivendor.admin.seller.dropdown.list') }}',
                    dataType: 'json',
                    method: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
        })(jQuery);
    </script>
@endsection
