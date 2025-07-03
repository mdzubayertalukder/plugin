<?php

namespace Plugin\Dropshipping\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Plugin\Dropshipping\Models\WooCommerceConfig;
use Plugin\Dropshipping\Models\DropshippingProduct;

class WooCommerceApiService
{
    public function syncProducts(WooCommerceConfig $config)
    {
        try {
            $config->update(['sync_status' => 'syncing']);

            $page = 1;
            $perPage = 50;
            $totalSynced = 0;

            do {
                $products = $this->fetchProducts($config, $page, $perPage);

                if (empty($products)) {
                    break;
                }

                foreach ($products as $productData) {
                    $this->syncSingleProduct($config, $productData);
                    $totalSynced++;
                }

                $page++;
                $config->update(['total_products' => $totalSynced]);
            } while (count($products) === $perPage);

            $config->update([
                'sync_status' => 'completed',
                'last_sync_at' => now(),
                'total_products' => $totalSynced
            ]);
        } catch (\Exception $e) {
            $config->update(['sync_status' => 'failed']);
            Log::error('WooCommerce sync failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function fetchProducts(WooCommerceConfig $config, int $page = 1, int $perPage = 50)
    {
        $response = Http::withBasicAuth($config->consumer_key, $config->consumer_secret)
            ->timeout(30)
            ->get($config->full_store_url . '/wp-json/wc/v3/products', [
                'page' => $page,
                'per_page' => $perPage,
                'status' => 'publish'
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to fetch products: ' . $response->status());
    }

    public function syncSingleProduct(WooCommerceConfig $config, array $productData)
    {
        $existingProduct = DropshippingProduct::where('woocommerce_config_id', $config->id)
            ->where('woocommerce_product_id', $productData['id'])
            ->first();

        $productInfo = [
            'woocommerce_config_id' => $config->id,
            'woocommerce_product_id' => $productData['id'],
            'name' => $productData['name'] ?? '',
            'slug' => $productData['slug'] ?? '',
            'description' => $productData['description'] ?? '',
            'short_description' => $productData['short_description'] ?? '',
            'price' => $productData['price'] ?? 0,
            'regular_price' => $productData['regular_price'] ?? 0,
            'sale_price' => $productData['sale_price'] ?? null,
            'sku' => $productData['sku'] ?? '',
            'stock_quantity' => $productData['stock_quantity'] ?? null,
            'stock_status' => $productData['stock_status'] ?? 'instock',
            'categories' => $productData['categories'] ?? [],
            'tags' => $productData['tags'] ?? [],
            'images' => $productData['images'] ?? [],
            'gallery_images' => array_slice($productData['images'] ?? [], 1),
            'attributes' => $productData['attributes'] ?? [],
            'status' => $productData['status'] ?? 'publish',
            'featured' => $productData['featured'] ?? false,
            'last_synced_at' => now()
        ];

        if ($existingProduct) {
            $existingProduct->update($productInfo);
            return $existingProduct;
        } else {
            return DropshippingProduct::create($productInfo);
        }
    }

    public function testConnection(WooCommerceConfig $config): array
    {
        try {
            $response = Http::withBasicAuth($config->consumer_key, $config->consumer_secret)
                ->timeout(10)
                ->get($config->full_store_url . '/wp-json/wc/v3/system_status');

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Connection successful'];
            }

            return ['success' => false, 'message' => 'Connection failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection error: ' . $e->getMessage()];
        }
    }
}
