{{-- Dropshipping Plugin Dashboard Widget --}}
@if(isActivePluging('dropshipping') && function_exists('isTenant') && isTenant())
<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
    <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        {{ translate('Available Products') }}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        @php
                        $availableProducts = 0;
                        try {
                        $availableProducts = \Plugin\Dropshipping\Models\DropshippingProduct::count();
                        } catch (\Exception $e) {
                        // Silent fail if database not accessible
                        }
                        @endphp
                        {{ number_format($availableProducts) }}
                    </div>
                </div>
                <div class="col-auto">
                    <i class="icofont-truck fa-2x text-gray-300"></i>
                </div>
            </div>
            <div class="row no-gutters align-items-center mt-2">
                <div class="col">
                    @if(Route::has('dropshipping.products'))
                    <a href="{{ route('dropshipping.products') }}" class="btn btn-info btn-sm">
                        {{ translate('Browse Products') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
    <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        {{ translate('Imported Products') }}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        @php
                        $importedProducts = 0;
                        try {
                        if (function_exists('tenant') && tenant('id')) {
                        $importedProducts = \Plugin\Dropshipping\Models\ProductImportHistory::where('tenant_id', tenant('id'))->count();
                        }
                        } catch (\Exception $e) {
                        // Silent fail if database not accessible
                        }
                        @endphp
                        {{ number_format($importedProducts) }}
                    </div>
                </div>
                <div class="col-auto">
                    <i class="icofont-download fa-2x text-gray-300"></i>
                </div>
            </div>
            <div class="row no-gutters align-items-center mt-2">
                <div class="col">
                    @if(Route::has('dropshipping.import.history'))
                    <a href="{{ route('dropshipping.import.history') }}" class="btn btn-warning btn-sm">
                        {{ translate('View History') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif