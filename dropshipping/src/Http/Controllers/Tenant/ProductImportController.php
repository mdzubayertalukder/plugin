<?php

namespace Plugin\Dropshipping\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugin\Dropshipping\Models\DropshippingProduct;
use Plugin\Dropshipping\Models\ProductImportHistory;
use Plugin\Dropshipping\Models\DropshippingPlanLimit;
use Plugin\Dropshipping\Services\ProductImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductImportController extends Controller
{
    protected $importService;

    public function __construct(ProductImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show available products for import
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $priceMin = $request->get('price_min');
        $priceMax = $request->get('price_max');
        $stockStatus = $request->get('stock_status');

        $query = DropshippingProduct::with('woocommerceConfig')
            ->published()
            ->inStock();

        // Apply filters
        if ($search) {
            $query->search($search);
        }

        if ($category) {
            $query->whereJsonContains('categories', ['id' => $category]);
        }

        if ($priceMin) {
            $query->where('price', '>=', $priceMin);
        }

        if ($priceMax) {
            $query->where('price', '<=', $priceMax);
        }

        if ($stockStatus) {
            $query->where('stock_status', $stockStatus);
        }

        $products = $query->paginate(12);

        // Get available categories
        $categories = $this->getAvailableCategories();

        // Get current tenant limits
        $tenantLimits = $this->getTenantImportLimits();

        return view('dropshipping::tenant.import.products', compact(
            'products',
            'categories',
            'tenantLimits',
            'search',
            'category',
            'priceMin',
            'priceMax',
            'stockStatus'
        ));
    }

    /**
     * Import single product
     */
    public function importSingle(Request $request, $productId)
    {
        try {
            $product = DropshippingProduct::findOrFail($productId);
            $markupPercentage = $request->get('markup_percentage', 20);

            // Check import limits
            $limitsCheck = $this->checkImportLimits(1);
            if (!$limitsCheck['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $limitsCheck['message']
                ]);
            }

            // Check if already imported
            $existingImport = ProductImportHistory::where('tenant_id', tenant('id'))
                ->where('dropshipping_product_id', $productId)
                ->where('status', 'completed')
                ->first();

            if ($existingImport) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product has already been imported'
                ]);
            }

            // Create import history record
            $importHistory = ProductImportHistory::create([
                'tenant_id' => tenant('id'),
                'woocommerce_config_id' => $product->woocommerce_config_id,
                'dropshipping_product_id' => $productId,
                'import_type' => 'single',
                'status' => 'processing',
                'pricing_adjustments' => [
                    'markup_percentage' => $markupPercentage,
                    'original_price' => $product->price,
                    'final_price' => $product->price * (1 + $markupPercentage / 100)
                ],
                'imported_by' => auth()->id()
            ]);

            // Import the product
            $localProductId = $this->importService->importProduct($product, $markupPercentage);

            // Update import history
            $importHistory->markAsCompleted($localProductId);

            return response()->json([
                'success' => true,
                'message' => 'Product imported successfully',
                'local_product_id' => $localProductId
            ]);
        } catch (\Exception $e) {
            Log::error('Product import failed: ' . $e->getMessage());

            if (isset($importHistory)) {
                $importHistory->markAsFailed($e->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import multiple products (bulk import)
     */
    public function importBulk(Request $request)
    {
        $productIds = $request->get('product_ids', []);
        $markupPercentage = $request->get('markup_percentage', 20);

        if (empty($productIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No products selected for import'
            ]);
        }

        // Check import limits
        $limitsCheck = $this->checkImportLimits(count($productIds));
        if (!$limitsCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitsCheck['message']
            ]);
        }

        try {
            $results = [
                'successful' => 0,
                'failed' => 0,
                'skipped' => 0,
                'errors' => []
            ];

            foreach ($productIds as $productId) {
                try {
                    $product = DropshippingProduct::find($productId);

                    if (!$product) {
                        $results['failed']++;
                        $results['errors'][] = "Product ID {$productId} not found";
                        continue;
                    }

                    // Check if already imported
                    $existingImport = ProductImportHistory::where('tenant_id', tenant('id'))
                        ->where('dropshipping_product_id', $productId)
                        ->where('status', 'completed')
                        ->first();

                    if ($existingImport) {
                        $results['skipped']++;
                        continue;
                    }

                    // Create import history
                    $importHistory = ProductImportHistory::create([
                        'tenant_id' => tenant('id'),
                        'woocommerce_config_id' => $product->woocommerce_config_id,
                        'dropshipping_product_id' => $productId,
                        'import_type' => 'bulk',
                        'status' => 'processing',
                        'pricing_adjustments' => [
                            'markup_percentage' => $markupPercentage,
                            'original_price' => $product->price,
                            'final_price' => $product->price * (1 + $markupPercentage / 100)
                        ],
                        'imported_by' => auth()->id()
                    ]);

                    // Import the product
                    $localProductId = $this->importService->importProduct($product, $markupPercentage);
                    $importHistory->markAsCompleted($localProductId);

                    $results['successful']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Product ID {$productId}: " . $e->getMessage();

                    if (isset($importHistory)) {
                        $importHistory->markAsFailed($e->getMessage());
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk import completed. {$results['successful']} successful, {$results['failed']} failed, {$results['skipped']} skipped",
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk import failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show import history
     */
    public function history()
    {
        $imports = ProductImportHistory::with(['dropshippingProduct', 'woocommerceConfig', 'localProduct'])
            ->where('tenant_id', tenant('id'))
            ->latest()
            ->paginate(15);

        return view('dropshipping::tenant.import.history', compact('imports'));
    }

    /**
     * Show import limits for current tenant
     */
    public function limits()
    {
        $tenantLimits = $this->getTenantImportLimits();

        return response()->json([
            'success' => true,
            'limits' => $tenantLimits
        ]);
    }

    /**
     * Check import limits for tenant
     */
    public function checkImportLimit(Request $request)
    {
        $quantity = $request->get('quantity', 1);
        $result = $this->checkImportLimits($quantity);

        return response()->json($result);
    }

    /**
     * Preview import data
     */
    public function previewImport(Request $request)
    {
        $productIds = $request->get('product_ids', []);
        $markupPercentage = $request->get('markup_percentage', 20);

        $products = DropshippingProduct::whereIn('id', $productIds)->get();

        $preview = $products->map(function ($product) use ($markupPercentage) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'original_price' => $product->price,
                'markup_percentage' => $markupPercentage,
                'final_price' => $product->price * (1 + $markupPercentage / 100),
                'sku' => $product->sku,
                'stock_status' => $product->stock_status
            ];
        });

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'total_products' => count($productIds),
            'estimated_cost' => $preview->sum('final_price')
        ]);
    }

    /**
     * Get available categories
     */
    protected function getAvailableCategories()
    {
        $categories = DropshippingProduct::select('categories')
            ->whereNotNull('categories')
            ->get()
            ->pluck('categories')
            ->flatten(1)
            ->unique('id')
            ->values()
            ->toArray();

        return $categories;
    }

    /**
     * Get tenant import limits
     */
    protected function getTenantImportLimits()
    {
        // Get tenant's package
        $tenantAccount = \Plugin\Saas\Models\SaasAccount::where('id', tenant('id'))->first();

        if (!$tenantAccount) {
            return [
                'monthly_limit' => 0,
                'monthly_used' => 0,
                'monthly_remaining' => 0,
                'total_limit' => 0,
                'total_used' => 0,
                'total_remaining' => 0
            ];
        }

        $planLimit = DropshippingPlanLimit::where('package_id', $tenantAccount->package_id)->first();

        if (!$planLimit) {
            return [
                'monthly_limit' => 0,
                'monthly_used' => 0,
                'monthly_remaining' => 0,
                'total_limit' => 0,
                'total_used' => 0,
                'total_remaining' => 0
            ];
        }

        $monthlyUsed = ProductImportHistory::forTenant(tenant('id'))
            ->successful()
            ->whereMonth('imported_at', now()->month)
            ->whereYear('imported_at', now()->year)
            ->count();

        $totalUsed = ProductImportHistory::forTenant(tenant('id'))
            ->successful()
            ->count();

        return [
            'monthly_limit' => $planLimit->monthly_import_limit,
            'monthly_used' => $monthlyUsed,
            'monthly_remaining' => $planLimit->getRemainingMonthlyImports(tenant('id')),
            'total_limit' => $planLimit->total_import_limit,
            'total_used' => $totalUsed,
            'total_remaining' => $planLimit->getRemainingTotalImports(tenant('id')),
            'bulk_limit' => $planLimit->bulk_import_limit,
            'auto_sync_enabled' => $planLimit->auto_sync_enabled
        ];
    }

    /**
     * Check if import is allowed based on limits
     */
    protected function checkImportLimits(int $quantity): array
    {
        $tenantAccount = \Plugin\Saas\Models\SaasAccount::where('id', tenant('id'))->first();

        if (!$tenantAccount) {
            return ['allowed' => false, 'message' => 'Tenant account not found'];
        }

        $planLimit = DropshippingPlanLimit::where('package_id', $tenantAccount->package_id)->first();

        if (!$planLimit) {
            return ['allowed' => false, 'message' => 'No import limits configured for your plan'];
        }

        // Check monthly limit
        if ($planLimit->isMonthlyLimitReached(tenant('id'))) {
            return ['allowed' => false, 'message' => 'Monthly import limit reached'];
        }

        // Check total limit
        if ($planLimit->isTotalLimitReached(tenant('id'))) {
            return ['allowed' => false, 'message' => 'Total import limit reached'];
        }

        // Check bulk import limit
        if ($quantity > 1 && !$planLimit->canBulkImport($quantity)) {
            return ['allowed' => false, 'message' => "Bulk import limit exceeded. Maximum allowed: {$planLimit->bulk_import_limit}"];
        }

        // Check remaining monthly quota
        $remainingMonthly = $planLimit->getRemainingMonthlyImports(tenant('id'));
        if ($remainingMonthly !== -1 && $quantity > $remainingMonthly) {
            return ['allowed' => false, 'message' => "Insufficient monthly quota. Remaining: {$remainingMonthly}"];
        }

        return ['allowed' => true, 'message' => 'Import allowed'];
    }
}
