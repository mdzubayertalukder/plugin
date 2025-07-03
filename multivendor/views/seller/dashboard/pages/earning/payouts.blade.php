@extends('plugin/multivendor::seller.dashboard.layouts.seller_master')
@section('title')
    {{ translate('Payouts') }}
@endsection
@section('custom_css')
@endsection

@section('seller_main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Payouts') }}</h4>
                    </div>
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
                                <th>{{ translate('Amount') }}</th>
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
