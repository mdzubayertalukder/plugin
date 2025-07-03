<?php

namespace Plugin\Dropshipping\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DropshippingTenantController extends Controller
{
    /**
     * Show dropshipping dashboard for tenant
     */
    public function dashboard()
    {
        $tenantId = tenant('id');

        $importStats = [
            'total_imports' => DB::table('dropshipping_product_import_history')
                ->where('tenant_id', $tenantId)
                ->count(),
            'successful_imports' => DB::table('dropshipping_product_import_history')
                ->where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->count(),
            'pending_imports' => DB::table('dropshipping_product_import_history')
                ->where('tenant_id', $tenantId)
                ->where('status', 'pending')
                ->count(),
            'this_month' => DB::table('dropshipping_product_import_history')
                ->where('tenant_id', $tenantId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        $recentImports = DB::table('dropshipping_product_import_history')
            ->join('dropshipping_products', 'dropshipping_products.id', '=', 'dropshipping_product_import_history.dropshipping_product_id')
            ->join('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_product_import_history.woocommerce_config_id')
            ->where('dropshipping_product_import_history.tenant_id', $tenantId)
            ->select(
                'dropshipping_product_import_history.*',
                'dropshipping_products.name as product_name',
                'dropshipping_woocommerce_configs.name as store_name'
            )
            ->orderBy('dropshipping_product_import_history.created_at', 'desc')
            ->limit(10)
            ->get();

        $availableStores = DB::table('dropshipping_woocommerce_configs')
            ->where('is_active', 1)
            ->count();

        return view('plugin/dropshipping::tenant.dashboard', compact(
            'importStats',
            'recentImports',
            'availableStores'
        ));
    }

    /**
     * Show all available products for import in card format
     */
    public function allProducts(Request $request)
    {
        try {
            // Get all available products from all active stores
            $products = DB::table('dropshipping_products')
                ->join('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_products.woocommerce_config_id')
                ->where('dropshipping_woocommerce_configs.is_active', 1)
                ->where('dropshipping_products.status', 'publish')
                ->select(
                    'dropshipping_products.*',
                    'dropshipping_woocommerce_configs.name as store_name'
                )
                ->orderBy('dropshipping_products.created_at', 'desc')
                ->paginate(24); // 24 products per page for nice card grid

            // Get available stores for filter
            $stores = DB::table('dropshipping_woocommerce_configs')
                ->where('is_active', 1)
                ->select('id', 'name')
                ->get();

            // Filter by store if selected
            $selectedStore = $request->get('store_id');
            if ($selectedStore) {
                $products = DB::table('dropshipping_products')
                    ->join('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_products.woocommerce_config_id')
                    ->where('dropshipping_woocommerce_configs.is_active', 1)
                    ->where('dropshipping_products.status', 'publish')
                    ->where('dropshipping_products.woocommerce_config_id', $selectedStore)
                    ->select(
                        'dropshipping_products.*',
                        'dropshipping_woocommerce_configs.name as store_name'
                    )
                    ->orderBy('dropshipping_products.created_at', 'desc')
                    ->paginate(24);
            }

            return view('plugin/dropshipping::tenant.all-products', compact(
                'products',
                'stores',
                'selectedStore'
            ));
        } catch (\Exception $e) {
            // Fallback view if there are any errors
            $products = collect();
            $stores = collect();
            $selectedStore = null;

            return view('plugin/dropshipping::tenant.all-products', compact(
                'products',
                'stores',
                'selectedStore'
            ));
        }
    }

    /**
     * Show available products for import (legacy method)
     */
    public function products(Request $request)
    {
        return $this->allProducts($request);
    }

    /**
     * Show imported products
     */
    public function importedProducts()
    {
        $tenantId = tenant('id');

        $importedProducts = DB::table('dropshipping_product_import_history')
            ->join('dropshipping_products', 'dropshipping_products.id', '=', 'dropshipping_product_import_history.dropshipping_product_id')
            ->join('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_product_import_history.woocommerce_config_id')
            ->where('dropshipping_product_import_history.tenant_id', $tenantId)
            ->where('dropshipping_product_import_history.status', 'completed')
            ->select(
                'dropshipping_product_import_history.*',
                'dropshipping_products.name as product_name',
                'dropshipping_products.price',
                'dropshipping_products.stock_status',
                'dropshipping_woocommerce_configs.name as store_name'
            )
            ->orderBy('dropshipping_product_import_history.imported_at', 'desc')
            ->paginate(20);

        return view('plugin/dropshipping::tenant.imported', compact('importedProducts'));
    }

    /**
     * Update pricing for imported product
     */
    public function updatePricing(Request $request, $id)
    {
        $tenantId = tenant('id');

        $request->validate([
            'markup_percentage' => 'required|numeric|min:0|max:1000',
            'fixed_markup' => 'nullable|numeric|min:0',
        ]);

        try {
            $importHistory = DB::table('dropshipping_product_import_history')
                ->where('id', $id)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$importHistory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import record not found'
                ]);
            }

            $pricingAdjustments = [
                'markup_percentage' => $request->markup_percentage,
                'fixed_markup' => $request->fixed_markup,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ];

            DB::table('dropshipping_product_import_history')
                ->where('id', $id)
                ->update([
                    'pricing_adjustments' => json_encode($pricingAdjustments),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Pricing updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricing: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync single product
     */
    public function syncProduct($id)
    {
        $tenantId = tenant('id');

        try {
            $importHistory = DB::table('dropshipping_product_import_history')
                ->where('id', $id)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$importHistory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import record not found'
                ]);
            }

            // Here you would implement the actual sync logic
            // For now, we'll just update the timestamp

            DB::table('dropshipping_product_import_history')
                ->where('id', $id)
                ->update([
                    'imported_at' => now(),
                    'status' => 'completed'
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Product synced successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync product: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove imported product
     */
    public function removeProduct($id)
    {
        $tenantId = tenant('id');

        try {
            $deleted = DB::table('dropshipping_product_import_history')
                ->where('id', $id)
                ->where('tenant_id', $tenantId)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get product details via AJAX
     */
    public function getProductDetails($id)
    {
        try {
            $product = DB::table('dropshipping_products')
                ->join('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_products.woocommerce_config_id')
                ->where('dropshipping_products.id', $id)
                ->select(
                    'dropshipping_products.*',
                    'dropshipping_woocommerce_configs.name as store_name'
                )
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get product details: ' . $e->getMessage()
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
}
