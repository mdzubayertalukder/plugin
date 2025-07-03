<?php

namespace Plugin\Dropshipping\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Plugin\Dropshipping\Models\WooCommerceConfig;
use Plugin\Dropshipping\Services\WooCommerceApiService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WooCommerceConfigController extends Controller
{
    protected $wooCommerceApi;

    public function __construct(WooCommerceApiService $wooCommerceApi)
    {
        $this->wooCommerceApi = $wooCommerceApi;
    }

    /**
     * Display list of WooCommerce configurations
     */
    public function index()
    {
        $configs = WooCommerceConfig::with(['creator', 'updater'])
            ->withCount('products')
            ->latest()
            ->paginate(15);

        return view('dropshipping::admin.woocommerce.index', compact('configs'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('dropshipping::admin.woocommerce.create');
    }

    /**
     * Store new WooCommerce configuration
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:dropshipping_woocommerce_configs,name',
            'description' => 'nullable|string|max:1000',
            'store_url' => 'required|url|max:500',
            'consumer_key' => 'required|string|max:255',
            'consumer_secret' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Test connection before saving
            $testResult = $this->testWooCommerceConnection(
                $request->store_url,
                $request->consumer_key,
                $request->consumer_secret
            );

            if (!$testResult['success']) {
                return redirect()->back()
                    ->withErrors(['connection' => $testResult['message']])
                    ->withInput();
            }

            $config = WooCommerceConfig::create([
                'name' => $request->name,
                'description' => $request->description,
                'store_url' => rtrim($request->store_url, '/'),
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => $request->consumer_secret,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            toastNotification('success', translate('WooCommerce configuration added successfully'));
            return redirect()->route('core.dropshipping.admin.woocommerce.index');
        } catch (\Exception $e) {
            Log::error('Error creating WooCommerce config: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while saving the configuration'])
                ->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $config = WooCommerceConfig::findOrFail($id);
        return view('dropshipping::admin.woocommerce.edit', compact('config'));
    }

    /**
     * Update WooCommerce configuration
     */
    public function update(Request $request, $id)
    {
        $config = WooCommerceConfig::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:dropshipping_woocommerce_configs,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'store_url' => 'required|url|max:500',
            'consumer_key' => 'required|string|max:255',
            'consumer_secret' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Test connection if credentials changed
            if (
                $request->consumer_key !== $config->consumer_key ||
                $request->consumer_secret !== $config->consumer_secret ||
                $request->store_url !== $config->store_url
            ) {

                $testResult = $this->testWooCommerceConnection(
                    $request->store_url,
                    $request->consumer_key,
                    $request->consumer_secret
                );

                if (!$testResult['success']) {
                    return redirect()->back()
                        ->withErrors(['connection' => $testResult['message']])
                        ->withInput();
                }
            }

            $config->update([
                'name' => $request->name,
                'description' => $request->description,
                'store_url' => rtrim($request->store_url, '/'),
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => $request->consumer_secret,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => auth()->id()
            ]);

            toastNotification('success', translate('WooCommerce configuration updated successfully'));
            return redirect()->route('core.dropshipping.admin.woocommerce.index');
        } catch (\Exception $e) {
            Log::error('Error updating WooCommerce config: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating the configuration'])
                ->withInput();
        }
    }

    /**
     * Delete WooCommerce configuration
     */
    public function destroy($id)
    {
        try {
            $config = WooCommerceConfig::findOrFail($id);

            // Check if configuration has associated products
            if ($config->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete configuration with associated products. Please remove products first.'
                ], 400);
            }

            $config->delete();

            toastNotification('success', translate('WooCommerce configuration deleted successfully'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error deleting WooCommerce config: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the configuration'
            ], 500);
        }
    }

    /**
     * Test WooCommerce connection
     */
    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_url' => 'required|url',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid parameters provided'
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
    public function syncProducts($id)
    {
        try {
            $config = WooCommerceConfig::findOrFail($id);

            if (!$config->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WooCommerce configuration is not properly set up'
                ]);
            }

            // Update sync status
            $config->update(['sync_status' => 'syncing']);

            // Dispatch job for syncing products (you can implement this as a job)
            $this->wooCommerceApi->syncProducts($config);

            return response()->json([
                'success' => true,
                'message' => 'Product sync started successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while starting product sync'
            ], 500);
        }
    }

    /**
     * Test WooCommerce API connection
     */
    private function testWooCommerceConnection($storeUrl, $consumerKey, $consumerSecret)
    {
        try {
            $storeUrl = rtrim($storeUrl, '/');

            // Test endpoint with authentication
            $response = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(10)
                ->get($storeUrl . '/wp-json/wc/v3/system_status');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'store_info' => [
                        'version' => $data['environment']['version'] ?? 'Unknown',
                        'wp_version' => $data['environment']['wp_version'] ?? 'Unknown'
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to connect: ' . $response->status() . ' - ' . $response->reason()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }
}
