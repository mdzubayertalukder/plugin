@extends('core::base.layouts.master')
@section('title')
    {{ translate('Refund Requests') }}
@endsection
@section('custom_css')
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Refund Requests') }}</h4>
                    </div>
                </div>

                <div class="px-2 filter-area d-flex align-items-center">
                    <form method="get" action="{{ route('plugin.refund.requests') }}">
                        <select class="theme-input-style mb-10" name="payment_status">
                            <option value="">{{ translate('Payment Status') }}</option>

                            <option value="{{ config('tlecommercecore.return_request_payment_status.pending') }}"
                                @selected(request()->has('payment_status') && request()->get('payment_status') == config('tlecommercecore.return_request_payment_status.pending'))>
                                {{ translate('Pending') }}
                            </option>
                            <option value="{{ config('tlecommercecore.return_request_payment_status.refunded') }}"
                                @selected(request()->has('payment_status') && request()->get('payment_status') == config('tlecommercecore.return_request_payment_status.refunded'))>
                                {{ translate('Refunded') }}
                            </option>


                        </select>

                        <select class="theme-input-style mb-10" name="return_status">
                            <option value="">{{ translate('Return Status') }}</option>
                            <option value="{{ config('tlecommercecore.return_request_status.pending') }}"
                                @selected(request()->has('return_status') && request()->get('return_status') == config('tlecommercecore.return_request_status.pending'))>
                                {{ translate('Pending') }}
                            </option>
                            <option value="{{ config('tlecommercecore.return_request_status.processing') }}"
                                @selected(request()->has('return_status') && request()->get('return_status') == config('tlecommercecore.return_request_status.processing'))>
                                {{ translate('Processing') }}
                            </option>
                            <option value="{{ config('tlecommercecore.return_request_status.product_received') }}"
                                @selected(request()->has('return_status') && request()->get('return_status') == config('tlecommercecore.return_request_status.product_received'))>
                                {{ translate('Product Received') }}
                            </option>
                            <option value="{{ config('tlecommercecore.return_request_status.approved') }}"
                                @selected(request()->has('return_status') && request()->get('return_status') == config('tlecommercecore.return_request_status.approved'))>
                                {{ translate('Approved') }}
                            </option>
                            <option value="{{ config('tlecommercecore.return_request_status.cancelled') }}"
                                @selected(request()->has('return_status') && request()->get('return_status') == config('tlecommercecore.return_request_status.cancelled'))>
                                {{ translate('Cancelled') }}
                            </option>
                        </select>

                        <input type="text" name="search" class="theme-input-style mb-10"
                            value="{{ request()->has('search') ? request()->get('search') : '' }}"
                            placeholder="Order code">
                        <button type="submit" class="btn long mb-1">{{ translate('Filter') }}</button>
                    </form>
                    <a class="btn btn-danger long mb-auto"
                        href="{{ route('plugin.refund.requests') }}">{{ translate('Clear Filter') }}</a>

                </div>

                <div class="table-responsive">
                    <table class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>{{ translate('Refund Code') }}</th>
                                <th>{{ translate('Order Code') }}</th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Customer') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Payment Status') }}</th>
                                <th>{{ translate('Quick Action') }}</th>
                                <th class="text-right">{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($refund_request_list->count() > 0)
                                @foreach ($refund_request_list as $key => $request)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            <a href="{{ route('plugin.refund.request.details', ['id' => $request->id]) }}">
                                                {{ $request->code }}

                                            </a>
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $request->order_id]) }}">
                                                {{ $request->order_code }}

                                            </a>
                                        </td>
                                        <td>{{ $request['created_at']->format('d M Y') }}</td>
                                        <td>
                                            <a
                                                href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $request->customer_id]) }}">
                                                {{ $request->customer_name }}
                                            </a>
                                        </td>
                                        <td>{!! currencyExchange($request->total_amount) !!} </td>
                                        <td>
                                            @if ($request->return_status == config('tlecommercecore.return_request_status.approved'))
                                                <p class="badge badge-success">{{ translate('approved') }}</p>
                                            @elseif ($request->return_status == config('tlecommercecore.return_request_status.processing'))
                                                <p class="badge badge-primary">{{ translate('Processing') }}</p>
                                            @elseif ($request->return_status == config('tlecommercecore.return_request_status.cancelled'))
                                                <p class="badge badge-danger">{{ translate('Cancelled') }}</p>
                                            @elseif ($request->return_status == config('tlecommercecore.return_request_status.product_received'))
                                                <p class="badge badge-dark">{{ translate('Product Received') }}</p>
                                            @else
                                                <p class="badge badge-info">{{ translate('Pending') }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($request->payment_status == config('tlecommercecore.return_request_payment_status.refunded'))
                                                <p class="badge badge-success">{{ translate('refunded') }}</p>
                                            @else
                                                <p class="badge badge-danger">{{ translate('Pending') }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn-success quick-view" data-id="{{ $request->id }}"
                                                data-action="quick-view" title="View Details"><i class="icofont-eye"></i>
                                            </button>
                                            <button class="btn-info quick-view" data-id="{{ $request->id }}"
                                                data-action="status-update" title="Update Request Status">
                                                <i class="icofont-ui-edit"></i>
                                            </button>
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
                                                    <a href="{{ route('plugin.refund.request.details', ['id' => $request->id]) }}"
                                                        class="request-details">{{ translate('Details') }}
                                                    </a>
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
                        {!! $refund_request_list->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--Quick view modal-->
    <div id="quick-view-modal" class="quick-view-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 bold">{{ translate('Refund Request Information') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body quick-view-content">

                </div>
            </div>
        </div>
    </div>
    <!--End quick view modal-->
@endsection
@section('custom_scripts')
    <script>
        (function($) {
            "use strict";
            /**
             * Quick view
             * 
             **/
            $(".quick-view").on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let action = $(this).data('action');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        id: id,
                        action: action
                    },
                    url: '{{ route('plugin.refund.request.quick.view') }}',
                    success: function(response) {
                        $(".quick-view-content").html(response);
                        $("#quick-view-modal").modal('show');
                    },
                    error: function(response) {
                        toastr.error('{{ translate('Details not found') }}');
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
