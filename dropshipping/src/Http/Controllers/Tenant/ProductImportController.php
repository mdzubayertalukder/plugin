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
use Illuminate\Support\Str;

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

        // Get available WooCommerce stores from main database
        $stores = DB::connection('mysql')->table('dropshipping_woocommerce_configs')
            ->where('is_active', 1)
            ->get();

        $selectedStore = $request->get('store_id', $stores->first()->id ?? null);

        $products = collect();
        if ($selectedStore) {
            $products = DB::connection('mysql')->table('dropshipping_products')
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
     * Import a single product
     */
    public function importSingle(Request $request, $productId)
    {
        try {
            $tenantId = tenant('id');
            $userId = Auth::id();

            // Get the product from dropshipping products (main database)
            $product = DB::connection('mysql')->table('dropshipping_products')
                ->where('id', $productId)
                ->where('status', 'publish')
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found or not available for import.'
                ], 404);
            }

            // Check if already imported
            $existingImport = DB::table('dropshipping_product_import_history')
                ->where('tenant_id', $tenantId)
                ->where('dropshipping_product_id', $productId)
                ->where('import_status', 'completed')
                ->first();

            if ($existingImport) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product has already been imported to your store.'
                ]);
            }

            // Get markup percentage from request (default 20%)
            $markupPercentage = $request->get('markup_percentage', 20);

            // Calculate pricing
            $originalPrice = $product->regular_price ?? $product->price;
            $finalPrice = $originalPrice * (1 + $markupPercentage / 100);
            $salePrice = null;

            if ($product->sale_price) {
                $salePrice = $product->sale_price * (1 + $markupPercentage / 100);
            }

            // Generate unique SKU
            $sku = $this->generateUniqueSku($product->sku ?? 'DS-' . $productId);

            // Extract first image
            $thumbnailImageId = null;
            if (!empty($product->images)) {
                $images = json_decode($product->images, true);
                if (is_array($images) && count($images) > 0) {
                    // For now, we'll store the image URL in the thumbnail_image field
                    // In a real system, you'd upload the image and get an ID
                    if (is_array($images[0])) {
                        $imageUrl = $images[0]['src'] ?? $images[0]['url'] ?? null;
                    } else {
                        $imageUrl = $images[0];
                    }
                    $thumbnailImageId = $imageUrl; // Store URL directly for now
                }
            }

            // Create product in local database
            $localProductId = DB::table('tl_com_products')->insertGetId([
                'name' => $product->name,
                'summary' => $product->short_description,
                'description' => $product->description,
                'permalink' => $this->generateUniqueSlug($product->slug ?? Str::slug($product->name)),
                'product_type' => 1, // Default product type
                'unit' => 15, // Default unit
                'conditions' => 8, // Default condition
                'has_variant' => 2, // No variant
                'discount_type' => 2, // No discount
                'thumbnail_image' => $thumbnailImageId,
                'is_featured' => 2, // Not featured
                'min_item_on_purchase' => 1,
                'low_stock_quantity_alert' => 1,
                'is_authentic' => 1,
                'has_warranty' => 2, // No warranty
                'has_replacement_warranty' => 2, // No replacement warranty
                'is_refundable' => 2, // Not refundable
                'is_active_cod' => 1, // COD active
                'is_active_free_shipping' => 2, // Free shipping not active
                'cod_location_type' => 'anywhere',
                'is_active_attatchment' => 2, // No attachment
                'shipping_cost' => 0,
                'is_apply_multiple_qty_shipping_cost' => 1,
                'is_enable_tax' => 2, // Tax not enabled
                'status' => 1, // Active
                'is_approved' => 1, // Approved
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create product pricing record
            DB::table('tl_com_single_product_price')->insert([
                'product_id' => $localProductId,
                'sku' => $sku,
                'purchase_price' => $originalPrice,
                'unit_price' => $finalPrice,
                'quantity' => $product->stock_quantity ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create import history record
            $importId = DB::table('dropshipping_product_import_history')->insertGetId([
                'tenant_id' => $tenantId,
                'woocommerce_store_id' => $product->woocommerce_config_id ?? 1,
                'woocommerce_config_id' => $product->woocommerce_config_id ?? 1,
                'woocommerce_product_id' => $product->woocommerce_product_id ?? $product->id,
                'dropshipping_product_id' => $productId,
                'local_product_id' => $localProductId,
                'import_type' => 'manual',
                'import_status' => 'completed',
                'imported_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product imported successfully to your store!',
                'product_name' => $product->name,
                'product_price' => number_format($finalPrice, 2),
                'original_price' => number_format($originalPrice, 2),
                'markup_applied' => $markupPercentage . '%',
                'local_product_id' => $localProductId,
                'sku' => $sku
            ]);
        } catch (\Exception $e) {
            Log::error('Product Import Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to import product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique SKU for the product
     */
    private function generateUniqueSku($baseSku)
    {
        $originalSku = $baseSku ?: 'DS-' . uniqid();
        $sku = $originalSku;
        $counter = 1;

        while (DB::table('tl_com_single_product_price')->where('sku', $sku)->exists()) {
            $sku = $originalSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }

    /**
     * Generate unique slug for the product
     */
    private function generateUniqueSlug($baseSlug)
    {
        $originalSlug = $baseSlug ?: Str::slug('product-' . uniqid());
        $slug = $originalSlug;
        $counter = 1;

        while (DB::table('tl_com_products')->where('permalink', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Import multiple products in bulk
     */
    public function importBulk(Request $request)
    {
        $tenantId = tenant('id');

        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer',
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
                    // Get product details (from main database)
                    $product = DB::connection('mysql')->table('dropshipping_products')->where('id', $productId)->first();
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
            'product_id' => 'required|integer',
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
            $product = DB::connection('mysql')->table('dropshipping_products')
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
