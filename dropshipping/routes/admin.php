<?php

use Illuminate\Support\Facades\Route;
use Plugin\Dropshipping\Http\Controllers\Admin\WooCommerceConfigController;
use Illuminate\Support\Facades\DB;

/**
 * Dropshipping Admin Routes
 * These routes are for super admin to manage WooCommerce configurations
 */

Route::group(['prefix' => getAdminPrefix(), 'as' => 'admin.dropshipping.', 'middleware' => ['auth']], function () {
    // Main Dropshipping Dashboard
    Route::get('/dropshipping', [WooCommerceConfigController::class, 'dashboard'])->name('dashboard');

    // WooCommerce Configuration Management
    Route::get('/dropshipping/woocommerce-config', [WooCommerceConfigController::class, 'index'])->name('woocommerce.index');
    Route::get('/dropshipping/woocommerce-config/create', [WooCommerceConfigController::class, 'create'])->name('woocommerce.create');
    Route::post('/dropshipping/woocommerce-config', [WooCommerceConfigController::class, 'store'])->name('woocommerce.store');
    Route::get('/dropshipping/woocommerce-config/{id}/edit', [WooCommerceConfigController::class, 'edit'])->name('woocommerce.edit');
    Route::put('/dropshipping/woocommerce-config/{id}', [WooCommerceConfigController::class, 'update'])->name('woocommerce.update');
    Route::delete('/dropshipping/woocommerce-config/{id}', [WooCommerceConfigController::class, 'destroy'])->name('woocommerce.destroy');

    // WooCommerce Actions
    Route::post('/dropshipping/woocommerce-config/test-connection', [WooCommerceConfigController::class, 'testConnection'])->name('woocommerce.test');
    Route::post('/dropshipping/woocommerce-config/{id}/sync-products', [WooCommerceConfigController::class, 'syncProducts'])->name('woocommerce.sync');

    // DEBUG: Manual sync test route (temporary)
    Route::get('/dropshipping/debug/sync/{id}', function ($id) {
        try {
            $config = DB::table('dropshipping_woocommerce_configs')->where('id', $id)->first();

            if (!$config) {
                return response()->json(['error' => 'Configuration not found']);
            }

            // Test API connection
            $apiService = new \Plugin\Dropshipping\Services\WooCommerceApiService();
            $apiService->setCredentials($config->store_url, $config->consumer_key, $config->consumer_secret);

            $connectionTest = $apiService->testConnection();
            if (!$connectionTest['success']) {
                return response()->json(['error' => 'Connection failed: ' . $connectionTest['message']]);
            }

            // Try to get products
            $result = $apiService->getProducts(1, 5); // Just 5 products for testing

            if (!$result['success']) {
                return response()->json(['error' => 'Failed to fetch products: ' . $result['message']]);
            }

            $products = $result['products'];
            $savedCount = 0;

            foreach ($products as $product) {
                // Helper function to convert empty strings to null for numeric fields
                $cleanPrice = function ($value) {
                    if ($value === '' || $value === null) {
                        return null;
                    }
                    return is_numeric($value) ? (float)$value : null;
                };

                // Helper function to convert empty strings to 0 for quantity fields
                $cleanQuantity = function ($value) {
                    if ($value === '' || $value === null) {
                        return 0;
                    }
                    return is_numeric($value) ? (int)$value : 0;
                };

                $productData = [
                    'woocommerce_config_id' => $config->id,
                    'woocommerce_product_id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'description' => $product['description'] ?? '',
                    'short_description' => $product['short_description'] ?? '',
                    'price' => $cleanPrice($product['price']),
                    'regular_price' => $cleanPrice($product['regular_price']),
                    'sale_price' => $cleanPrice($product['sale_price']),
                    'sku' => $product['sku'] ?? '',
                    'stock_quantity' => $cleanQuantity($product['stock_quantity']),
                    'stock_status' => $product['stock_status'] ?? 'instock',
                    'categories' => json_encode($product['categories'] ?? []),
                    'tags' => json_encode($product['tags'] ?? []),
                    'images' => json_encode($product['images'] ?? []),
                    'attributes' => json_encode($product['attributes'] ?? []),
                    'status' => $product['status'] ?? 'publish',
                    'featured' => $product['featured'] ?? false,
                    'date_created' => isset($product['date_created']) ? date('Y-m-d H:i:s', strtotime($product['date_created'])) : now(),
                    'date_modified' => isset($product['date_modified']) ? date('Y-m-d H:i:s', strtotime($product['date_modified'])) : now(),
                    'last_synced_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Check if product already exists
                $existing = DB::table('dropshipping_products')
                    ->where('woocommerce_config_id', $config->id)
                    ->where('woocommerce_product_id', $product['id'])
                    ->first();

                if (!$existing) {
                    DB::table('dropshipping_products')->insert($productData);
                    $savedCount++;
                }
            }

            // Update product count
            $totalProducts = DB::table('dropshipping_products')->where('woocommerce_config_id', $config->id)->count();
            DB::table('dropshipping_woocommerce_configs')
                ->where('id', $config->id)
                ->update(['total_products' => $totalProducts]);

            return response()->json([
                'success' => true,
                'message' => "Sync test completed! Saved {$savedCount} new products. Total: {$totalProducts}",
                'total_products_available' => $result['total_products'] ?? 'Unknown',
                'products_fetched' => count($products),
                'products_saved' => $savedCount,
                'total_in_db' => $totalProducts
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    })->name('debug.sync');

    // Plan Limits Management
    Route::get('/dropshipping/plan-limits', [WooCommerceConfigController::class, 'planLimits'])->name('plan-limits.index');
    Route::post('/dropshipping/plan-limits', [WooCommerceConfigController::class, 'storePlanLimits'])->name('plan-limits.store');
    Route::put('/dropshipping/plan-limits/{id}', [WooCommerceConfigController::class, 'updatePlanLimits'])->name('plan-limits.update');

    // Reports
    Route::get('/dropshipping/reports/imports', [WooCommerceConfigController::class, 'importReports'])->name('reports.imports');
    Route::get('/dropshipping/reports/usage', [WooCommerceConfigController::class, 'usageReports'])->name('reports.usage');

    // Settings
    Route::get('/dropshipping/settings', [WooCommerceConfigController::class, 'settings'])->name('settings.index');
    Route::post('/dropshipping/settings/update', [WooCommerceConfigController::class, 'updateSettings'])->name('settings.update');
});
