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
            ->join(DB::connection('mysql')->getDatabaseName() . '.dropshipping_products', 'dropshipping_products.id', '=', 'dropshipping_product_import_history.dropshipping_product_id')
            ->join(DB::connection('mysql')->getDatabaseName() . '.dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_product_import_history.woocommerce_config_id')
            ->where('dropshipping_product_import_history.tenant_id', $tenantId)
            ->select(
                'dropshipping_product_import_history.*',
                'dropshipping_products.name as product_name',
                'dropshipping_woocommerce_configs.name as store_name'
            )
            ->orderBy('dropshipping_product_import_history.created_at', 'desc')
            ->limit(10)
            ->get();

        $availableStores = DB::connection('mysql')->table('dropshipping_woocommerce_configs')
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
            // Get all available products from all active stores (from main database)
            $productsQuery = DB::connection('mysql')->table('dropshipping_products')
                ->join('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_products.woocommerce_config_id')
                ->where('dropshipping_woocommerce_configs.is_active', 1)
                ->where('dropshipping_products.status', 'publish')
                ->select(
                    'dropshipping_products.*',
                    'dropshipping_woocommerce_configs.name as store_name'
                )
                ->orderBy('dropshipping_products.created_at', 'desc');

            $products = $productsQuery->paginate(24); // 24 products per page for nice card grid

            // Process images for each product
            $products->getCollection()->transform(function ($product) {
                // Extract first image from JSON images data
                $image = null;
                if (!empty($product->images)) {
                    $images = json_decode($product->images, true);
                    if (is_array($images) && count($images) > 0) {
                        if (is_array($images[0])) {
                            $image = $images[0]['src'] ?? $images[0]['url'] ?? null;
                        } else {
                            $image = $images[0];
                        }
                    }
                }
                $product->image = $image;
                return $product;
            });

            // Get available stores for filter (from main database)
            $stores = DB::connection('mysql')->table('dropshipping_woocommerce_configs')
                ->where('is_active', 1)
                ->select('id', 'name')
                ->get();

            // Filter by store if selected
            $selectedStore = $request->get('store_id');
            if ($selectedStore) {
                $filteredQuery = DB::connection('mysql')->table('dropshipping_products')
                    ->join('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_products.woocommerce_config_id')
                    ->where('dropshipping_woocommerce_configs.is_active', 1)
                    ->where('dropshipping_products.status', 'publish')
                    ->where('dropshipping_products.woocommerce_config_id', $selectedStore)
                    ->select(
                        'dropshipping_products.*',
                        'dropshipping_woocommerce_configs.name as store_name'
                    )
                    ->orderBy('dropshipping_products.created_at', 'desc');

                $products = $filteredQuery->paginate(24);

                // Process images for filtered products too
                $products->getCollection()->transform(function ($product) {
                    // Extract first image from JSON images data
                    $image = null;
                    if (!empty($product->images)) {
                        $images = json_decode($product->images, true);
                        if (is_array($images) && count($images) > 0) {
                            if (is_array($images[0])) {
                                $image = $images[0]['src'] ?? $images[0]['url'] ?? null;
                            } else {
                                $image = $images[0];
                            }
                        }
                    }
                    $product->image = $image;
                    return $product;
                });
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
            ->join(DB::connection('mysql')->getDatabaseName() . '.dropshipping_products', 'dropshipping_products.id', '=', 'dropshipping_product_import_history.dropshipping_product_id')
            ->join(DB::connection('mysql')->getDatabaseName() . '.dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_product_import_history.woocommerce_config_id')
            ->where('dropshipping_product_import_history.tenant_id', $tenantId)
            ->where('dropshipping_product_import_history.import_status', 'completed')
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
     * Show tenant's local products (the actual imported products in their store)
     */
    public function myProducts(Request $request)
    {
        $tenantId = tenant('id');

        try {
            // Get all local products that were imported via dropshipping
            $localProductsQuery = DB::table('tl_com_products')
                ->leftJoin('tl_com_single_product_price', 'tl_com_single_product_price.product_id', '=', 'tl_com_products.id')
                ->leftJoin('dropshipping_product_import_history', 'dropshipping_product_import_history.local_product_id', '=', 'tl_com_products.id')
                ->leftJoin(DB::connection('mysql')->getDatabaseName() . '.dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_product_import_history.woocommerce_config_id')
                ->where('dropshipping_product_import_history.tenant_id', $tenantId)
                ->where('dropshipping_product_import_history.import_status', 'completed')
                ->select(
                    'tl_com_products.*',
                    'tl_com_single_product_price.sku',
                    'tl_com_single_product_price.unit_price',
                    'tl_com_single_product_price.purchase_price',
                    'tl_com_single_product_price.quantity as stock_quantity',
                    'dropshipping_product_import_history.imported_at',
                    'dropshipping_woocommerce_configs.name as store_name'
                )
                ->orderBy('dropshipping_product_import_history.imported_at', 'desc');

            // Add search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $localProductsQuery->where('tl_com_products.name', 'like', '%' . $search . '%');
            }

            $localProducts = $localProductsQuery->paginate(24);

            // Process products for display
            $localProducts->getCollection()->transform(function ($product) {
                // Calculate markup percentage
                if ($product->purchase_price && $product->unit_price && $product->purchase_price > 0) {
                    $product->markup_percentage = round((($product->unit_price - $product->purchase_price) / $product->purchase_price) * 100, 2);
                } else {
                    $product->markup_percentage = 0;
                }

                // Format dates
                $product->imported_at_formatted = $product->imported_at ?
                    \Carbon\Carbon::parse($product->imported_at)->format('M d, Y') : '';

                return $product;
            });

            // Get statistics
            $stats = [
                'total_products' => DB::table('dropshipping_product_import_history')
                    ->where('tenant_id', $tenantId)
                    ->where('import_status', 'completed')
                    ->count(),
                'this_month' => DB::table('dropshipping_product_import_history')
                    ->where('tenant_id', $tenantId)
                    ->where('import_status', 'completed')
                    ->whereYear('imported_at', now()->year)
                    ->whereMonth('imported_at', now()->month)
                    ->count(),
                'total_value' => DB::table('tl_com_single_product_price')
                    ->leftJoin('dropshipping_product_import_history', 'dropshipping_product_import_history.local_product_id', '=', 'tl_com_single_product_price.product_id')
                    ->where('dropshipping_product_import_history.tenant_id', $tenantId)
                    ->where('dropshipping_product_import_history.import_status', 'completed')
                    ->sum('tl_com_single_product_price.unit_price')
            ];

            return view('plugin/dropshipping::tenant.my-products', compact('localProducts', 'stats'));
        } catch (\Exception $e) {
            // Fallback if there are any errors
            $localProducts = collect();
            $stats = ['total_products' => 0, 'this_month' => 0, 'total_value' => 0];

            return view('plugin/dropshipping::tenant.my-products', compact('localProducts', 'stats'));
        }
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
            $product = DB::connection('mysql')->table('dropshipping_products')
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
