@php
    $plans = getAllPlans();
    $plugins = availablePluginsForTenant();
    $payment_methods = getTenantPaymentGateways();

    $applicable_plugins = $package_details->plugins->toArray();
    $applicable_payment_methods = $package_details->payment_methods->toArray();
@endphp
@extends('plugin/saas::user.panel.layouts.user_dashboard_layout')
@section('title')
    {{ translate('Store Details') }}
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-30">
                <div class="card-header py-3 bg-white">
                    <h4>{{ translate('Store Details') }}</h4>
                </div>
                <div class="card-body">
                    <h3 class="mb-3">
                        {{ $saas_account_details->store_name }}
                    </h3>
                    <ul class="status-list">
                        <li>
                            <span class="bold mr-1">{{ translate('Subscriber') }}: </span>
                            {{ $saas_account_details->subscriber }}
                        </li>
                        @if ($saas_account_details->domain != null)
                            <li><span class="bold mr-1">{{ translate('Domain') }}:</span>
                                <span>{{ $saas_account_details->domain }}</span>
                            </li>
                        @endif
                        <li><span class="bold mr-1">{{ 'Membership' }}:</span>
                            <span>{{ $saas_account_details->membership_type }}</span>
                        </li>
                        <li><span class="bold mr-1">{{ translate('Package') }}:</span>
                            <span>{{ translatePackageName($saas_account_details->package_id) }}</span>
                        </li>
                        <li><span class="bold mr-1">{{ translate('Plan') }}:</span>
                            <span>{{ translate($saas_account_details->plan_name) }}</span>
                        </li>
                        <li><span class="bold mr-1">{{ translate('Subscribed') }}:</span>
                            <span>{{ $saas_account_details->created_at }}</span>
                        </li>
                        <li><span class="bold mr-1">{{ translate('Subscription Expire') }}:</span>
                            <span>{{ $saas_account_details->due_date == null ? 'Lifetime' : $saas_account_details->due_date }}</span>
                        </li>
                        <li><span class="bold mr-1">{{ translate('Renewed') }}:</span>
                            <span>{{ $saas_account_details->renewed }}</span>
                        </li>
                        <li><span class="bold mr-1">{{ translate('Status') }}:</span>
                            @if ($saas_account_details->status == 0)
                                <span class="badge badge-danger mt-1">
                                    {{ translate('Inactive') }}
                                </span>
                            @else
                                <span class="badge badge-success mt-1">
                                    {{ translate('Active') }}
                                </span>
                            @endif
                        </li>
                    </ul>
                    <div class="d-flex gap-10 mt-3">
                        @if ($saas_account_details->domain != null)
                            <a href="https://{{ $saas_account_details->domain }}" target="_blank">
                                <h3 class="btn  status-btn btn-danger">{{ translate('Store Frontend') }}
                                </h3>
                            </a>
                            <a href="https://{{ $saas_account_details->domain }}/admin" target="_blank">
                                <button class="btn  status-btn btn-dark">{{ translate('Store Admin Panel') }}</button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-30">
                <div class="card-header py-3 bg-white">
                    <h4>{{ translate('Package Features') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="invoice invoice-form">
                                <h5 class="mb-3">{{ translate('Applicable Features') }}</h5>
                                <ul class="list-unstyled mb-4">
                                    @foreach ($plugins as $plugin)
                                        @if ($plugin->type != 'saas' && $plugin->location != 'tlecommercecore')
                                            <li class="mb-2">
                                                @if (in_array($plugin->id, array_column($applicable_plugins, 'plugin_id')))
                                                    <i class="icofont-check text-success"></i>
                                                @else
                                                    <i class="icofont-close text-danger"></i>
                                                @endif

                                                {{ $plugin->name }}
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="invoice invoice-form">
                                @php
                                    $privileges = $package_details->privileges;
                                @endphp
                                @if ($privileges != null)
                                    <h5 class="mb-3">{{ translate('Access Privileges') }}</h5>
                                    <ul class="list-unstyled mb-4">
                                        @foreach ($privileges as $key => $value)
                                            @php
                                                $privilege = str_replace('package_privileges_', '', $key);
                                                $privilege = ucwords(implode(' ', explode('_', $privilege)));
                                            @endphp
                                            <li class="mb-2">
                                                <i class="icofont-check text-success"></i>
                                                {{ $privilege }} - {{ $value == -1 ? 'Unlimitted' : $value }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="invoice invoice-form">
                                <h5 class="mb-3">{{ translate('Applicable Payment Methods') }}</h5>
                                <ul class="list-unstyled mb-4">
                                    @foreach ($payment_methods as $method => $id)
                                        <li class="mb-2">
                                            @if (in_array($id, array_column($applicable_payment_methods, 'payment_method')))
                                                <i class="icofont-check text-success"></i>
                                            @else
                                                <i class="icofont-close text-danger"></i>
                                            @endif
                                            @if ($method == 'cod')
                                                {{ translate('Cash On Delivery') }}
                                            @else
                                                {{ ucfirst($method) }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-30">
                <div class="card-header bg-white py-3">
                    <h4>{{ translate('Payment History') }}</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="hoverable text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Payment ID') }} </th>
                                    <th>{{ translate('Payment Date') }} </th>
                                    <th>{{ translate('Payment Method') }} </th>
                                    <th>{{ translate('Final Amount') }} </th>
                                    <th>{{ translate('Invoice') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $key = 1;
                                @endphp
                                @forelse ($payment_history as $payment)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $payment->pid }}</td>
                                        <td>{{ $payment->updated_at }}</td>
                                        <td>{{ $payment->method }}</td>
                                        <td>{{ $payment->final_amount }} {{ $payment->currency }}</td>
                                        <td>
                                            <a href="{{ route('plugin.saas.print.subscription.payment.invoice', $saas_account_details->store_id) }}"
                                                class="btn btn-success sm">{{ translate('Invoice') }}</a>
                                        </td>
                                    </tr>
                                    @php
                                        $key++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="5">
                                            <p class="mb-0 alert">
                                                No data found in this table
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
