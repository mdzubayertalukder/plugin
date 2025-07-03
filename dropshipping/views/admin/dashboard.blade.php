@extends('core::base.layouts.master')

@section('title')
{{ translate('Dropshipping Dashboard') }}
@endsection

@section('main_content')
{{-- Header Section --}}
<div class="row">
    <div class="col-md-12">
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
            <div>
                <h4><i class="icofont-dashboard-web"></i> {{ translate('Dropshipping Dashboard') }}</h4>
                <p style="color: #666; margin: 0;">{{ translate('Manage WooCommerce integrations and monitor import activities') }}</p>
            </div>
            <div class="d-flex align-items-center gap-10 flex-wrap">
                @if($syncingStores > 0)
                <div class="alert alert-info mb-0 py-2 px-3" style="border-radius: 6px;">
                    <i class="icofont-sync" style="animation: spin 2s linear infinite;"></i>
                    {{ $syncingStores }} {{ translate('store(s) syncing') }}
                </div>
                @endif
                <a href="{{ route('admin.dropshipping.woocommerce.create') }}" class="btn long">
                    <i class="icofont-plus"></i> {{ translate('Add Store') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Statistics Overview --}}
<div class="row">
    <div class="col-lg-3 col-md-6 mb-30">
        <div class="card h-100" style="border-left: 4px solid #007bff;">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-left">
                        <h6 style="color: #666; text-transform: uppercase; font-size: 12px; margin-bottom: 5px;">
                            {{ translate('Total Stores') }}
                        </h6>
                        <h3 style="margin: 0; font-weight: bold; color: #333;">{{ number_format($totalConfigs) }}</h3>
                        <small style="color: #28a745;">
                            <i class="icofont-check-circled"></i> {{ $activeConfigs }} {{ translate('active') }}
                        </small>
                    </div>
                    <div class="icon-box" style="background: rgba(0,123,255,0.1); padding: 15px; border-radius: 50%;">
                        <i class="icofont-store" style="font-size: 24px; color: #007bff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-30">
        <div class="card h-100" style="border-left: 4px solid #28a745;">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-left">
                        <h6 style="color: #666; text-transform: uppercase; font-size: 12px; margin-bottom: 5px;">
                            {{ translate('Total Products') }}
                        </h6>
                        <h3 style="margin: 0; font-weight: bold; color: #333;">{{ number_format($totalProducts) }}</h3>
                        <small style="color: #007bff;">
                            <i class="icofont-upload"></i> {{ translate('synced from stores') }}
                        </small>
                    </div>
                    <div class="icon-box" style="background: rgba(40,167,69,0.1); padding: 15px; border-radius: 50%;">
                        <i class="icofont-box" style="font-size: 24px; color: #28a745;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-30">
        <div class="card h-100" style="border-left: 4px solid #ffc107;">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-left">
                        <h6 style="color: #666; text-transform: uppercase; font-size: 12px; margin-bottom: 5px;">
                            {{ translate('Total Imports') }}
                        </h6>
                        <h3 style="margin: 0; font-weight: bold; color: #333;">{{ number_format($totalImports) }}</h3>
                        <small style="color: #28a745;">
                            <i class="icofont-calendar"></i> {{ $todayImports }} {{ translate('today') }}
                        </small>
                    </div>
                    <div class="icon-box" style="background: rgba(255,193,7,0.1); padding: 15px; border-radius: 50%;">
                        <i class="icofont-download" style="font-size: 24px; color: #ffc107;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-30">
        <div class="card h-100" style="border-left: 4px solid #dc3545;">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-left">
                        <h6 style="color: #666; text-transform: uppercase; font-size: 12px; margin-bottom: 5px;">
                            {{ translate('Success Rate') }}
                        </h6>
                        <h3 style="margin: 0; font-weight: bold; color: #333;">
                            {{ $totalImports > 0 ? number_format(($successfulImports / $totalImports) * 100, 1) : 0 }}%
                        </h3>
                        <small style="color: #dc3545;">
                            <i class="icofont-close-circled"></i> {{ $failedImports }} {{ translate('failed') }}
                        </small>
                    </div>
                    <div class="icon-box" style="background: rgba(220,53,69,0.1); padding: 15px; border-radius: 50%;">
                        <i class="icofont-chart-pie" style="font-size: 24px; color: #dc3545;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions Panel --}}
<div class="row mb-30">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white border-bottom2">
                <h4><i class="icofont-rocket"></i> {{ translate('Quick Actions') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.dropshipping.woocommerce.index') }}" class="btn btn-outline-primary btn-block h-100 d-flex align-items-center justify-content-center" style="min-height: 60px; border-radius: 8px;">
                            <div class="text-center">
                                <i class="icofont-store d-block mb-1" style="font-size: 20px;"></i>
                                <span>{{ translate('Manage Stores') }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.dropshipping.plan-limits.index') }}" class="btn btn-outline-info btn-block h-100 d-flex align-items-center justify-content-center" style="min-height: 60px; border-radius: 8px;">
                            <div class="text-center">
                                <i class="icofont-settings d-block mb-1" style="font-size: 20px;"></i>
                                <span>{{ translate('Plan Limits') }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.dropshipping.reports.imports') }}" class="btn btn-outline-success btn-block h-100 d-flex align-items-center justify-content-center" style="min-height: 60px; border-radius: 8px;">
                            <div class="text-center">
                                <i class="icofont-chart-bar-graph d-block mb-1" style="font-size: 20px;"></i>
                                <span>{{ translate('Import Reports') }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.dropshipping.settings.index') }}" class="btn btn-outline-warning btn-block h-100 d-flex align-items-center justify-content-center" style="min-height: 60px; border-radius: 8px;">
                            <div class="text-center">
                                <i class="icofont-gear d-block mb-1" style="font-size: 20px;"></i>
                                <span>{{ translate('Settings') }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Content Area --}}
<div class="row">
    {{-- Recent Activities --}}
    <div class="col-lg-8 mb-30">
        <div class="card h-100">
            <div class="card-header bg-white border-bottom2 d-flex justify-content-between align-items-center">
                <h4><i class="icofont-clock-time"></i> {{ translate('Recent Import Activity') }}</h4>
                <a href="{{ route('admin.dropshipping.reports.imports') }}" class="btn btn-sm btn-outline-primary">
                    {{ translate('View All') }} <i class="icofont-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @if($recentImports->count() > 0)
                <div class="table-responsive">
                    <table class="text-nowrap dh-table">
                        <thead>
                            <tr>
                                <th>{{ translate('Store') }}</th>
                                <th>{{ translate('Product') }}</th>
                                <th>{{ translate('Tenant') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentImports as $import)
                            <tr>
                                <td>
                                    <strong style="color: #007bff;">{{ $import->config_name ?? 'Unknown' }}</strong>
                                </td>
                                <td>
                                    {{ $import->product_name ?? 'Product Info Not Available' }}
                                </td>
                                <td>
                                    <span style="color: #666;">{{ $import->tenant_id }}</span>
                                </td>
                                <td>
                                    @if($import->status == 'completed')
                                    <span style="color: #28a745; font-weight: bold;">
                                        <i class="icofont-check-circled"></i> {{ translate('Completed') }}
                                    </span>
                                    @elseif($import->status == 'failed')
                                    <span style="color: #dc3545; font-weight: bold;">
                                        <i class="icofont-close-circled"></i> {{ translate('Failed') }}
                                    </span>
                                    @elseif($import->status == 'processing')
                                    <span style="color: #ffc107; font-weight: bold;">
                                        <i class="icofont-spinner"></i> {{ translate('Processing') }}
                                    </span>
                                    @else
                                    <span style="color: #6c757d; font-weight: bold;">
                                        <i class="icofont-clock-time"></i> {{ translate('Pending') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <small style="color: #666;">
                                        {{ \Carbon\Carbon::parse($import->created_at)->diffForHumans() }}
                                    </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="icofont-files-stack" style="font-size: 3rem; color: #999; margin-bottom: 1rem; display: block;"></i>
                    <h5 style="color: #999;">{{ translate('No recent import activity') }}</h5>
                    <p style="color: #999;">{{ translate('Import activities will appear here once tenants start importing products') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- System Status & Top Stores --}}
    <div class="col-lg-4 mb-30">
        {{-- System Status --}}
        <div class="card mb-30">
            <div class="card-header bg-white border-bottom2">
                <h4><i class="icofont-heart-beat"></i> {{ translate('System Status') }}</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ translate('Successful Imports') }}</span>
                        <span style="color: #28a745; font-weight: bold;">{{ $successfulImports }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $totalImports > 0 ? ($successfulImports / $totalImports) * 100 : 0 }}%;"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ translate('Failed Imports') }}</span>
                        <span style="color: #dc3545; font-weight: bold;">{{ $failedImports }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: {{ $totalImports > 0 ? ($failedImports / $totalImports) * 100 : 0 }}%;"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ translate('Pending Imports') }}</span>
                        <span style="color: #ffc107; font-weight: bold;">{{ $pendingImports }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $totalImports > 0 ? ($pendingImports / $totalImports) * 100 : 0 }}%;"></div>
                    </div>
                </div>

                @if($lastSyncTime)
                <div class="text-center mt-3 pt-3 border-top">
                    <small style="color: #666;">
                        <i class="icofont-clock-time"></i>
                        {{ translate('Last sync') }}: {{ \Carbon\Carbon::parse($lastSyncTime)->diffForHumans() }}
                    </small>
                </div>
                @endif
            </div>
        </div>

        {{-- Top Performing Stores --}}
        <div class="card">
            <div class="card-header bg-white border-bottom2">
                <h4><i class="icofont-star"></i> {{ translate('Top Stores') }}</h4>
            </div>
            <div class="card-body">
                @if($topStores->count() > 0)
                @foreach($topStores as $store)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <strong style="color: #333;">{{ $store->name }}</strong>
                        <br>
                        <small style="color: #666;">{{ $store->product_count }} {{ translate('products') }}</small>
                        @if($store->is_active)
                        <span style="color: #28a745; font-size: 10px;">
                            <i class="icofont-check-circled"></i> {{ translate('Active') }}
                        </span>
                        @else
                        <span style="color: #dc3545; font-size: 10px;">
                            <i class="icofont-close-circled"></i> {{ translate('Inactive') }}
                        </span>
                        @endif
                    </div>
                    <div class="text-right">
                        <div style="background: rgba(0,123,255,0.1); padding: 8px 12px; border-radius: 20px; color: #007bff; font-weight: bold;">
                            {{ $store->product_count }}
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-3">
                    <i class="icofont-store" style="font-size: 2rem; color: #999;"></i>
                    <p style="color: #999; margin: 10px 0 0 0;">{{ translate('No stores configured') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>



@endsection