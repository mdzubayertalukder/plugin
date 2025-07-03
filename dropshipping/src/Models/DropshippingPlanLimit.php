<?php

namespace Plugin\Dropshipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DropshippingPlanLimit extends Model
{
    use HasFactory;

    protected $table = 'dropshipping_plan_limits';

    protected $fillable = [
        'package_id',
        'monthly_import_limit',
        'total_import_limit',
        'bulk_import_limit',
        'auto_sync_enabled',
        'pricing_markup_min',
        'pricing_markup_max',
        'allowed_categories',
        'restricted_categories',
        'settings'
    ];

    protected $casts = [
        'monthly_import_limit' => 'integer',
        'total_import_limit' => 'integer',
        'bulk_import_limit' => 'integer',
        'auto_sync_enabled' => 'boolean',
        'pricing_markup_min' => 'decimal:2',
        'pricing_markup_max' => 'decimal:2',
        'allowed_categories' => 'array',
        'restricted_categories' => 'array',
        'settings' => 'array'
    ];

    /**
     * Get the package/plan
     */
    public function package()
    {
        return $this->belongsTo(\Plugin\Saas\Models\Package::class, 'package_id');
    }

    /**
     * Get tenants using this package
     */
    public function tenants()
    {
        return $this->hasMany(\Plugin\Saas\Models\SaasAccount::class, 'package_id', 'package_id');
    }

    /**
     * Check if monthly limit is reached for a tenant
     */
    public function isMonthlyLimitReached($tenantId)
    {
        if ($this->monthly_import_limit === -1) {
            return false; // Unlimited
        }

        $currentMonthImports = ProductImportHistory::forTenant($tenantId)
            ->successful()
            ->whereMonth('imported_at', now()->month)
            ->whereYear('imported_at', now()->year)
            ->count();

        return $currentMonthImports >= $this->monthly_import_limit;
    }

    /**
     * Check if total limit is reached for a tenant
     */
    public function isTotalLimitReached($tenantId)
    {
        if ($this->total_import_limit === -1) {
            return false; // Unlimited
        }

        $totalImports = ProductImportHistory::forTenant($tenantId)
            ->successful()
            ->count();

        return $totalImports >= $this->total_import_limit;
    }

    /**
     * Get remaining monthly imports for a tenant
     */
    public function getRemainingMonthlyImports($tenantId)
    {
        if ($this->monthly_import_limit === -1) {
            return -1; // Unlimited
        }

        $currentMonthImports = ProductImportHistory::forTenant($tenantId)
            ->successful()
            ->whereMonth('imported_at', now()->month)
            ->whereYear('imported_at', now()->year)
            ->count();

        return max(0, $this->monthly_import_limit - $currentMonthImports);
    }

    /**
     * Get remaining total imports for a tenant
     */
    public function getRemainingTotalImports($tenantId)
    {
        if ($this->total_import_limit === -1) {
            return -1; // Unlimited
        }

        $totalImports = ProductImportHistory::forTenant($tenantId)
            ->successful()
            ->count();

        return max(0, $this->total_import_limit - $totalImports);
    }

    /**
     * Check if bulk import is allowed
     */
    public function canBulkImport($quantity)
    {
        if ($this->bulk_import_limit === -1) {
            return true; // Unlimited
        }

        return $quantity <= $this->bulk_import_limit;
    }

    /**
     * Validate pricing markup
     */
    public function isValidMarkup($markup)
    {
        if ($this->pricing_markup_min !== null && $markup < $this->pricing_markup_min) {
            return false;
        }

        if ($this->pricing_markup_max !== null && $markup > $this->pricing_markup_max) {
            return false;
        }

        return true;
    }

    /**
     * Check if category is allowed
     */
    public function isCategoryAllowed($categoryId)
    {
        // If no allowed categories specified, all are allowed (unless restricted)
        if (empty($this->allowed_categories)) {
            return !$this->isCategoryRestricted($categoryId);
        }

        return in_array($categoryId, $this->allowed_categories);
    }

    /**
     * Check if category is restricted
     */
    public function isCategoryRestricted($categoryId)
    {
        if (empty($this->restricted_categories)) {
            return false;
        }

        return in_array($categoryId, $this->restricted_categories);
    }

    /**
     * Get formatted limits
     */
    public function getFormattedLimitsAttribute()
    {
        return [
            'monthly' => $this->monthly_import_limit === -1 ? 'Unlimited' : number_format($this->monthly_import_limit),
            'total' => $this->total_import_limit === -1 ? 'Unlimited' : number_format($this->total_import_limit),
            'bulk' => $this->bulk_import_limit === -1 ? 'Unlimited' : number_format($this->bulk_import_limit)
        ];
    }

    /**
     * Get auto sync status badge
     */
    public function getAutoSyncBadgeAttribute()
    {
        return $this->auto_sync_enabled
            ? '<span class="badge badge-success">Enabled</span>'
            : '<span class="badge badge-secondary">Disabled</span>';
    }

    /**
     * Create default limits for a package
     */
    public static function createDefault($packageId)
    {
        return self::create([
            'package_id' => $packageId,
            'monthly_import_limit' => 100,
            'total_import_limit' => -1,
            'bulk_import_limit' => 20,
            'auto_sync_enabled' => false,
            'pricing_markup_min' => 0,
            'pricing_markup_max' => null,
            'settings' => [
                'auto_update_prices' => false,
                'auto_update_stock' => false,
                'import_reviews' => false
            ]
        ]);
    }
}
