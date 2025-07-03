<?php

namespace Plugin\Dropshipping\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Plugin\Dropshipping\Models\WooCommerceConfig;
use Plugin\Dropshipping\Services\WooCommerceApiService;
use Plugin\Dropshipping\Models\DropshippingProduct;
use Plugin\Dropshipping\Models\DropshippingPlanLimit;
use Plugin\Dropshipping\Models\ProductImportHistory;

class WooCommerceConfigController extends Controller
{
    protected $wooCommerceApi;

    public function __construct(WooCommerceApiService $wooCommerceApi)
    {
        $this->wooCommerceApi = $wooCommerceApi;
    }

    /**
     * Show dropshipping dashboard
     */
    public function dashboard()
    {
        try {
            // Basic Statistics
            $totalConfigs = DB::table('dropshipping_woocommerce_configs')->count();
            $activeConfigs = DB::table('dropshipping_woocommerce_configs')->where('is_active', 1)->count();
            $totalProducts = DB::table('dropshipping_products')->count();
            $totalImports = DB::table('dropshipping_product_import_history')->count();

            // Import Statistics
            $todayImports = DB::table('dropshipping_product_import_history')
                ->whereDate('created_at', today())
                ->count();

            $successfulImports = DB::table('dropshipping_product_import_history')
                ->where('status', 'completed')
                ->count();

            $failedImports = DB::table('dropshipping_product_import_history')
                ->where('status', 'failed')
                ->count();

            $pendingImports = DB::table('dropshipping_product_import_history')
                ->where('status', 'pending')
                ->count();

            // Recent Activities
            $recentImports = DB::table('dropshipping_product_import_history')
                ->leftJoin('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_product_import_history.woocommerce_config_id')
                ->leftJoin('dropshipping_products', 'dropshipping_products.id', '=', 'dropshipping_product_import_history.dropshipping_product_id')
                ->select(
                    'dropshipping_product_import_history.*',
                    'dropshipping_woocommerce_configs.name as config_name',
                    'dropshipping_products.name as product_name'
                )
                ->orderBy('dropshipping_product_import_history.created_at', 'desc')
                ->limit(8)
                ->get();

            // Top Performing Stores (by product count)
            $topStores = DB::table('dropshipping_woocommerce_configs')
                ->leftJoin('dropshipping_products', 'dropshipping_products.woocommerce_config_id', '=', 'dropshipping_woocommerce_configs.id')
                ->select(
                    'dropshipping_woocommerce_configs.name',
                    'dropshipping_woocommerce_configs.store_url',
                    'dropshipping_woocommerce_configs.is_active',
                    'dropshipping_woocommerce_configs.last_sync_at',
                    DB::raw('COUNT(dropshipping_products.id) as product_count')
                )
                ->groupBy('dropshipping_woocommerce_configs.id', 'dropshipping_woocommerce_configs.name', 'dropshipping_woocommerce_configs.store_url', 'dropshipping_woocommerce_configs.is_active', 'dropshipping_woocommerce_configs.last_sync_at')
                ->orderBy('product_count', 'desc')
                ->limit(5)
                ->get();

            // Monthly Import Trends (last 6 months)
            $monthlyTrends = DB::table('dropshipping_product_import_history')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_imports'),
                    DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as successful_imports'),
                    DB::raw('COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_imports')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();

            // System Health Indicators
            $syncingStores = DB::table('dropshipping_woocommerce_configs')
                ->where('sync_status', 'syncing')
                ->count();

            $lastSyncTime = DB::table('dropshipping_woocommerce_configs')
                ->whereNotNull('last_sync_at')
                ->max('last_sync_at');
        } catch (\Exception $e) {
            // Fallback values in case of database errors
            $totalConfigs = $activeConfigs = $totalProducts = $totalImports = 0;
            $todayImports = $successfulImports = $failedImports = $pendingImports = 0;
            $recentImports = collect([]);
            $topStores = collect([]);
            $monthlyTrends = collect([]);
            $syncingStores = 0;
            $lastSyncTime = null;
        }

        return view('plugin/dropshipping::admin.dashboard', compact(
            'totalConfigs',
            'activeConfigs',
            'totalProducts',
            'totalImports',
            'todayImports',
            'successfulImports',
            'failedImports',
            'pendingImports',
            'recentImports',
            'topStores',
            'monthlyTrends',
            'syncingStores',
            'lastSyncTime'
        ));
    }

    /**
     * Display WooCommerce configurations
     */
    public function index()
    {
        $configs = DB::table('dropshipping_woocommerce_configs')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('plugin/dropshipping::admin.woocommerce.index', compact('configs'));
    }

    /**
     * Show form to create new WooCommerce configuration
     */
    public function create()
    {
        return view('plugin/dropshipping::admin.woocommerce.create');
    }

    /**
     * Store new WooCommerce configuration
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'store_url' => 'required|url|max:500',
            'consumer_key' => 'required|string|max:255',
            'consumer_secret' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            toastNotification('error', $validator->errors()->first(), 'Error');
            return redirect()->back()->withInput();
        }

        try {
            // Test connection before saving
            $testResult = $this->testWooCommerceConnection(
                $request->store_url,
                $request->consumer_key,
                $request->consumer_secret
            );

            if (!$testResult['success']) {
                toastNotification('error', 'WooCommerce connection failed: ' . $testResult['message'], 'Error');
                return redirect()->back()->withInput();
            }

            DB::table('dropshipping_woocommerce_configs')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'store_url' => rtrim($request->store_url, '/'),
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => $request->consumer_secret,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            toastNotification('success', 'WooCommerce configuration created successfully', 'Success');
            return redirect()->route('admin.dropshipping.woocommerce.index');
        } catch (\Exception $e) {
            toastNotification('error', 'Failed to create configuration: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show form to edit WooCommerce configuration
     */
    public function edit($id)
    {
        $config = DB::table('dropshipping_woocommerce_configs')->where('id', $id)->first();

        if (!$config) {
            toastNotification('error', 'Configuration not found', 'Error');
            return redirect()->route('admin.dropshipping.woocommerce.index');
        }

        return view('plugin/dropshipping::admin.woocommerce.edit', compact('config'));
    }

    /**
     * Update WooCommerce configuration
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'store_url' => 'required|url|max:500',
            'consumer_key' => 'required|string|max:255',
            'consumer_secret' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            toastNotification('error', $validator->errors()->first(), 'Error');
            return redirect()->back()->withInput();
        }

        try {
            $config = DB::table('dropshipping_woocommerce_configs')->where('id', $id)->first();

            if (!$config) {
                toastNotification('error', 'Configuration not found', 'Error');
                return redirect()->route('admin.dropshipping.woocommerce.index');
            }

            // Test connection before updating
            $testResult = $this->testWooCommerceConnection(
                $request->store_url,
                $request->consumer_key,
                $request->consumer_secret
            );

            if (!$testResult['success']) {
                toastNotification('error', 'WooCommerce connection failed: ' . $testResult['message'], 'Error');
                return redirect()->back()->withInput();
            }

            DB::table('dropshipping_woocommerce_configs')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'store_url' => rtrim($request->store_url, '/'),
                    'consumer_key' => $request->consumer_key,
                    'consumer_secret' => $request->consumer_secret,
                    'is_active' => $request->has('is_active') ? 1 : 0,
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]);

            toastNotification('success', 'WooCommerce configuration updated successfully', 'Success');
            return redirect()->route('admin.dropshipping.woocommerce.index');
        } catch (\Exception $e) {
            toastNotification('error', 'Failed to update configuration: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete WooCommerce configuration
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::table('dropshipping_woocommerce_configs')->where('id', $id)->delete();

            if ($deleted) {
                toastNotification('success', 'Configuration deleted successfully', 'Success');
            } else {
                toastNotification('error', 'Configuration not found', 'Error');
            }
        } catch (\Exception $e) {
            toastNotification('error', 'Failed to delete configuration: ' . $e->getMessage(), 'Error');
        }

        return redirect()->route('admin.dropshipping.woocommerce.index');
    }

    /**
     * Test WooCommerce connection
     */
    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_url' => 'required|url',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $result = $this->testWooCommerceConnection(
            $request->store_url,
            $request->consumer_key,
            $request->consumer_secret
        );

        return response()->json($result);
    }

    /**
     * Sync products from WooCommerce
     */
    public function syncProducts(Request $request, $id)
    {
        try {
            $config = DB::table('dropshipping_woocommerce_configs')->where('id', $id)->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration not found'
                ]);
            }

            // Update sync status
            DB::table('dropshipping_woocommerce_configs')
                ->where('id', $id)
                ->update(['sync_status' => 'syncing']);

            // Initialize WooCommerce API service
            $apiService = new \Plugin\Dropshipping\Services\WooCommerceApiService();
            $apiService->setCredentials($config->store_url, $config->consumer_key, $config->consumer_secret);

            // Sync products in batches
            $totalSynced = 0;
            $page = 1;
            $perPage = 50;
            $errors = [];

            do {
                $result = $apiService->getProducts($page, $perPage);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }

                $products = $result['products'];

                foreach ($products as $product) {
                    try {
                        $this->saveProductToDatabase($product, $config->id);
                        $totalSynced++;
                    } catch (\Exception $e) {
                        $errors[] = "Product {$product['id']}: " . $e->getMessage();
                    }
                }

                $page++;

                // Update progress
                DB::table('dropshipping_woocommerce_configs')
                    ->where('id', $id)
                    ->update(['total_products' => $totalSynced]);
            } while (count($products) == $perPage && $page <= 20); // Limit to 20 pages (1000 products) for now

            // Update final status
            DB::table('dropshipping_woocommerce_configs')
                ->where('id', $id)
                ->update([
                    'sync_status' => 'completed',
                    'last_sync_at' => now(),
                    'total_products' => $totalSynced
                ]);

            $message = "Successfully synced {$totalSynced} products.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'synced_count' => $totalSynced,
                'errors' => array_slice($errors, 0, 5) // Show first 5 errors
            ]);
        } catch (\Exception $e) {
            DB::table('dropshipping_woocommerce_configs')
                ->where('id', $id)
                ->update(['sync_status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save product to database
     */
    private function saveProductToDatabase($product, $configId)
    {
        $existingProduct = DB::table('dropshipping_products')
            ->where('woocommerce_config_id', $configId)
            ->where('woocommerce_product_id', $product['id'])
            ->first();

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
            'woocommerce_config_id' => $configId,
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
            'updated_at' => now()
        ];

        if ($existingProduct) {
            DB::table('dropshipping_products')
                ->where('id', $existingProduct->id)
                ->update($productData);
        } else {
            $productData['created_at'] = now();
            DB::table('dropshipping_products')->insert($productData);
        }
    }

    /**
     * Show plan limits
     */
    public function planLimits()
    {
        try {
            $limits = DB::table('dropshipping_plan_limits')
                ->leftJoin('tl_saas_packages', 'tl_saas_packages.id', '=', 'dropshipping_plan_limits.package_id')
                ->select('dropshipping_plan_limits.*', 'tl_saas_packages.name as package_name')
                ->get();
        } catch (\Exception $e) {
            // If there's any database error, provide empty collection
            $limits = collect([]);
        }

        return view('plugin/dropshipping::admin.plan-limits.index', compact('limits'));
    }

    /**
     * Show import reports
     */
    public function importReports()
    {
        try {
            $reports = DB::table('dropshipping_product_import_history')
                ->leftJoin('dropshipping_woocommerce_configs', 'dropshipping_woocommerce_configs.id', '=', 'dropshipping_product_import_history.woocommerce_config_id')
                ->leftJoin('dropshipping_products', 'dropshipping_products.id', '=', 'dropshipping_product_import_history.dropshipping_product_id')
                ->select(
                    'dropshipping_product_import_history.*',
                    'dropshipping_woocommerce_configs.name as config_name',
                    'dropshipping_products.name as product_name'
                )
                ->orderBy('dropshipping_product_import_history.created_at', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            // If there's any database error, provide empty collection
            $reports = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1,
                ['path' => request()->url()]
            );
        }

        return view('plugin/dropshipping::admin.reports.imports', compact('reports'));
    }

    /**
     * Show usage reports
     */
    public function usageReports()
    {
        $usage = DB::table('dropshipping_product_import_history')
            ->select(
                'tenant_id',
                DB::raw('COUNT(*) as total_imports'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as successful_imports'),
                DB::raw('COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_imports'),
                DB::raw('MAX(created_at) as last_import')
            )
            ->groupBy('tenant_id')
            ->paginate(20);

        return view('plugin/dropshipping::admin.reports.usage', compact('usage'));
    }

    /**
     * Show settings
     */
    public function settings()
    {
        $settings = DB::table('dropshipping_settings')->pluck('value', 'key');
        return view('plugin/dropshipping::admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        try {
            foreach ($request->except(['_token']) as $key => $value) {
                DB::table('dropshipping_settings')
                    ->updateOrInsert(
                        ['key' => $key],
                        ['value' => $value, 'updated_at' => now()]
                    );
            }

            toastNotification('success', 'Settings updated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            toastNotification('error', 'Failed to update settings: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }

    /**
     * Test WooCommerce API connection
     */
    private function testWooCommerceConnection($storeUrl, $consumerKey, $consumerSecret)
    {
        try {
            $url = rtrim($storeUrl, '/') . '/wp-json/wc/v3/system_status';

            $response = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connection successful'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'HTTP Error: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
