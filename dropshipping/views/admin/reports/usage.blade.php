@extends('core::base.layouts.master')

@section('title')
{{ translate('Usage Reports') }}
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
            <h4><i class="icofont-chart-bar-graph"></i> {{ translate('Usage Reports') }}</h4>
            <div class="d-flex align-items-center gap-10 flex-wrap">
                <a href="{{ route('admin.dropshipping.dashboard') }}" class="btn btn-outline-primary">
                    <i class="icofont-arrow-left"></i> {{ translate('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-30">
            <div class="card-header bg-white border-bottom2">
                <h4>{{ translate('Tenant Usage Statistics') }}</h4>
            </div>
            <div class="card-body">
                @if($usage->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ translate('Tenant ID') }}</th>
                                <th>{{ translate('Total Imports') }}</th>
                                <th>{{ translate('Successful') }}</th>
                                <th>{{ translate('Failed') }}</th>
                                <th>{{ translate('Last Import') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usage as $tenant)
                            <tr>
                                <td>{{ $tenant->tenant_id }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ number_format($tenant->total_imports) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ number_format($tenant->successful_imports) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-danger">{{ number_format($tenant->failed_imports) }}</span>
                                </td>
                                <td>{{ $tenant->last_import }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $usage->links() }}
                @else
                <div class="text-center py-4">
                    <i class="icofont-chart-bar-graph fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ translate('No usage data available') }}</h5>
                    <p class="text-muted">{{ translate('Usage statistics will appear here once tenants start importing products') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection