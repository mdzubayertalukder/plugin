@extends('core::base.layouts.master')

@section('title')
{{ translate('My Products') }}
@endsection

@section('main_content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 font-weight-bold text-dark mb-1">My Products</h2>
                    <p class="text-muted">Products imported from dropshipping stores</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dropshipping.products') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Import More Products
                    </a>
                    <a href="{{ route('dropshipping.import.history') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-history"></i> Import History
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Products</h6>
                                    <h3 class="mb-0">{{ $stats['total_products'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-box fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">This Month</h6>
                                    <h3 class="mb-0">{{ $stats['this_month'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Value</h6>
                                    <h3 class="mb-0">${{ number_format($stats['total_value'] ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dropshipping.my.products') }}">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Search products..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    @if(request('search'))
                                    <a href="{{ route('dropshipping.my.products') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products List -->
            @if($localProducts && count($localProducts) > 0)
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Purchase Price</th>
                                    <th>Selling Price</th>
                                    <th>Markup</th>
                                    <th>Stock</th>
                                    <th>Store</th>
                                    <th>Imported</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($localProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->thumbnail_image)
                                            <img src="{{ $product->thumbnail_image }}"
                                                alt="{{ $product->name }}"
                                                class="rounded me-2"
                                                style="width: 40px; height: 40px; object-fit: cover;"
                                                onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                                            @else
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="font-weight-medium">{{ $product->name }}</div>
                                                @if($product->summary)
                                                <small class="text-muted">{{ Str::limit(strip_tags($product->summary), 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $product->sku }}</code>
                                    </td>
                                    <td>
                                        <span class="text-muted">${{ number_format($product->purchase_price ?? 0, 2) }}</span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($product->unit_price ?? 0, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($product->markup_percentage > 0)
                                        <span class="badge bg-success">+{{ $product->markup_percentage }}%</span>
                                        @else
                                        <span class="badge bg-secondary">0%</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->stock_quantity > 0)
                                        <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                        @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $product->store_name ?? 'Unknown' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $product->imported_at_formatted }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="editProduct({{ $product->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="viewProduct({{ $product->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($localProducts->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $localProducts->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- No Products -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-box-open fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted">No Products Found</h4>
                    @if(request('search'))
                    <p class="text-muted">No products match your search criteria.</p>
                    <a href="{{ route('dropshipping.my.products') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear Search
                    </a>
                    @else
                    <p class="text-muted">You haven't imported any products yet.</p>
                    <a href="{{ route('dropshipping.products') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Import Your First Product
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Product Edit Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editProductContent">
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

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .opacity-75 {
        opacity: 0.75;
    }
</style>
@endpush

@push('scripts')
<script>
    function editProduct(productId) {
        $('#editProductModal').modal('show');

        // Load product edit form via AJAX
        setTimeout(() => {
            $('#editProductContent').html(`
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Product editing feature is coming soon! For now, you can manage products through the main products section.
                </div>
            `);
        }, 1000);
    }

    function viewProduct(productId) {
        // You can implement product view functionality here
        alert('Product view feature coming soon!');
    }
</script>
@endpush