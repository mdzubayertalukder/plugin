@extends('core::base.layouts.master')

@section('title')
{{ translate('Dropshipping Products') }}
@endsection

@section('main_content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 font-weight-bold text-dark mb-1">Dropshipping Products</h2>
                    <p class="text-muted">Browse and import products from our partner stores</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dropshipping.import.history') }}" class="btn btn-outline-primary">
                        <i class="fas fa-history"></i> Import History
                    </a>
                </div>
            </div>

            <!-- Store Filter -->
            @if($stores && count($stores) > 0)
            <div class="card mb-4">
                <div class="card-body py-3">
                    <form method="GET" class="d-flex align-items-center gap-3">
                        <label for="store_id" class="form-label mb-0 text-muted">Filter by Store:</label>
                        <select name="store_id" id="store_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <option value="">All Stores</option>
                            @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                            @endforeach
                        </select>
                        @if($selectedStore)
                        <a href="{{ route('dropshipping.products.all') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear Filter
                        </a>
                        @endif
                    </form>
                </div>
            </div>
            @endif

            <!-- Products Grid -->
            @if($products && count($products) > 0)
            <div class="row">
                @foreach($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 product-card">
                        <!-- Product Image -->
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            @if(isset($product->image) && $product->image)
                            <img src="{{ $product->image }}"
                                class="card-img-top h-100 w-100"
                                style="object-fit: cover;"
                                alt="{{ $product->name }}"
                                onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                            @else
                            <div class="card-img-top h-100 w-100 d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image text-muted fa-3x"></i>
                            </div>
                            @endif

                            <!-- Store Badge -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-primary">{{ $product->store_name ?? 'Store' }}</span>
                            </div>

                            <!-- Stock Status -->
                            @if($product->stock_quantity > 0)
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success">In Stock</span>
                            </div>
                            @else
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-warning">Out of Stock</span>
                            </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="card-body d-flex flex-column">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-2 text-dark" title="{{ $product->name }}">
                                    {{ Str::limit($product->name, 60) }}
                                </h6>

                                @if($product->short_description)
                                <p class="card-text text-muted small mb-2">
                                    {{ Str::limit(strip_tags($product->short_description), 80) }}
                                </p>
                                @endif

                                <!-- Price -->
                                <div class="d-flex align-items-center mb-2">
                                    @if($product->sale_price && $product->sale_price != $product->regular_price)
                                    <span class="h6 text-danger mb-0 me-2">${{ number_format(floatval($product->sale_price), 2) }}</span>
                                    <span class="text-muted text-decoration-line-through small">${{ number_format(floatval($product->regular_price), 2) }}</span>
                                    @else
                                    <span class="h6 text-dark mb-0">${{ number_format(floatval($product->regular_price), 2) }}</span>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>SKU: {{ $product->sku ?: 'N/A' }}</span>
                                        <span>Stock: {{ $product->stock_quantity ?: '0' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Import Button -->
                            <div class="mt-3">
                                <form method="POST" action="{{ route('dropshipping.import.product', $product->id) }}" class="import-form">
                                    @csrf
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm import-btn"
                                            {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-download"></i> Import Product
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="showProductDetails({{ $product->id }})">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @else
            <!-- No Products -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted">No Products Available</h4>
                <p class="text-muted">There are no products available for import at the moment.</p>
                @if(!$stores || count($stores) == 0)
                <p class="text-muted small">Please ask your administrator to set up WooCommerce stores.</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="productDetailsContent">
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
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .import-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .badge {
        font-size: 0.7rem;
    }

    .card-title {
        line-height: 1.3;
        height: 2.6em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>
@endpush

@push('scripts')
<script>
    // Handle import form submission
    $(document).on('submit', '.import-form', function(e) {
        e.preventDefault();

        let form = $(this);
        let btn = form.find('.import-btn');
        let originalText = btn.html();

        // Show loading state
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importing...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Show success message
                    btn.removeClass('btn-primary').addClass('btn-success')
                        .html('<i class="fas fa-check"></i> Imported!');

                    // Show success notification
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Product imported successfully!');
                    } else {
                        alert('Product imported successfully!');
                    }

                    // Reset button after 3 seconds
                    setTimeout(() => {
                        btn.removeClass('btn-success').addClass('btn-primary')
                            .html(originalText).prop('disabled', false);
                    }, 3000);
                } else {
                    // Show error
                    btn.prop('disabled', false).html(originalText);
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Import failed!');
                    } else {
                        alert(response.message || 'Import failed!');
                    }
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                let message = 'Import failed!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
            }
        });
    });

    // Show product details modal
    function showProductDetails(productId) {
        $('#productDetailsModal').modal('show');
        $('#productDetailsContent').html(`
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        // Here you can load product details via AJAX if needed
        // For now, we'll show a placeholder
        setTimeout(() => {
            $('#productDetailsContent').html(`
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Product details feature is coming soon! For now, you can import the product directly.
                </div>
            `);
        }, 1000);
    }
</script>
@endpush