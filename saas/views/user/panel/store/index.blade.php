@php
    $is_extended = isExtendedLicense();
@endphp
@extends('plugin/saas::user.panel.layouts.user_dashboard_layout')
@section('title')
    {{ translate('Stores') }}
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

    <style>
        .hover-hand {
            cursor: pointer;
        }
    </style>
@endsection
@section('main_content')
    <div class="row">
        <!-- Store List-->
        <div class="col-md-12">
            <div class="card mb-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="font-20">{{ translate('Stores') }}</h4>
                        @if ($is_extended)
                            <a href="{{ route('plugin.saas.subscribe.now') }}"
                                class="btn long">{{ translate('Create New Store') }}</a>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="hoverable text-nowrap border-top2 " id="store_list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Store Name') }} </th>
                                <th>{{ translate('Package Name') }}</th>
                                <th>{{ translate('Plan Name') }}</th>
                                <th>{{ translate('Valid Untill') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $key = 1;
                            @endphp
                            @foreach ($stores as $store)
                                @php
                                    $class = '';
                                    if ($store->is_notifiable() == 0) {
                                        $class = 'table-danger';
                                    }
                                    if ($store->is_notifiable() == 1) {
                                        $class = 'table-warning';
                                    }
                                @endphp
                                <tr class="{{ $class }}">
                                    <td>{{ $key }}.</td>
                                    <td> {{ $store->store_name }} </td>
                                    <td>{{ $store->package != null ? $store->package->translation('name') : '' }}</td>
                                    <td>
                                        @if ($store->plan != null)
                                            {{ $store->plan->name }}
                                        @else
                                            <i class="icofont-ban"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($store->is_expired())
                                            @if ($store->is_trial() == 1)
                                                <p class="badge badge-danger mb-0">{{ translate('Trail Expried') }}</p><br>
                                                <a href="{{ route('plugin.saas.user.order.plan', ['package' => $store->package->id, 'plan' => $store->plan->id, 'is_trial' => 0, 'store' => $store->id]) }}"
                                                    class="btn-link">{{ translate('Upgrade Now') }}</a>
                                            @else
                                                <p class="badge badge-danger mb-0">{{ translate('Expried') }}</p><br>
                                                <a href="{{ route('plugin.saas.user.order.plan', ['package' => $store->package->id, 'plan' => $store->plan->id, 'is_trial' => 0, 'store' => $store->id]) }}"
                                                    class="btn-link">{{ translate('Upgrade Now') }}
                                                </a>
                                            @endif
                                        @else
                                            @if ($store->valid_till == null || $store->plan_id == config('saas.plans.lifetime'))
                                                {{ translate('Lifetime') }}
                                            @else
                                                @if ($store->is_trial() == 1)
                                                    {{ translate('Trial Ends At') }} {{ $store->valid_till }}<br>
                                                    <a href="{{ route('plugin.saas.user.order.plan', ['package' => $store->package->id, 'plan' => $store->plan->id, 'is_trial' => 0, 'store' => $store->id]) }}"
                                                        class="btn-link">{{ translate('Upgrade Now') }}
                                                    </a>
                                                @else
                                                    {{ $store->valid_till }}
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if ($store->status == 0)
                                            <span class="badge badge-primary">{{ translate('Pending') }}</span>
                                        @else
                                            <span class="badge badge-success">{{ translate('Approved') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown-button">
                                            <a href="#" class="d-flex align-items-center" data-toggle="dropdown">
                                                <div class="menu-icon style--two mr-0">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a
                                                    href="{{ route('plugin.saas.user.store.details', $store->id) }}">{{ translate('Show Details') }}</a>

                                                @if ($store->domain != null)
                                                    <a
                                                        href="https://{{ $store->domain->domain }}/admin">{{ translate('Visit Admin Panel') }}</a>
                                                    <a
                                                        href="https://{{ $store->domain->domain }}">{{ translate('Visit Frontend') }}</a>
                                                @endif
                                                @if ($is_extended)
                                                    <a
                                                        href="{{ route('plugin.saas.change.subscription.plan', $store->id) }}">{{ translate('Change Plan') }}</a>
                                                @endif
                                            </div>
                                        </div>
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
        <!-- Store List-->
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
        $("#store_list").DataTable();
    })(jQuery);
</script>
@endsection
