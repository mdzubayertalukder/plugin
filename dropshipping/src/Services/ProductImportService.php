<?php

namespace Plugin\Dropshipping\Services;

use Plugin\Dropshipping\Models\DropshippingProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImportService
{
    /**
     * Import a dropshipping product to tenant store
     */
    public function importProduct(DropshippingProduct $dropshippingProduct, float $markupPercentage = 20): int
    {
        try {
            DB::beginTransaction();

            // Calculate pricing
            $originalPrice = $dropshippingProduct->regular_price ?? $dropshippingProduct->price;
            $finalPrice = $originalPrice * (1 + $markupPercentage / 100);
            $salePrice = null;

            if ($dropshippingProduct->sale_price) {
                $salePrice = $dropshippingProduct->sale_price * (1 + $markupPercentage / 100);
            }

            // Create product data
            $productData = [
                'name' => $dropshippingProduct->name,
                'slug' => $this->generateUniqueSlug($dropshippingProduct->slug ?: Str::slug($dropshippingProduct->name)),
                'description' => $dropshippingProduct->description,
                'short_description' => $dropshippingProduct->short_description,
                'price' => $finalPrice,
                'regular_price' => $finalPrice,
                'sale_price' => $salePrice,
                'sku' => $this->generateUniqueSku($dropshippingProduct->sku),
                'stock_quantity' => $dropshippingProduct->stock_quantity,
                'stock_status' => $dropshippingProduct->stock_status,
                'weight' => $dropshippingProduct->weight,
                'status' => 'publish',
                'featured' => $dropshippingProduct->featured,
                'meta_data' => json_encode([
                    'dropshipping_source' => 'woocommerce',
                    'source_product_id' => $dropshippingProduct->woocommerce_product_id,
                    'source_config_id' => $dropshippingProduct->woocommerce_config_id,
                    'markup_percentage' => $markupPercentage,
                    'original_price' => $originalPrice
                ]),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ];

            // Insert into products table (assuming tl_products table exists)
            $productId = DB::table('tl_products')->insertGetId($productData);

            // Import categories if they exist
            if (!empty($dropshippingProduct->categories)) {
                $this->importProductCategories($productId, $dropshippingProduct->categories);
            }

            // Import tags if they exist
            if (!empty($dropshippingProduct->tags)) {
                $this->importProductTags($productId, $dropshippingProduct->tags);
            }

            // Import images
            if (!empty($dropshippingProduct->images)) {
                $this->importProductImages($productId, $dropshippingProduct->images);
            }

            // Import gallery images
            if (!empty($dropshippingProduct->gallery_images)) {
                $this->importProductGalleryImages($productId, $dropshippingProduct->gallery_images);
            }

            // Import attributes if they exist
            if (!empty($dropshippingProduct->attributes)) {
                $this->importProductAttributes($productId, $dropshippingProduct->attributes);
            }

            DB::commit();
            Log::info("Successfully imported product: {$dropshippingProduct->name} (ID: {$productId})");

            return $productId;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product import failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate unique product slug
     */
    protected function generateUniqueSlug(string $baseSlug): string
    {
        $slug = Str::slug($baseSlug);
        $originalSlug = $slug;
        $counter = 1;

        while (DB::table('tl_products')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate unique SKU
     */
    protected function generateUniqueSku(string $baseSku): string
    {
        if (empty($baseSku)) {
            $baseSku = 'DS-' . Str::random(8);
        }

        $sku = $baseSku;
        $originalSku = $sku;
        $counter = 1;

        while (DB::table('tl_products')->where('sku', $sku)->exists()) {
            $sku = $originalSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }

    /**
     * Import product categories
     */
    protected function importProductCategories(int $productId, array $categories)
    {
        foreach ($categories as $category) {
            // Check if category exists in tenant store
            $localCategoryId = $this->findOrCreateCategory($category);

            if ($localCategoryId) {
                // Link product to category
                DB::table('tl_product_categories')->insert([
                    'product_id' => $productId,
                    'category_id' => $localCategoryId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Import product tags
     */
    protected function importProductTags(int $productId, array $tags)
    {
        foreach ($tags as $tag) {
            // Check if tag exists in tenant store
            $localTagId = $this->findOrCreateTag($tag);

            if ($localTagId) {
                // Link product to tag
                DB::table('tl_product_tags')->insert([
                    'product_id' => $productId,
                    'tag_id' => $localTagId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Import product images
     */
    protected function importProductImages(int $productId, array $images)
    {
        foreach ($images as $index => $image) {
            if (empty($image['src'])) {
                continue;
            }

            // Download and save image locally (optional)
            $localImagePath = $this->downloadAndSaveImage($image['src'], $productId);

            DB::table('tl_product_images')->insert([
                'product_id' => $productId,
                'image_path' => $localImagePath ?: $image['src'],
                'alt_text' => $image['alt'] ?? '',
                'is_primary' => $index === 0 ? 1 : 0,
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Import product gallery images
     */
    protected function importProductGalleryImages(int $productId, array $galleryImages)
    {
        foreach ($galleryImages as $index => $image) {
            if (empty($image['src'])) {
                continue;
            }

            // Download and save image locally (optional)
            $localImagePath = $this->downloadAndSaveImage($image['src'], $productId);

            DB::table('tl_product_gallery')->insert([
                'product_id' => $productId,
                'image_path' => $localImagePath ?: $image['src'],
                'alt_text' => $image['alt'] ?? '',
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Import product attributes
     */
    protected function importProductAttributes(int $productId, array $attributes)
    {
        foreach ($attributes as $attribute) {
            DB::table('tl_product_attributes')->insert([
                'product_id' => $productId,
                'attribute_name' => $attribute['name'] ?? '',
                'attribute_value' => is_array($attribute['options']) ?
                    implode(', ', $attribute['options']) : ($attribute['options'] ?? ''),
                'is_variation' => $attribute['variation'] ?? false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Find or create category in tenant store
     */
    protected function findOrCreateCategory(array $categoryData): ?int
    {
        if (empty($categoryData['name'])) {
            return null;
        }

        // Check if category exists
        $existingCategory = DB::table('tl_categories')
            ->where('name', $categoryData['name'])
            ->first();

        if ($existingCategory) {
            return $existingCategory->id;
        }

        // Create new category
        return DB::table('tl_categories')->insertGetId([
            'name' => $categoryData['name'],
            'slug' => Str::slug($categoryData['name']),
            'description' => $categoryData['description'] ?? '',
            'status' => 'active',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Find or create tag in tenant store
     */
    protected function findOrCreateTag(array $tagData): ?int
    {
        if (empty($tagData['name'])) {
            return null;
        }

        // Check if tag exists
        $existingTag = DB::table('tl_tags')
            ->where('name', $tagData['name'])
            ->first();

        if ($existingTag) {
            return $existingTag->id;
        }

        // Create new tag
        return DB::table('tl_tags')->insertGetId([
            'name' => $tagData['name'],
            'slug' => Str::slug($tagData['name']),
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Download and save image locally (optional implementation)
     */
    protected function downloadAndSaveImage(string $imageUrl, int $productId): ?string
    {
        try {
            // This is a basic implementation - you might want to implement proper image handling
            // For now, we'll just return the original URL
            // In a full implementation, you would:
            // 1. Download the image
            // 2. Store it in your local storage
            // 3. Return the local path

            return $imageUrl; // Return original URL for now

        } catch (\Exception $e) {
            Log::warning('Failed to download image: ' . $e->getMessage());
            return $imageUrl; // Fallback to original URL
        }
    }

    /**
     * Update imported product pricing
     */
    public function updateProductPricing(int $localProductId, float $newMarkupPercentage): bool
    {
        try {
            // Get original product data from meta
            $product = DB::table('tl_products')->where('id', $localProductId)->first();

            if (!$product) {
                return false;
            }

            $metaData = json_decode($product->meta_data, true) ?? [];
            $originalPrice = $metaData['original_price'] ?? $product->regular_price;

            // Calculate new pricing
            $newPrice = $originalPrice * (1 + $newMarkupPercentage / 100);

            // Update product
            DB::table('tl_products')
                ->where('id', $localProductId)
                ->update([
                    'price' => $newPrice,
                    'regular_price' => $newPrice,
                    'meta_data' => json_encode(array_merge($metaData, [
                        'markup_percentage' => $newMarkupPercentage
                    ])),
                    'updated_at' => now()
                ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update product pricing: ' . $e->getMessage());
            return false;
        }
    }
}
