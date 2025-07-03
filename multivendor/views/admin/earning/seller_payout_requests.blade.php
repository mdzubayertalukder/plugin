@extends('core::base.layouts.master')
@section('title')
    {{ translate('Payout Requests') }}
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
                        <h4 class="font-20">{{ translate('Payout Requests') }}</h4>
                    </div>
                </div>

                <div class="px-2 filter-area d-flex align-items-center">
                    <form method="get" action="{{ route('plugin.multivendor.admin.seller.payout.requests.list') }}">
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
                        href="{{ route('plugin.multivendor.admin.seller.payout.requests.list') }}">{{ translate('Clear Filter') }}</a>

                </div>

                <div class="table-responsive">
                    <table class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Seller') }}</th>
                                <th>{{ translate('Requested Amount') }}</th>
                                <th>{{ translate('Mesage') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Action') }}</th>
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
                                                    <a href="#" class="request-details"
                                                        data-request="{{ $request->id }}">{{ translate('Update Status') }}</a>
                                                </div>
                                            </div>
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
    <!--Request details Modal-->
    <div id="request-details-modal" class="request-details-modal modal fade show" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Payout Request Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-content-html">

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--End Request details Modal-->
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            /**
             * Request Details
             * 
             **/
            $('.request-details').on('click', function(e) {
                e.preventDefault();
                $(".modal-content-html").html('');
                let id = $(this).data('request');
                $.post('{{ route('plugin.multivendor.admin.seller.payout.requests.details') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id
                }, function(data) {
                    $(".modal-content-html").html(data.data);
                    $("#request-details-modal").modal('show');
                })
            });
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
