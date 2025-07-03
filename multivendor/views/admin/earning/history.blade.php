@extends('core::base.layouts.master')
@section('title')
    {{ translate('Seller Earnings') }}
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
                        <h4 class="font-20">{{ translate('Seller Earnings') }}</h4>
                    </div>
                </div>

                <div class="px-2 filter-area d-flex align-items-center">
                    <form method="get" action="{{ route('plugin.multivendor.admin.seller.earning.list') }}">
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
                        href="{{ route('plugin.multivendor.admin.seller.earning.list') }}">{{ translate('Clear Filter') }}</a>

                </div>

                <div class="table-responsive">
                    <table class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>{{ translate('Seller') }}</th>
                                <th>{{ translate('Order Code') }}</th>
                                <th>{{ translate('Admin Commission') }}</th>
                                <th>{{ translate('Seller Earning') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Added Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($earnings->count() > 0)
                                @foreach ($earnings as $key => $earning)
                                    <tr>
                                        <td>
                                            @if ($earning->seller != null)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar img-50 mr-10">
                                                        <a
                                                            href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $earning->seller->id]) }}">
                                                            <img src="{{ asset(getFilePath($earning->seller->image)) }}"
                                                                alt="{{ $earning->seller->name }}"
                                                                class="img-fluid radius-50 w-100">
                                                        </a>
                                                    </div>
                                                    <div class="info">
                                                        <h4 class="name"><a
                                                                href="{{ route('plugin.multivendor.admin.seller.details', ['id' => $earning->seller->id]) }}">{{ $earning->seller->name }}</a>
                                                        </h4>
                                                        <p class="email mb-0">{{ translate('Email') }}:
                                                            {{ $earning->seller->email }}
                                                        </p>
                                                        <p class="phone mb-0">{{ translate('Balance') }}:
                                                            {!! currencyExchange($earning->seller->sellerWithdrawableBalance()) !!}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $earning->order->id]) }}">
                                                {{ $earning->order->order_code }}
                                            </a>
                                        </td>
                                        <td>{!! currencyExchange($earning->admin_commission) !!} </td>
                                        <td>{!! currencyExchange($earning->earning) !!} </td>
                                        <td>
                                            @if ($earning->status == config('tlecommercecore.seller_earning_status.approve'))
                                                <p class="badge badge-success">{{ translate('Approved') }}</p>
                                            @endif

                                            @if ($earning->status == config('tlecommercecore.seller_earning_status.pending'))
                                                <p class="badge badge-warning">{{ translate('Pending') }}</p>
                                            @endif

                                            @if ($earning->status == config('tlecommercecore.seller_earning_status.refunded'))
                                                <p class="badge badge-danger">{{ translate('Refunded') }}</p>
                                            @endif
                                        </td>

                                        <td>{{ $earning->created_at->format('d M Y') }}</td>
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
                        {!! $earnings->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
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
                placeholder: '{{ translate('Filter By Seller') }}',
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
