@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Earnings') }}
@endsection
@section('custom_css')
@endsection

@section('seller_main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Earnings') }}</h4>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>{{ translate('Order Code') }}</th>
                                <th>{{ translate('Admin Commission') }}</th>
                                <th>{{ translate('Your Earning') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Added Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($earnings->count() > 0)
                                @foreach ($earnings as $key => $earning)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ route('plugin.multivendor.seller.dashboard.order.details', ['id' => $earning->order->id]) }}">
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
