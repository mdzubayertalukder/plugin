@extends('core::base.layouts.master')
@section('title')
    {{ translate('Payment History') }}
@endsection
@section('custom_css')
    <!-- ======= BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/plugins/data-table/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- ======= END BEGIN PAGE LEVEL PLUGINS STYLES ======= -->
@endsection
@section('main_content')
    <div class="row">
        <!-- Payment History -->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Payment History') }}</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2 " id="payment_history">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Payment ID') }} </th>
                                <th>{{ translate('Subscriber') }} </th>
                                <th>{{ translate('Payment Date') }} </th>
                                <th>{{ translate('Payment Method') }} </th>
                                <th>{{ translate('Payment Currency') }} </th>
                                <th>{{ translate('Final Amout') }} </th>
                                <th>{{ translate('Invoice') }} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($payment_history as $payment)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $payment->pid }}</td>
                                    <td>{{ $payment->subscriber }}</td>
                                    <td>{{ $payment->updated_at }}</td>
                                    <td>{{ $payment->method }}</td>
                                    <td>{{ $payment->currency }}</td>
                                    <td>{{ $payment->final_amount }}</td>
                                    <td>
                                        <a href="{{ route('plugin.saas.admin.print.subscription.payment.invoice', $payment->store_id) }}"
                                            class="btn btn-success sm">{{ translate('Invoice') }}</a>
                                    </td>
                                </tr>
                                @php
                                    $key++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Payment History-->
    </div>
@endsection
@section('custom_scripts')
    <script src="{{ asset('/public/backend/assets/plugins/data-table/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('/public/backend/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
    </script>

    <script type="application/javascript">
        (function($) {
            "use strict";
            $("#payment_history").DataTable();
        })(jQuery);
    </script>
@endsection