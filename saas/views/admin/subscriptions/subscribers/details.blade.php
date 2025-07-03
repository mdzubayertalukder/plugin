@extends('core::base.layouts.master')
@section('title')
    {{ translate('Subscriber Details') }} -{{ $subscriber_info->name }}
@endsection
@section('main_content')
    <div class="row">
        <div class="col-md-4 mb-20">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h4>{{ translate('Subscriber Details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="center mb-20">
                        <img src="{{ getFilePath($subscriber_info->image) }}" alt="{{ $subscriber_info->name }}"
                            class="img-70 rounded-circle">
                    </div>
                    <p class="mb-1"><span class="bold">{{ translate('Name') }} :</span> {{ $subscriber_info->name }}</p>
                    <p class="mb-1"><span class="bold">{{ translate('Email') }} :</span> {{ $subscriber_info->email }}
                    </p>
                    <p class="mb-1"><span class="bold">{{ translate('Registered') }} :</span>
                        {{ $subscriber_info->created_at->format('d M Y') }}
                        ({{ $subscriber_info->created_at->diffForHumans() }})
                    </p>
                    <p>
                        <span class="bold">{{ translate('Status') }} :</span>
                        @if ($subscriber_info->status == 1)
                            <span class="badge badge-success">{{ translate('Active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ translate('Inzctive') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <!-- Store List-->
            <div class="card mb-30">
                <div class="card-header py-3 bg-white">
                    <h4>{{ translate('Stores') }}</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="hoverable text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Store Name') }} </th>
                                    <th>{{ translate('Subscription') }} </th>
                                    <th>{{ translate('Subscription Period') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th>{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $key = 1;
                                @endphp
                                @forelse ($stores as $store)
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
                                        <td>
                                            {{ $store->store_name }}
                                        </td>
                                        <td>
                                            {{ $store->package != null ? $store->package->translation('name') : '' }}
                                            @if ($store->plan != null)
                                                - {{ $store->plan->name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($store->is_expired())
                                                @if ($store->is_trial() == 1)
                                                    <p class="badge badge-danger mb-0">{{ translate('Trail Expried') }}</p>
                                                @else
                                                    <p class="badge badge-danger">{{ translate('Expried') }}</p>
                                                @endif
                                            @else
                                                @if ($store->valid_till == null || $store->plan_id == config('saas.plans.lifetime'))
                                                    {{ translate('Lifetime') }}
                                                @else
                                                    @if ($store->is_trial() == 1)
                                                        {{ translate('Trial Ends At') }} {{ $store->valid_till }}
                                                    @else
                                                        {{ $store->valid_till }}
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($store->status == 0)
                                                <span class="badge badge-danger">{{ translate('Inactive') }}</span>
                                            @else
                                                <span class="badge badge-success">{{ translate('Active') }}</span>
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
                                                    <a href="{{ route('plugin.saas.store.details', $store->id) }}">
                                                        {{ translate('Show Details') }}
                                                    </a>
                                                    @if ($store->domain != null)
                                                        <a href="https://{{ $store->domain->domain }}">
                                                            {{ translate('Visit Frontend') }}
                                                        </a>
                                                        <a href="https://{{ $store->domain->domain }}/admin">
                                                            {{ translate('Visit Admin Panel') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $key++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="6">
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
            <!-- Store List-->

            <!-- Payment Histories-->
            <div class="card mb-30">
                <div class="card-header py-3 bg-white">
                    <h4>{{ translate('Payment Histories') }}</h4>
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
                                    <th>{{ translate('Final Amout') }} </th>
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
                                            <a href="{{ route('plugin.saas.admin.print.subscription.payment.invoice', $payment->store_id) }}"
                                                class="btn btn-success sm">{{ translate('Invoice') }}</a>
                                        </td>
                                    </tr>
                                    @php
                                        $key++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="7">
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
            <!-- Payment Histories-->

            <!-- Custom Domain Requests-->
            <div class="card mb-30">
                <div class="card-header py-3 bg-white">
                    <h4 class="font-20">{{ translate('Custom Domains') }}</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="hoverable text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Store Name') }} </th>
                                    <th>{{ translate('Subdomain') }} </th>
                                    <th>{{ translate('Requested Domain') }} </th>
                                    <th>{{ translate('Duration') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $key = 1;
                                @endphp
                                @forelse ($domain_request as $domain)
                                    @php
                                        $status = '';
                                        $class = 'badge-danger';
                                        if ($domain->status == 0) {
                                            $status = 'Pending';
                                            $class = 'badge-warning';
                                        }
                                        if ($domain->status == 1) {
                                            $status = 'Approved';
                                            $class = 'badge-success';
                                        }
                                        if ($domain->status == 2) {
                                            $status = 'Cancelled';
                                            $class = 'badge-danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $domain->saasAccount->store_name }}</td>
                                        <td>{{ $domain->current_domain }} <span
                                                class="badge mb-2 {{ $class }}">{{ $status }}</span></td>
                                        <td>{{ $domain->requested_domain }}</td>
                                        <td>
                                            @if ($domain->status == 0)
                                                -
                                            @endif
                                            @if ($domain->status == 1)
                                                {{ $domain->approved_date }} - {{ translate('Present') }}
                                            @endif
                                            @if ($domain->status == 2)
                                                {{ $domain->approved_date }} - {{ $domain->cancelled_date }}
                                            @endif
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
            <!-- Custom Domain Requests-->
        </div>
    </div>
@endsection
