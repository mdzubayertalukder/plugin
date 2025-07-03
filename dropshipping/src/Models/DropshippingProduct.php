<?php

namespace Plugin\Dropshipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DropshippingProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dropshipping_products';

    protected $fillable = [
        'woocommerce_config_id',
        'woocommerce_product_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'regular_price',
        'sale_price',
        'sku',
        'stock_quantity',
        'stock_status',
        'categories',
        'tags',
        'images',
        'gallery_images',
        'attributes',
        'variations',
        'weight',
        'dimensions',
        'meta_data',
        'status',
        'featured',
        'catalog_visibility',
        'date_created',
        'date_modified',
        'last_synced_at'
    ];

    protected $casts = [
        'woocommerce_product_id' => 'integer',
        'price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'categories' => 'array',
        'tags' => 'array',
        'images' => 'array',
        'gallery_images' => 'array',
        'attributes' => 'array',
        'variations' => 'array',
        'dimensions' => 'array',
        'meta_data' => 'array',
        'featured' => 'boolean',
        'date_created' => 'datetime',
        'date_modified' => 'datetime',
        'last_synced_at' => 'datetime'
    ];

    protected $dates = [
        'date_created',
        'date_modified',
        'last_synced_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the WooCommerce configuration
     */
    public function woocommerceConfig()
    {
        return $this->belongsTo(WooCommerceConfig::class, 'woocommerce_config_id');
    }

    /**
     * Get import history for this product
     */
    public function importHistory()
    {
        return $this->hasMany(ProductImportHistory::class, 'dropshipping_product_id');
    }

    /**
     * Get tenants who imported this product
     */
    public function importedByTenants()
    {
        return $this->hasMany(ProductImportHistory::class, 'dropshipping_product_id')
            ->with('tenant')
            ->where('status', 'completed')
            ->latest();
    }

    /**
     * Scope for in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'instock');
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope for published products
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->sale_price && $this->sale_price < $this->regular_price) {
            return [
                'sale_price' => number_format($this->sale_price, 2),
                'regular_price' => number_format($this->regular_price, 2),
                'on_sale' => true
            ];
        }

        return [
            'price' => number_format($this->price ?: $this->regular_price, 2),
            'on_sale' => false
        ];
    }

    /**
     * Get main image
     */
    public function getMainImageAttribute()
    {
        $images = $this->images;
        return !empty($images) && is_array($images) ? $images[0] : null;
    }

    /**
     * Get stock status badge
     */
    public function getStockStatusBadgeAttribute()
    {
        switch ($this->stock_status) {
            case 'instock':
                return '<span class="badge badge-success">In Stock</span>';
            case 'outofstock':
                return '<span class="badge badge-danger">Out of Stock</span>';
            case 'onbackorder':
                return '<span class="badge badge-warning">On Backorder</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }

    /**
     * Check if product needs sync
     */
    public function needsSync()
    {
        if (!$this->last_synced_at) {
            return true;
        }

        return $this->last_synced_at->lt($this->date_modified);
    }

    /**
     * Get category names as string
     */
    public function getCategoryNamesAttribute()
    {
        if (!$this->categories || !is_array($this->categories)) {
            return '';
        }

        return collect($this->categories)->pluck('name')->implode(', ');
    }

    /**
     * Get tag names as string
     */
    public function getTagNamesAttribute()
    {
        if (!$this->tags || !is_array($this->tags)) {
            return '';
        }

        return collect($this->tags)->pluck('name')->implode(', ');
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%");
        });
    }
}
