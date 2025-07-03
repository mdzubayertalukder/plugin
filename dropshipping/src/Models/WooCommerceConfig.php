<?php

namespace Plugin\Dropshipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WooCommerceConfig extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dropshipping_woocommerce_configs';

    protected $fillable = [
        'name',
        'description',
        'store_url',
        'consumer_key',
        'consumer_secret',
        'is_active',
        'last_sync_at',
        'total_products',
        'sync_status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
        'total_products' => 'integer'
    ];

    protected $hidden = [
        'consumer_key',
        'consumer_secret'
    ];

    protected $dates = [
        'last_sync_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the products for this WooCommerce config
     */
    public function products()
    {
        return $this->hasMany(DropshippingProduct::class, 'woocommerce_config_id');
    }

    /**
     * Get import history for this config
     */
    public function importHistory()
    {
        return $this->hasMany(ProductImportHistory::class, 'woocommerce_config_id');
    }

    /**
     * Get the user who created this config
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user who last updated this config
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Scope for active configurations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get masked consumer secret for display
     */
    public function getMaskedConsumerSecretAttribute()
    {
        if (empty($this->consumer_secret)) {
            return '';
        }
        return str_repeat('*', strlen($this->consumer_secret) - 4) . substr($this->consumer_secret, -4);
    }

    /**
     * Get full store URL with protocol
     */
    public function getFullStoreUrlAttribute()
    {
        $url = $this->store_url;
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        return rtrim($url, '/');
    }

    /**
     * Check if configuration is properly set up
     */
    public function isConfigured()
    {
        return !empty($this->store_url) &&
            !empty($this->consumer_key) &&
            !empty($this->consumer_secret);
    }

    /**
     * Get sync status badge
     */
    public function getSyncStatusBadgeAttribute()
    {
        switch ($this->sync_status) {
            case 'syncing':
                return '<span class="badge badge-warning">Syncing</span>';
            case 'completed':
                return '<span class="badge badge-success">Completed</span>';
            case 'failed':
                return '<span class="badge badge-danger">Failed</span>';
            default:
                return '<span class="badge badge-secondary">Not Synced</span>';
        }
    }
}
