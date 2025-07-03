@extends('core::base.layouts.master')

@section('title')
{{ translate('Dropshipping Dashboard') }}
@endsection

@section('custom_css')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stats-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .quick-action-card {
        border-radius: 10px;
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }

    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('main_content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4><i class="icofont-truck"></i> {{ translate('Dropshipping Dashboard') }}</h4>
                <p class="text-muted">{{ translate('Manage your dropshipping products and imports') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="stats-number">{{ number_format($importStats['total_imports']) }}</div>
            <div class="stats-label">{{ translate('Total Imports') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="stats-number">{{ number_format($importStats['successful_imports']) }}</div>
            <div class="stats-label">{{ translate('Successful Imports') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
            <div class="stats-number">{{ number_format($importStats['pending_imports']) }}</div>
            <div class="stats-label">{{ translate('Pending Imports') }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
            <div class="stats-number">{{ number_format($importStats['this_month']) }}</div>
            <div class="stats-label">{{ translate('This Month') }}</div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card quick-action-card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="icofont-search-product fa-3x text-primary"></i>
                </div>
                <h5>{{ translate('Browse Products') }}</h5>
                <p class="text-muted">{{ translate('Browse and import products from available stores') }}</p>
                @if(Route::has('dropshipping.products'))
                <a href="{{ route('dropshipping.products') }}" class="btn btn-primary">{{ translate('Browse Now') }}</a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card quick-action-card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="icofont-history fa-3x text-success"></i>
                </div>
                <h5>{{ translate('Import History') }}</h5>
                <p class="text-muted">{{ translate('View your product import history and status') }}</p>
                @if(Route::has('dropshipping.import.history'))
                <a href="{{ route('dropshipping.import.history') }}" class="btn btn-success">{{ translate('View History') }}</a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card quick-action-card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="icofont-package fa-3x text-info"></i>
                </div>
                <h5>{{ translate('Imported Products') }}</h5>
                <p class="text-muted">{{ translate('Manage your imported dropshipping products') }}</p>
                @if(Route::has('dropshipping.manage.imported'))
                <a href="{{ route('dropshipping.manage.imported') }}" class="btn btn-info">{{ translate('Manage Products') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Recent Imports Table --}}
@if($recentImports->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="icofont-clock-time"></i> {{ translate('Recent Imports') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ translate('Product') }}</th>
                                <th>{{ translate('Store') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentImports as $import)
                            <tr>
                                <td>
                                    <strong>{{ $import->product_name ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ $import->store_name ?? 'N/A' }}</td>
                                <td>
                                    @if($import->status == 'completed')
                                    <span class="badge badge-success">{{ translate('Completed') }}</span>
                                    @elseif($import->status == 'pending')
                                    <span class="badge badge-warning">{{ translate('Pending') }}</span>
                                    @else
                                    <span class="badge badge-danger">{{ translate('Failed') }}</span>
                                    @endif
                                </td>
                                <td>{{ date('M d, Y', strtotime($import->created_at)) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Available Stores Info --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <i class="icofont-store fa-2x text-muted"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ translate('Available Stores') }}</h5>
                        <p class="text-muted mb-0">
                            {{ translate('You have access to') }} <strong>{{ number_format($availableStores) }}</strong> {{ translate('dropshipping stores') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection