@extends('core::base.layouts.master')
@section('title')
    {{ translate('Pickup point Orders') }}
@endsection
@section('custom_css')
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/daterangepicker/daterangepicker.css') }}">
@endsection
@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-30">
                <div class="card-body border-bottom2 mb-20">
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Pickup Point Orders') }}</h4>
                    </div>
                </div>
                <!--Filter counter button-->
                <div class="px-2 filter-area d-flex align-items-center mb-20">
                    <a href="{{ route('plugin.pickuppoint.orders') }}" class="btn sm btn-info">{{ translate('All') }}
                        ({{ $order_counter != null ? $order_counter['all'] : 0 }})
                    </a>
                    <a href="{{ route('plugin.pickuppoint.orders', ['delivery_status' => config('tlecommercecore.order_delivery_status.pending')]) }}"
                        class="btn sm btn-primary">{{ translate('Pending') }}
                        ({{ $order_counter != null ? $order_counter['pending'] : 0 }})
                    </a>
                    <a href="{{ route('plugin.pickuppoint.orders', ['delivery_status' => config('tlecommercecore.order_delivery_status.processing')]) }}"
                        class="btn sm btn-info">{{ translate('Processing') }}
                        ({{ $order_counter != null ? $order_counter['processing'] : 0 }})
                    </a>
                    <a href="{{ route('plugin.pickuppoint.orders', ['delivery_status' => config('tlecommercecore.order_delivery_status.shipped')]) }}"
                        class="btn sm">{{ translate('To Shipped') }}
                        ({{ $order_counter != null ? $order_counter['shipped'] : 0 }})
                    </a>
                    <a href="{{ route('plugin.pickuppoint.orders', ['delivery_status' => config('tlecommercecore.order_delivery_status.cancelled')]) }}"
                        class="btn sm btn-danger">{{ translate('Cancelled') }}
                        ({{ $order_counter != null ? $order_counter['cancelled'] : 0 }})
                    </a>
                    <a href="{{ route('plugin.pickuppoint.orders', ['delivery_status' => config('tlecommercecore.order_delivery_status.delivered')]) }}"
                        class="btn sm btn-success">{{ translate('Delivered') }}
                        ({{ $order_counter != null ? $order_counter['delivered'] : 0 }})
                    </a>
                    <a href="{{ route('plugin.pickuppoint.orders', ['payment_status' => config('tlecommercecore.order_payment_status.unpaid')]) }}"
                        class="btn sm btn-warning">{{ translate('Unpaid') }}
                        ({{ $order_counter != null ? $order_counter['unpaid'] : 0 }})
                    </a>
                    <a href="{{ route('plugin.pickuppoint.orders', ['payment_status' => config('tlecommercecore.order_payment_status.paid')]) }}"
                        class="btn sm btn-success">{{ translate('Paid') }}
                        ({{ $order_counter != null ? $order_counter['paid'] : 0 }})
                    </a>
                </div>
                <!--End counter button-->
                <!--Filter and bulk action-->
                <div class="px-2 filter-area d-flex align-items-center">
                    <!--Bulk actions-->
                    <select class="theme-input-style" id="bulkActionSelector">
                        <option value="">
                            {{ translate('Bulk Action') }}
                        </option>
                        <option value="d-{{ config('tlecommercecore.order_delivery_status.processing') }}">
                            {{ translate('Change status to processing') }}
                        </option>
                        <option value="d-{{ config('tlecommercecore.order_delivery_status.ready_to_ship') }}">
                            {{ translate('Change status to ready to ship') }}
                        </option>
                        <option value="d-{{ config('tlecommercecore.order_delivery_status.shipped') }}">
                            {{ translate('Change status to shipped') }}
                        </option>
                        <option value="d-{{ config('tlecommercecore.order_delivery_status.delivered') }}">
                            {{ translate('Change status to delivered') }}
                        </option>
                        <option value="p-{{ config('tlecommercecore.order_payment_status.paid') }}">
                            {{ translate('Change status to paid') }}
                        </option>
                        <option value="p-{{ config('tlecommercecore.order_payment_status.unpaid') }}">
                            {{ translate('Change status to unpaid') }}
                        </option>
                        <option value="d-{{ config('tlecommercecore.order_delivery_status.cancelled') }}">
                            {{ translate('Move to trash') }}
                        </option>
                    </select>
                    <button class="btn long btn-danger fire-bulk-action"
                        href="{{ route('plugin.tlcommercecore.orders.inhouse') }}" type="submit">{{ translate('Apply') }}
                    </button>
                    <!--End bulk actions-->
                    <!--Filter data-->
                    <form method="get" action="{{ route('plugin.pickuppoint.orders') }}">
                        <select class="theme-input-style mb-2" name="per_page">
                            <option value="">{{ translate('Per page') }}</option>
                            <option value="20" @selected(request()->has('per_page') && request()->get('per_page') == '20')>20</option>
                            <option value="50" @selected(request()->has('per_page') && request()->get('per_page') == '50')>50</option>
                            <option value="all" @selected(request()->has('per_page') && request()->get('per_page') == 'all')>All</option>
                        </select>
                        <select class="theme-input-style mb-2" name="pick_up_point">
                            <option value="">{{ translate('Pickup Point') }}</option>
                            @foreach ($pick_up_points as $pick_up_point)
                                <option value="{{ $pick_up_point->id }}" @selected(request()->has('pick_up_point') && request()->get('pick_up_point') == $pick_up_point->id)>
                                    {{ $pick_up_point->name }}
                                </option>
                            @endforeach
                        </select>
                        <select class="theme-input-style mb-2" name="delivery_status">
                            <option value="">{{ translate('Delivery status') }}</option>
                            <option value="{{ config('tlecommercecore.order_delivery_status.pending') }}"
                                @selected(request()->has('delivery_status') && request()->get('delivery_status') == config('tlecommercecore.order_delivery_status.pending'))>
                                {{ translate('Pending') }}</option>
                            <option value="{{ config('tlecommercecore.order_delivery_status.pending') }}"
                                @selected(request()->has('delivery_status') && request()->get('delivery_status') == config('tlecommercecore.order_delivery_status.processing'))>
                                {{ translate('Processing') }}</option>
                            <option value="{{ config('tlecommercecore.order_delivery_status.delivered') }}"
                                @selected(request()->has('delivery_status') && request()->get('delivery_status') == config('tlecommercecore.order_delivery_status.delivered'))>
                                {{ translate('Delivered') }}</option>
                            <option value="{{ config('tlecommercecore.order_delivery_status.shipped') }}"
                                @selected(request()->has('delivery_status') && request()->get('delivery_status') == config('tlecommercecore.order_delivery_status.shipped'))>
                                {{ translate('Shipped') }}</option>
                            <option value="{{ config('tlecommercecore.order_delivery_status.cancelled') }}"
                                @selected(request()->has('delivery_status') && request()->get('delivery_status') == config('tlecommercecore.order_delivery_status.cancelled'))>
                                {{ translate('Cancelled') }}</option>
                        </select>
                        <select class="theme-input-style mb-2" name="payment_status">
                            <option value="">{{ translate('Payment status') }}</option>
                            <option value="{{ config('tlecommercecore.order_payment_status.paid') }}"
                                @selected(request()->has('payment_status') && request()->get('payment_status') == config('tlecommercecore.order_payment_status.paid'))>{{ translate('Paid') }}
                            </option>
                            <option value="{{ config('tlecommercecore.order_payment_status.unpaid') }}"
                                @selected(request()->has('payment_status') && request()->get('payment_status') == config('tlecommercecore.order_payment_status.unpaid'))>{{ translate('Unpaid') }}
                            </option>
                        </select>
                        <input type="text" class="theme-input-style mb-2" id="orderDateRange"
                            placeholder="Filter by date" name="order_date" readonly>
                        <input type="text" name="order_code" class="theme-input-style mb-2"
                            value="{{ request()->has('order_code') ? request()->get('order_code') : '' }}"
                            placeholder="Enter order code">
                        <button type="submit" class="btn long">{{ translate('Filter') }}</button>
                    </form>
                    @if (request()->has('order_code') ||
                            request()->has('payment_status') ||
                            request()->has('delivery_status') ||
                            request()->has('order_date'))
                        <a class="btn long btn-danger"
                            href="{{ route('plugin.pickuppoint.orders') }}">{{ translate('Clear Filter') }}</a>
                    @endif
                    <!--End filter data-->

                </div>
                <!--End filter and bulk action-->

                <!--Table -->
                <div class="table-responsive">
                    <table id="conditionTable" class="hoverable text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    <label class="position-relative mr-2">
                                        <input type="checkbox" name="select_all" class="select-all" onchange="selectAll()">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>{{ translate('Order Code') }}</th>
                                <th>{{ translate('Order Date') }}</th>
                                <th>{{ translate('Pickup Point') }}</th>
                                <th>{{ translate('Customer') }}</th>
                                <th>{{ translate('Num. of Products') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Order Status') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($orders->count() > 0)
                                @foreach ($orders as $key => $order)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="position-relative mr-2">
                                                    <input type="checkbox" name="items[]" class="item-id"
                                                        value="{{ $order->id }}">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $order->id]) }}"
                                                target="_blank">
                                                {{ $order->order_code }}
                                                @if ($order->read_at != null)
                                                    <span class="badge badge-success">{{ translate('New') }}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at }}</td>
                                        <td>{{ $order->pickup_point_name }}</td>
                                        <td>
                                            @if ($order->customer_name != null)
                                                <a href="{{ route('plugin.tlcommercecore.customers.details', ['id' => $order->customer_id]) }}"
                                                    target="_blank">
                                                    {{ $order->customer_name }}
                                                </a>
                                            @else
                                                {{ $order->guest_customer }}<span
                                                    class="badge badge-info">{{ translate('Guest') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->total_product }}</td>
                                        <td>{!! currencyExchange($order->total_payable_amount) !!}</td>
                                        <td>
                                            @if ($order->delivery_status == config('tlecommercecore.order_delivery_status.pending'))
                                                <button class="btn-success order-accept-btn"
                                                    data-order="{{ $order->id }}" title="Accept order">
                                                    <i class="icofont-check-circled"></i>
                                                </button>
                                            @endif
                                            @if ($order->delivery_status == config('tlecommercecore.order_delivery_status.pending'))
                                                <button class="btn-danger order-cancel-btn"
                                                    data-order="{{ $order->id }}" title="Cancel order"><i
                                                        class="icofont-delete"></i>
                                                </button>
                                            @endif
                                            @if ($order->delivery_status != config('tlecommercecore.order_delivery_status.pending'))
                                                <button class="btn-info status-details-btn" title="Update order status"
                                                    data-order="{{ $order->id }}">
                                                    <i class="icofont-ui-edit"></i>
                                                </button>
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
                                                    <a
                                                        href="{{ route('plugin.tlcommercecore.orders.details', ['id' => $order->id]) }}">{{ translate('Details') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9">
                                        <p class="alert alert-danger text-center">{{ translate('Nothing found') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pgination px-3">
                        {!! $orders->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5-custom') !!}
                    </div>
                </div>
                <!--End table-->
            </div>

        </div>
    </div>
    <!--Status Details Modal-->
    <div id="status-details-modal" class="status-details-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6 font-weight-bold">{{ translate('Update order status') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="detail-content"></div>
                </div>
            </div>
        </div>
    </div>
    <!--End Status Details Modal-->
    <!--Cancel  Modal-->
    <div id="order-cancel-modal" class="order-cancel-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Cancel Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to cancel  this order') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.orders.cancel') }}">
                        @csrf
                        <input type="hidden" name="order_id" id="cancelOrderId">
                        <button type="submit" class="btn long mt-2">{{ translate('Confirm') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Cancel Modal-->
    <!--Order Accept  Modal-->
    <div id="order-accept-modal" class="order-accept-modal modal fade show" aria-modal="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Accept Confirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{ translate('Are you sure to accept  this order') }}?</p>
                    <form method="POST" action="{{ route('plugin.tlcommercecore.orders.accept') }}">
                        @csrf
                        <input type="hidden" name="order_id" id="acceptOrderId">
                        <button type="submit" class="btn long mt-2">{{ translate('Confirm') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Order Accept Modal-->
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/public/backend/assets/plugins/daterangepicker/daterangepicker.js') }}">
    </script>
    <script>
        /**
         * Cancel order
         * 
         **/
        $('.order-cancel-btn').on('click', function(e) {
            e.preventDefault();
            let $this = $(this);
            let id = $this.data('order');
            $("#cancelOrderId").val(id);
            $("#order-cancel-modal").modal('show');
        });
        /**
         * Accept order
         * 
         **/
        $('.order-accept-btn').on('click', function(e) {
            e.preventDefault();
            let $this = $(this);
            let id = $this.data('order');
            $("#acceptOrderId").val(id);
            $("#order-accept-modal").modal('show');
        });
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
                    $.post('{{ route('plugin.tlcommercecore.orders.bulk.action') }}', {
                        _token: '{{ csrf_token() }}',
                        data: data
                    }, function(data) {
                        location.reload();
                    })
                } else {
                    toastr.error('{{ translate('No Item Selected') }}', "Error!");
                }
            } else {
                toastr.error('{{ translate('No Action Selected') }}', "Error!");
            }

        });
        /**
         * Get review details
         **/
        $('.status-details-btn').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('order');
            $.post('{{ route('plugin.tlcommercecore.orders.status.details') }}', {
                _token: '{{ csrf_token() }}',
                id: id
            }, function(data) {
                $('.detail-content').html(data);
                changeCurrencyFont();
                $('#status-details-modal').modal('show');
            })
        });
        /**
         * 
         * Select all Items
         **/
        function selectAll() {
            if ($('.select-all').is(":checked")) {
                $(".item-id").prop("checked", true);
            } else {
                $(".item-id").prop("checked", false);
            }
        }

        /**
         * select date range
         * 
         * 
         **/
        function cb(start, end) {

            let initVal = '{{ request()->has('order_date') ? request()->get('order_date') : '' }}';
            $('#orderDateRange').val(initVal);
        }

        var start = moment().subtract(0, 'days');
        var end = moment();

        $('#orderDateRange').on('apply.daterangepicker', function(ev, picker) {
            let val = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
                'YYYY-MM-DD')
            $('#orderDateRange').val(val);
        });
        $('#orderDateRange').daterangepicker({
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
    </script>
@endsection
