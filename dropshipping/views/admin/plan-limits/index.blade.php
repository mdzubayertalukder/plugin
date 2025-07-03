@extends('core::base.layouts.master')

@section('title')
{{ translate('Dropshipping Plan Limits') }}
@endsection

@section('main_content')
<div class="row">
    <div class="col-md-12">
        <div class="align-items-center border-bottom2 d-flex flex-wrap gap-10 justify-content-between mb-4 pb-3">
            <h4><i class="icofont-settings"></i> {{ translate('Dropshipping Plan Limits') }}</h4>
            <div class="d-flex align-items-center gap-10 flex-wrap">
                <a href="{{ route('admin.dropshipping.dashboard') }}" class="btn long">
                    <i class="icofont-arrow-left"></i> {{ translate('Back to Dashboard') }}
                </a>
                <a href="#" class="btn long">
                    <i class="icofont-plus"></i> {{ translate('Add Plan Limit') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-30">
            <div class="card-header bg-white border-bottom2">
                <h4>{{ translate('Package Import Limits') }}</h4>
            </div>
            <div class="card-body">
                @if(isset($limits) && $limits->count() > 0)
                <div class="table-responsive">
                    <table class="text-nowrap dh-table">
                        <thead>
                            <tr>
                                <th>{{ translate('Package Name') }}</th>
                                <th>{{ translate('Bulk Import Limit') }}</th>
                                <th>{{ translate('Monthly Limit') }}</th>
                                <th>{{ translate('Total Import Limit') }}</th>
                                <th>{{ translate('Auto Sync') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($limits as $limit)
                            <tr>
                                <td>
                                    <strong>{{ $limit->package_name ?? 'Unknown Package' }}</strong>
                                    @if(isset($limit->package_id))
                                    <br><small style="color: #666;">Package ID: {{ $limit->package_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span style="color: #007bff; font-weight: bold;">
                                        {{ $limit->bulk_import_limit == -1 ? translate('Unlimited') : number_format($limit->bulk_import_limit) }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: #17a2b8; font-weight: bold;">
                                        {{ $limit->monthly_import_limit == -1 ? translate('Unlimited') : number_format($limit->monthly_import_limit) }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: #ffc107; font-weight: bold;">
                                        {{ $limit->total_import_limit == -1 ? translate('Unlimited') : number_format($limit->total_import_limit) }}
                                    </span>
                                </td>
                                <td>
                                    @if($limit->auto_sync_enabled)
                                    <span style="color: #28a745; font-weight: bold;">{{ translate('Enabled') }}</span>
                                    @else
                                    <span style="color: #dc3545; font-weight: bold;">{{ translate('Disabled') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown-button">
                                        <a href="#" class="d-flex align-items-center" data-toggle="dropdown">
                                            <div class="menu-icon style--two mr-0">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item">
                                                <i class="icofont-edit"></i> {{ translate('Edit') }}
                                            </a>
                                            <a href="#" class="dropdown-item" style="color: #dc3545;">
                                                <i class="icofont-trash"></i> {{ translate('Delete') }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center" style="padding: 2rem 0;">
                    <i class="icofont-settings" style="font-size: 3rem; color: #999; margin-bottom: 1rem; display: block;"></i>
                    <h5 style="color: #999;">{{ translate('No plan limits configured') }}</h5>
                    <p style="color: #999;">{{ translate('Set import limits for different subscription packages') }}</p>
                    <a href="#" class="btn long">
                        <i class="icofont-plus"></i> {{ translate('Add First Plan Limit') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection