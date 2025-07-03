@extends('core::base.layouts.master')

@section('title')
{{ translate('Import Reports') }}
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
            <h4><i class="icofont-chart-pie"></i> {{ translate('Import Reports') }}</h4>
            <div class="d-flex align-items-center gap-10 flex-wrap">
                <a href="{{ route('admin.dropshipping.dashboard') }}" class="btn long">
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
                <h4>{{ translate('Import Activity Reports') }}</h4>
            </div>
            <div class="card-body">
                @if(isset($reports) && $reports->count() > 0)
                <div class="table-responsive">
                    <table class="text-nowrap dh-table">
                        <thead>
                            <tr>
                                <th>{{ translate('WooCommerce Store') }}</th>
                                <th>{{ translate('Tenant') }}</th>
                                <th>{{ translate('Product') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Import Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->config_name ?? 'Unknown Store' }}</strong>
                                </td>
                                <td>
                                    <span style="color: #666;">{{ $report->tenant_id }}</span>
                                </td>
                                <td>
                                    {{ $report->product_name ?? 'Product Name Not Available' }}
                                </td>
                                <td>
                                    @if($report->status == 'completed')
                                    <span style="color: #28a745; font-weight: bold;">{{ translate('Completed') }}</span>
                                    @elseif($report->status == 'failed')
                                    <span style="color: #dc3545; font-weight: bold;">{{ translate('Failed') }}</span>
                                    @elseif($report->status == 'processing')
                                    <span style="color: #ffc107; font-weight: bold;">{{ translate('Processing') }}</span>
                                    @else
                                    <span style="color: #6c757d; font-weight: bold;">{{ translate('Pending') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ date('M d, Y H:i', strtotime($report->created_at)) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $reports->links() }}
                </div>
                @else
                <div class="text-center" style="padding: 2rem 0;">
                    <i class="icofont-chart-pie" style="font-size: 3rem; color: #999; margin-bottom: 1rem; display: block;"></i>
                    <h5 style="color: #999;">{{ translate('No import reports available') }}</h5>
                    <p style="color: #999;">{{ translate('Import reports will appear here once tenants start importing products') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection