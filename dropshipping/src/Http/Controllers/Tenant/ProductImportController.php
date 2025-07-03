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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $tenantId = tenant('id');

        // Get available WooCommerce stores
        $stores = DB::table('dropshipping_woocommerce_configs')
            ->where('is_active', 1)
            ->get();

        $selectedStore = $request->get('store_id', $stores->first()->id ?? null);

        $products = collect();
        if ($selectedStore) {
            $products = DB::table('dropshipping_products')
                ->where('woocommerce_config_id', $selectedStore)
                ->where('status', 'publish')
                ->when($request->get('search'), function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })
                ->when($request->get('category'), function ($query, $category) {
                    return $query->where('categories', 'like', '%' . $category . '%');
                })
                ->paginate(20);
        }

        // Get import limits for this tenant
        $limits = $this->getTenantImportLimits($tenantId);

        return view('plugin/dropshipping::tenant.import.products', compact(
            'stores',
            'selectedStore',
            'products',
            'limits'
        ));
    }

    /**
     * Import a single product (simplified - no database tracking)
     */
    public function importSingle(Request $request, $productId)
    {
        try {
            $userId = Auth::id();

            // Get the product from dropshipping products
            $product = DB::table('dropshipping_products')
                ->where('id', $productId)
                ->where('status', 'publish')
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found or not available for import.'
                ], 404);
            }

            // For now, just return success message
            // You can implement actual product creation logic here later

            return response()->json([
                'success' => true,
                'message' => 'Product import initiated! (Feature coming soon)',
                'product_name' => $product->name,
                'product_price' => $product->regular_price
            ]);
        } catch (\Exception $e) {
            Log::error('Product Import Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to import product. Please try again later.'
            ], 500);
        }
    }

    /**
     * Import multiple products in bulk
     */
    public function importBulk(Request $request)
    {
        $tenantId = tenant('id');

        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:dropshipping_products,id',
            'markup_percentage' => 'nullable|numeric|min:0|max:1000',
            'fixed_markup' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $productIds = $request->get('product_ids');
            $limits = $this->getTenantImportLimits($tenantId);

            // Check bulk import limit
            if (count($productIds) > $limits['bulk_limit'] && $limits['bulk_limit'] > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulk import limit exceeded. Maximum: ' . $limits['bulk_limit']
                ]);
            }

            // Check monthly limit
            if (($limits['monthly_used'] + count($productIds)) > $limits['monthly_limit'] && $limits['monthly_limit'] > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Monthly import limit would be exceeded'
                ]);
            }

            $importSettings = [
                'markup_percentage' => $request->get('markup_percentage', 0),
                'fixed_markup' => $request->get('fixed_markup', 0),
                'import_reviews' => $request->boolean('import_reviews'),
                'import_gallery' => $request->boolean('import_gallery'),
            ];

            $importedCount = 0;
            $errors = [];

            foreach ($productIds as $productId) {
                try {
                    // Get product details
                    $product = DB::table('dropshipping_products')->where('id', $productId)->first();
                    if (!$product) {
                        $errors[] = "Product ID {$productId} not found";
                        continue;
                    }

                    // Check if already imported
                    $existingImport = DB::table('dropshipping_product_import_history')
                        ->where('tenant_id', $tenantId)
                        ->where('dropshipping_product_id', $productId)
                        ->where('status', 'completed')
                        ->first();

                    if ($existingImport) {
                        $errors[] = "Product '{$product->name}' already imported";
                        continue;
                    }

                    // Create import record
                    $importId = DB::table('dropshipping_product_import_history')->insertGetId([
                        'tenant_id' => $tenantId,
                        'woocommerce_config_id' => $product->woocommerce_config_id,
                        'dropshipping_product_id' => $productId,
                        'import_type' => 'bulk',
                        'status' => 'completed',
                        'import_settings' => json_encode($importSettings),
                        'imported_at' => now(),
                        'imported_by' => Auth::id(),
                        'imported_data' => json_encode([
                            'product_name' => $product->name,
                            'original_price' => $product->price,
                            'import_time' => now(),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to import product ID {$productId}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} products",
                'imported_count' => $importedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk import failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show import history (simplified - placeholder)
     */
    public function history(Request $request)
    {
        // For now, show empty history
        $imports = collect();

        return view('plugin/dropshipping::tenant.import-history', compact('imports'));
    }

    /**
     * Show import limits for tenant
     */
    public function limits()
    {
        $tenantId = tenant('id');
        $limits = $this->getTenantImportLimits($tenantId);

        return view('plugin/dropshipping::tenant.import.limits', compact('limits'));
    }

    /**
     * Check import limit via AJAX
     */
    public function checkImportLimit()
    {
        $tenantId = tenant('id');
        $limits = $this->getTenantImportLimits($tenantId);

        return response()->json([
            'success' => true,
            'limits' => $limits
        ]);
    }

    /**
     * Preview import settings
     */
    public function previewImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:dropshipping_products,id',
            'markup_percentage' => 'nullable|numeric|min:0|max:1000',
            'fixed_markup' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $product = DB::table('dropshipping_products')
                ->where('id', $request->product_id)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }

            $originalPrice = (float) $product->price;
            $markupPercentage = (float) $request->get('markup_percentage', 0);
            $fixedMarkup = (float) $request->get('fixed_markup', 0);

            $finalPrice = $originalPrice;
            if ($markupPercentage > 0) {
                $finalPrice += ($originalPrice * $markupPercentage / 100);
            }
            $finalPrice += $fixedMarkup;

            return response()->json([
                'success' => true,
                'preview' => [
                    'original_price' => number_format($originalPrice, 2),
                    'markup_percentage' => $markupPercentage,
                    'fixed_markup' => number_format($fixedMarkup, 2),
                    'final_price' => number_format($finalPrice, 2),
                    'profit_margin' => number_format($finalPrice - $originalPrice, 2),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get tenant import limits
     */
    private function getTenantImportLimits($tenantId)
    {
        // This would typically get the limits based on the tenant's subscription package
        // For now, return default limits
        return [
            'monthly_limit' => 100,
            'monthly_used' => DB::table('dropshipping_product_import_history')
                ->where('tenant_id', $tenantId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_limit' => -1, // unlimited
            'bulk_limit' => 20,
        ];
    }

    /**
     * Log import activity
     */
    private function logImportActivity($userId, $productId, $action, $data = [])
    {
        try {
            DB::table('dropshipping_import_logs')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'action' => $action,
                'data' => json_encode($data),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log import activity: ' . $e->getMessage());
        }
    }

    /**
     * Get import statistics for dashboard
     */
    public function getImportStats($userId)
    {
        try {
            $stats = [
                'total_imported' => DB::table('dropshipping_imported_products')
                    ->where('user_id', $userId)
                    ->count(),

                'imported_today' => DB::table('dropshipping_imported_products')
                    ->where('user_id', $userId)
                    ->whereDate('imported_at', today())
                    ->count(),

                'imported_this_month' => DB::table('dropshipping_imported_products')
                    ->where('user_id', $userId)
                    ->whereYear('imported_at', now()->year)
                    ->whereMonth('imported_at', now()->month)
                    ->count(),

                'total_value' => DB::table('dropshipping_imported_products')
                    ->where('user_id', $userId)
                    ->sum('import_price') ?: 0
            ];

            return $stats;
        } catch (\Exception $e) {
            return [
                'total_imported' => 0,
                'imported_today' => 0,
                'imported_this_month' => 0,
                'total_value' => 0
            ];
        }
    }
}
