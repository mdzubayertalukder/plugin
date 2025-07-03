<?php

namespace Plugin\Dropshipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImportHistory extends Model
{
    use HasFactory;

    protected $table = 'dropshipping_product_import_history';

    protected $fillable = [
        'tenant_id',
        'woocommerce_config_id',
        'dropshipping_product_id',
        'local_product_id',
        'import_type',
        'status',
        'imported_data',
        'pricing_adjustments',
        'error_message',
        'import_settings',
        'imported_at',
        'imported_by'
    ];

    protected $casts = [
        'imported_data' => 'array',
        'pricing_adjustments' => 'array',
        'import_settings' => 'array',
        'imported_at' => 'datetime'
    ];

    protected $dates = [
        'imported_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the tenant who imported the product
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id');
    }

    /**
     * Get the WooCommerce configuration
     */
    public function woocommerceConfig()
    {
        return $this->belongsTo(WooCommerceConfig::class, 'woocommerce_config_id');
    }

    /**
     * Get the dropshipping product
     */
    public function dropshippingProduct()
    {
        return $this->belongsTo(DropshippingProduct::class, 'dropshipping_product_id');
    }

    /**
     * Get the local product (if exists)
     */
    public function localProduct()
    {
        return $this->belongsTo(\Core\Models\Product::class, 'local_product_id');
    }

    /**
     * Get the user who imported
     */
    public function importer()
    {
        return $this->belongsTo(\App\Models\User::class, 'imported_by');
    }

    /**
     * Scope for successful imports
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed imports
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending imports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for specific tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return '<span class="badge badge-success">Completed</span>';
            case 'failed':
                return '<span class="badge badge-danger">Failed</span>';
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            case 'processing':
                return '<span class="badge badge-info">Processing</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }

    /**
     * Get import type label
     */
    public function getImportTypeLabelAttribute()
    {
        switch ($this->import_type) {
            case 'single':
                return 'Single Product';
            case 'bulk':
                return 'Bulk Import';
            case 'auto_sync':
                return 'Auto Sync';
            default:
                return 'Manual';
        }
    }

    /**
     * Get formatted imported data
     */
    public function getFormattedImportedDataAttribute()
    {
        if (!$this->imported_data || !is_array($this->imported_data)) {
            return [];
        }

        return [
            'original_price' => $this->imported_data['original_price'] ?? 0,
            'imported_price' => $this->imported_data['imported_price'] ?? 0,
            'markup_percentage' => $this->imported_data['markup_percentage'] ?? 0,
            'product_name' => $this->imported_data['product_name'] ?? '',
            'sku' => $this->imported_data['sku'] ?? ''
        ];
    }

    /**
     * Check if import was successful
     */
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if import failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted($localProductId = null)
    {
        $this->update([
            'status' => 'completed',
            'local_product_id' => $localProductId,
            'imported_at' => now()
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }
}
