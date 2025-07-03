<?php

namespace Plugin\Dropshipping\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Plugin\Dropshipping\Models\WooCommerceConfig;
use Plugin\Dropshipping\Models\DropshippingProduct;

class WooCommerceApiService
{
    protected $storeUrl;
    protected $consumerKey;
    protected $consumerSecret;

    /**
     * Initialize WooCommerce API client
     */
    public function __construct($storeUrl = null, $consumerKey = null, $consumerSecret = null)
    {
        $this->storeUrl = $storeUrl;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Set API credentials
     */
    public function setCredentials($storeUrl, $consumerKey, $consumerSecret)
    {
        $this->storeUrl = rtrim($storeUrl, '/');
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        return $this;
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $response = $this->makeRequest('GET', '/wp-json/wc/v3/system_status');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Connection successful'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to connect: ' . $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get store information
     */
    public function getStoreInfo()
    {
        try {
            $response = $this->makeRequest('GET', '/wp-json/wc/v3/system_status');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'name' => $data['settings']['title']['value'] ?? 'Unknown Store',
                    'version' => $data['environment']['version'] ?? 'Unknown',
                    'url' => $this->storeUrl
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get store info'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get products from WooCommerce
     */
    public function getProducts($page = 1, $perPage = 100)
    {
        try {
            $response = $this->makeRequest('GET', '/wp-json/wc/v3/products', [
                'page' => $page,
                'per_page' => $perPage,
                'status' => 'publish'
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'products' => $response->json(),
                    'total_pages' => $response->header('X-WP-TotalPages'),
                    'total_products' => $response->header('X-WP-Total')
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch products: ' . $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get single product
     */
    public function getProduct($productId)
    {
        try {
            $response = $this->makeRequest('GET', "/wp-json/wc/v3/products/{$productId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'product' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get product categories
     */
    public function getCategories()
    {
        try {
            $response = $this->makeRequest('GET', '/wp-json/wc/v3/products/categories', [
                'per_page' => 100
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'categories' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch categories'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Make HTTP request to WooCommerce API
     */
    protected function makeRequest($method, $endpoint, $params = [])
    {
        if (!$this->storeUrl || !$this->consumerKey || !$this->consumerSecret) {
            throw new \Exception('API credentials not set');
        }

        $url = $this->storeUrl . $endpoint;

        // Add authentication parameters
        $params['consumer_key'] = $this->consumerKey;
        $params['consumer_secret'] = $this->consumerSecret;

        Log::info('WooCommerce API Request', [
            'method' => $method,
            'url' => $url,
            'params' => array_keys($params) // Log only parameter keys for security
        ]);

        $response = Http::timeout(30)->retry(2, 1000)->{strtolower($method)}($url, $params);

        Log::info('WooCommerce API Response', [
            'status' => $response->status(),
            'successful' => $response->successful()
        ]);

        return $response;
    }

    /**
     * Validate API credentials format
     */
    public static function validateCredentials($storeUrl, $consumerKey, $consumerSecret)
    {
        $errors = [];

        if (empty($storeUrl) || !filter_var($storeUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid store URL';
        }

        if (empty($consumerKey) || strlen($consumerKey) < 10) {
            $errors[] = 'Consumer key appears to be invalid';
        }

        if (empty($consumerSecret) || strlen($consumerSecret) < 10) {
            $errors[] = 'Consumer secret appears to be invalid';
        }

        return $errors;
    }

    /**
     * Sync products from WooCommerce to local database
     */
    public function syncProducts($configId, $limit = 100)
    {
        try {
            $syncedCount = 0;
            $errors = [];
            $page = 1;

            do {
                $result = $this->getProducts($page, $limit);

                if (!$result['success']) {
                    throw new \Exception($result['message']);
                }

                foreach ($result['products'] as $product) {
                    try {
                        $this->saveProductToDatabase($product, $configId);
                        $syncedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Product {$product['id']}: " . $e->getMessage();
                    }
                }

                $page++;
            } while ($page <= ($result['total_pages'] ?? 1) && $syncedCount < $limit);

            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Save product to local database
     */
    protected function saveProductToDatabase($product, $configId)
    {
        $existingProduct = DB::table('dropshipping_products')
            ->where('woocommerce_config_id', $configId)
            ->where('woocommerce_product_id', $product['id'])
            ->first();

        $productData = [
            'woocommerce_config_id' => $configId,
            'woocommerce_product_id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'description' => $product['description'] ?? '',
            'short_description' => $product['short_description'] ?? '',
            'price' => $product['price'] ?? 0,
            'regular_price' => $product['regular_price'] ?? 0,
            'sale_price' => $product['sale_price'] ?? null,
            'sku' => $product['sku'] ?? '',
            'stock_quantity' => $product['stock_quantity'] ?? 0,
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
}
