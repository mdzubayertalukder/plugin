@extends('core::base.layouts.master')

@section('title')
{{ translate('Import History') }}
@endsection

@section('custom_css')
<style>
    .status-badge {
        font-size: 0.85rem;
    }

    .history-card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .history-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .filter-section {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('main_content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4><i class="icofont-history"></i> {{ translate('Import History') }}</h4>
                <p class="text-muted">{{ translate('View your product import history and status') }}</p>
            </div>
            <div>
                @if(Route::has('dropshipping.products'))
                <a href="{{ route('dropshipping.products') }}" class="btn btn-primary">
                    <i class="icofont-plus"></i> {{ translate('Import More Products') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Filter Section --}}
<div class="row">
    <div class="col-12">
        <div class="filter-section">
            <form method="GET" action="{{ route('dropshipping.import.history') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="status">{{ translate('Status') }}</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">{{ translate('All Status') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ translate('Completed') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ translate('Pending') }}</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ translate('Failed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from">{{ translate('From Date') }}</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to">{{ translate('To Date') }}</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="icofont-search-1"></i> {{ translate('Filter') }}
                        </button>
                        <a href="{{ route('dropshipping.import.history') }}" class="btn btn-outline-secondary ml-2">
                            {{ translate('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import History Table --}}
@if(isset($imports) && $imports->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card history-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="icofont-list"></i> {{ translate('Import History') }}
                    <span class="badge badge-primary ml-2">{{ $imports->total() }} {{ translate('total') }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ translate('Product') }}</th>
                                <th>{{ translate('Store') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th>{{ translate('Import Date') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($imports as $import)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>{{ $import->product_name ?? 'N/A' }}</strong>
                                            @if(isset($import->sku) && $import->sku)
                                            <br><small class="text-muted">SKU: {{ $import->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-primary">{{ $import->store_name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($import->status == 'completed')
                                    <span class="badge badge-success status-badge">
                                        <i class="icofont-check"></i> {{ translate('Completed') }}
                                    </span>
                                    @elseif($import->status == 'pending')
                                    <span class="badge badge-warning status-badge">
                                        <i class="icofont-clock-time"></i> {{ translate('Pending') }}
                                    </span>
                                    @elseif($import->status == 'processing')
                                    <span class="badge badge-info status-badge">
                                        <i class="icofont-refresh"></i> {{ translate('Processing') }}
                                    </span>
                                    @else
                                    <span class="badge badge-danger status-badge">
                                        <i class="icofont-close"></i> {{ translate('Failed') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        {{ date('M d, Y', strtotime($import->created_at)) }}
                                        <br><small class="text-muted">{{ date('h:i A', strtotime($import->created_at)) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($import->status == 'completed')
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="viewProduct({{ $import->id }})"
                                            title="{{ translate('View Product') }}">
                                            <i class="icofont-eye"></i>
                                        </button>
                                        @endif

                                        @if($import->status == 'failed')
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                            onclick="retryImport({{ $import->id }})"
                                            title="{{ translate('Retry Import') }}">
                                            <i class="icofont-refresh"></i>
                                        </button>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-outline-info"
                                            onclick="viewDetails({{ $import->id }})"
                                            title="{{ translate('View Details') }}">
                                            <i class="icofont-info"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pagination --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $imports->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@else
{{-- Empty State --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body empty-state">
                <i class="icofont-history" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">{{ translate('No Import History') }}</h4>
                <p class="text-muted">{{ translate('You haven\'t imported any products yet') }}</p>
                @if(Route::has('dropshipping.products'))
                <a href="{{ route('dropshipping.products') }}" class="btn btn-primary">
                    <i class="icofont-plus"></i> {{ translate('Import Your First Product') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- Details Modal --}}
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Import Details') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">{{ translate('Loading...') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
    function viewProduct(importId) {
        // Redirect to product view or open in new tab
        window.open(`/products/${importId}`, '_blank');
    }

    function retryImport(importId) {
        if (confirm('{{ translate("Are you sure you want to retry this import?") }}')) {
            $.ajax({
                url: `{{ route('dropshipping.import.single', ':id') }}`.replace(':id', importId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    retry: true
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('{{ translate("Failed to retry import") }}');
                }
            });
        }
    }

    function viewDetails(importId) {
        $('#detailsModal').modal('show');

        $.ajax({
            url: `{{ route('dropshipping.ajax.product-details', ':id') }}`.replace(':id', importId),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#modalContent').html(response.html);
                } else {
                    $('#modalContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#modalContent').html('<div class="alert alert-danger">{{ translate("Failed to load details") }}</div>');
            }
        });
    }

    // Toast notification fallback
    if (typeof toastr === 'undefined') {
        window.toastr = {
            success: function(msg) {
                alert('Success: ' + msg);
            },
            error: function(msg) {
                alert('Error: ' + msg);
            }
        };
    }
</script>
@endsection