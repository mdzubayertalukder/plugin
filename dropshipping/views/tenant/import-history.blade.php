@extends('layouts.app')

@section('title', 'Import History')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 font-weight-bold text-dark mb-1">Import History</h2>
                    <p class="text-muted">Track your imported products and their status</p>
                </div>
                <div>
                    <a href="{{ route('dropshipping.products.all') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Import More Products
                    </a>
                </div>
            </div>

            <!-- Import History Table -->
            @if($imports && count($imports) > 0)
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">Image</th>
                                    <th>Product Name</th>
                                    <th>Store</th>
                                    <th>SKU</th>
                                    <th width="120">Price</th>
                                    <th width="100">Status</th>
                                    <th width="140">Import Date</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($imports as $import)
                                <tr>
                                    <!-- Product Image -->
                                    <td>
                                        @if($import->source_product_image)
                                        <img src="{{ $import->source_product_image }}"
                                            class="img-thumbnail"
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                            alt="{{ $import->product_name }}">
                                        @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                        @endif
                                    </td>

                                    <!-- Product Name -->
                                    <td>
                                        <div class="fw-medium text-dark">
                                            {{ Str::limit($import->product_name, 40) }}
                                        </div>
                                        @if($import->source_product_name && $import->source_product_name != $import->product_name)
                                        <small class="text-muted">
                                            Original: {{ Str::limit($import->source_product_name, 30) }}
                                        </small>
                                        @endif
                                    </td>

                                    <!-- Store -->
                                    <td>
                                        <span class="badge bg-info">{{ $import->store_name ?? 'Unknown' }}</span>
                                    </td>

                                    <!-- SKU -->
                                    <td>
                                        <code class="small">{{ $import->product_sku ?: 'N/A' }}</code>
                                    </td>

                                    <!-- Price -->
                                    <td>
                                        <div class="text-dark fw-medium">
                                            ${{ number_format(floatval($import->import_price), 2) }}
                                        </div>
                                        @if($import->original_price != $import->import_price)
                                        <small class="text-muted">
                                            Original: ${{ number_format(floatval($import->original_price), 2) }}
                                        </small>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        @if($import->status == 'imported')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Imported
                                        </span>
                                        @elseif($import->status == 'processing')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Processing
                                        </span>
                                        @elseif($import->status == 'failed')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Failed
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">{{ ucfirst($import->status) }}</span>
                                        @endif
                                    </td>

                                    <!-- Import Date -->
                                    <td>
                                        <div class="small text-dark">
                                            {{ \Carbon\Carbon::parse($import->imported_at)->format('M j, Y') }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ \Carbon\Carbon::parse($import->imported_at)->format('g:i A') }}
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="viewImportDetails({{ $import->id }})">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </li>
                                                @if($import->status == 'imported')
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="manageProduct({{ $import->id }})">
                                                        <i class="fas fa-cog"></i> Manage Product
                                                    </a>
                                                </li>
                                                @endif
                                                @if($import->status == 'failed')
                                                <li>
                                                    <a class="dropdown-item text-primary" href="#" onclick="retryImport({{ $import->id }})">
                                                        <i class="fas fa-redo"></i> Retry Import
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="removeImport({{ $import->id }})">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $imports->links() }}
            </div>
            @else
            <!-- No Import History -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-history fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted">No Import History</h4>
                <p class="text-muted">You haven't imported any products yet.</p>
                <a href="{{ route('dropshipping.products.all') }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Start Importing Products
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Details Modal -->
<div class="modal fade" id="importDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="importDetailsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function viewImportDetails(importId) {
        $('#importDetailsModal').modal('show');
        $('#importDetailsContent').html(`
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        // For now, show placeholder content
        setTimeout(() => {
            $('#importDetailsContent').html(`
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Import details feature is coming soon!
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Import ID:</strong> ${importId}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> Imported
                    </div>
                </div>
            `);
        }, 1000);
    }

    function manageProduct(importId) {
        if (typeof toastr !== 'undefined') {
            toastr.info('Product management feature is coming soon!');
        } else {
            alert('Product management feature is coming soon!');
        }
    }

    function retryImport(importId) {
        if (confirm('Are you sure you want to retry importing this product?')) {
            if (typeof toastr !== 'undefined') {
                toastr.info('Retry import feature is coming soon!');
            } else {
                alert('Retry import feature is coming soon!');
            }
        }
    }

    function removeImport(importId) {
        if (confirm('Are you sure you want to remove this import record? This action cannot be undone.')) {
            if (typeof toastr !== 'undefined') {
                toastr.info('Remove import feature is coming soon!');
            } else {
                alert('Remove import feature is coming soon!');
            }
        }
    }
</script>
@endpush