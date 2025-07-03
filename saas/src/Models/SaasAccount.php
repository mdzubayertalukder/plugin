<?php

namespace Plugin\Saas\Models;

use App\Models\Tenant;
use DateTime;
use Core\Models\User;
use Plugin\Saas\Models\Package;
use Plugin\Saas\Models\PackagePlan;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaasAccount extends Model
{
    protected $table = "tl_saas_accounts";

    /**
     * One to one relation with domain
     */
    public function domain()
    {
        return $this->hasOne(Domain::class, 'tenant_id', 'tenant_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
    /**
     * One to one relation with custom domain
     */
    public function customDomain()
    {
        return $this->hasOne(CustomDomain::class, 'store_id');
    }

    /**
     * check if store has pending domain request
     */
    public function hasPendingDomainRequest()
    {
        return $this->customDomain()->where('status', 0)->exists(); // return true if exists and false otherwise
    }

    /**
     * One to one relation with users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PackagePlan::class, 'package_plan');
    }

    public function is_notifiable()
    {
        $notify_before_expired_days = \Plugin\Saas\Repositories\SettingsRepository::getSaasSetting('notify_before_expired_days');

        if ($this->valid_till != null) {
            $current_date = new DateTime();
            $valid_till = new DateTime($this->valid_till);

            $interval = $current_date->diff($valid_till);
            $diffInDays = $interval->days;

            if ($diffInDays <= $notify_before_expired_days) {
                return 0;
            }

            if ($diffInDays <= 0) {
                return 1;
            }
        }

        return 2;
    }

    public function is_expired()
    {
        if ($this->valid_till == null) {
            return 0;
        }

        $now = now();
        if ($this->valid_till < $now) {
            return 1;
        }

        return 0;
    }

    public function is_trial()
    {
        return $this->membership_type == 'trail user' ? 1 : 0;
    }
}
