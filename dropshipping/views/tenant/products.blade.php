@extends('core::base.layouts.master')

@section('title')
{{ translate('Browse Products') }}
@endsection

@section('custom_css')
<style>
    .product-card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        transition: all 0.3s ease;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        height: 200px;
        object-fit: cover;
        border-radius: 10px 10px 0 0;
    }

    .product-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #28a745;
    }

    .import-btn {
        width: 100%;
    }

    .store-selector {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .limits-info {
        background: #f8f9fc;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('main_content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4><i class="icofont-search-product"></i> {{ translate('Browse Products') }}</h4>
                <p class="text-muted">{{ translate('Browse and import products from dropshipping stores') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Store Selector --}}
<div class="row">
    <div class="col-12">
        <div class="store-selector">
            <form method="GET" action="{{ route('dropshipping.products') }}">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <label class="mb-2"><strong>{{ translate('Select Store') }}</strong></label>
                        <select name="store_id" class="form-control" onchange="this.form.submit()">
                            <option value="">{{ translate('Select a store...') }}</option>
                            @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <div>
                            <i class="icofont-package"></i>
                            <span>{{ $stores->count() }} {{ translate('stores available') }}</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import Limits Info --}}
@if(isset($limits) && $limits)
<div class="row">
    <div class="col-12">
        <div class="limits-info">
            <div class="row">
                <div class="col-md-3">
                    <strong>{{ translate('Daily Limit') }}:</strong>
                    <span class="text-muted">{{ $limits['daily_remaining'] ?? 'N/A' }} / {{ $limits['daily_limit'] ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>{{ translate('Monthly Limit') }}:</strong>
                    <span class="text-muted">{{ $limits['monthly_remaining'] ?? 'N/A' }} / {{ $limits['monthly_limit'] ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>{{ translate('Total Limit') }}:</strong>
                    <span class="text-muted">{{ $limits['total_remaining'] ?? 'N/A' }} / {{ $limits['total_limit'] ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <div class="text-right">
                        @if(Route::has('dropshipping.import.limits'))
                        <a href="{{ route('dropshipping.import.limits') }}" class="btn btn-sm btn-outline-primary">
                            {{ translate('View Details') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Products Grid --}}
@if($selectedStore && $products->count() > 0)
<div class="row">
    @foreach($products as $product)
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
        <div class="card product-card">
            {{-- Product Image --}}
            <div class="position-relative">
                @php
                $images = json_decode($product->images ?? '[]', true);
                $firstImage = $images[0]['src'] ?? null;
                @endphp
                @if($firstImage)
                <img src="{{ $firstImage }}" alt="{{ $product->name }}" class="card-img-top product-image">
                @else
                <div class="card-img-top product-image bg-light d-flex align-items-center justify-content-center">
                    <i class="icofont-image text-muted" style="font-size: 3rem;"></i>
                </div>
                @endif

                {{-- Featured Badge --}}
                @if($product->featured)
                <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px;">
                    {{ translate('Featured') }}
                </span>
                @endif

                {{-- Stock Status --}}
                <span class="badge position-absolute" style="top: 10px; left: 10px; background-color: {{ $product->stock_status == 'instock' ? '#28a745' : '#dc3545' }};">
                    {{ $product->stock_status == 'instock' ? translate('In Stock') : translate('Out of Stock') }}
                </span>
            </div>

            <div class="card-body d-flex flex-column">
                {{-- Product Title --}}
                <h6 class="card-title" title="{{ $product->name }}">
                    {{ Str::limit($product->name, 60) }}
                </h6>

                {{-- Product Price --}}
                <div class="product-price mb-2">
                    @if($product->sale_price && $product->sale_price != $product->regular_price)
                    <span class="text-muted"><del>${{ number_format($product->regular_price, 2) }}</del></span>
                    <span class="text-success">${{ number_format($product->sale_price, 2) }}</span>
                    @else
                    ${{ number_format($product->price ?: $product->regular_price, 2) }}
                    @endif
                </div>

                {{-- Product Details --}}
                <div class="text-muted small mb-3">
                    @if($product->sku)
                    <div><strong>{{ translate('SKU') }}:</strong> {{ $product->sku }}</div>
                    @endif
                    @if($product->stock_quantity)
                    <div><strong>{{ translate('Stock') }}:</strong> {{ $product->stock_quantity }}</div>
                    @endif
                </div>

                {{-- Import Button --}}
                <div class="mt-auto">
                    @if($product->stock_status == 'instock')
                    <button type="button"
                        class="btn btn-primary import-btn"
                        onclick="importProduct({{ $product->id }})"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}">
                        <i class="icofont-download"></i> {{ translate('Import Product') }}
                    </button>
                    @else
                    <button type="button" class="btn btn-secondary import-btn" disabled>
                        {{ translate('Out of Stock') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $products->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@elseif($selectedStore && $products->count() == 0)
{{-- No Products Found --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icofont-search-document" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">{{ translate('No Products Found') }}</h4>
                <p class="text-muted">{{ translate('No products available in the selected store') }}</p>
            </div>
        </div>
    </div>
</div>

@else
{{-- No Store Selected --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icofont-store" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">{{ translate('Select a Store') }}</h4>
                <p class="text-muted">{{ translate('Please select a dropshipping store to browse products') }}</p>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">{{ translate('Import Product') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ translate('Are you sure you want to import this product?') }}</p>
                <div id="productInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirmImport">{{ translate('Import') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
    let currentProductId = null;

    function importProduct(productId) {
        currentProductId = productId;
        const productName = $(`[data-product-id="${productId}"]`).data('product-name');

        $('#productInfo').html(`<strong>${productName}</strong>`);
        $('#importModal').modal('show');
    }

    $('#confirmImport').click(function() {
        if (!currentProductId) return;

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ translate("Importing...") }}');

        $.ajax({
            url: `{{ route('dropshipping.import.single', ':id') }}`.replace(':id', currentProductId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#importModal').modal('hide');

                    // Update button state
                    $(`[data-product-id="${currentProductId}"]`)
                        .removeClass('btn-primary')
                        .addClass('btn-success')
                        .html('<i class="icofont-check"></i> {{ translate("Imported") }}')
                        .prop('disabled', true);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('{{ translate("Import failed. Please try again.") }}');
            },
            complete: function() {
                $('#confirmImport').prop('disabled', false).html('{{ translate("Import") }}');
                currentProductId = null;
            }
        });
    });

    // Toast notification function fallback
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