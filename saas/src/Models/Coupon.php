<?php

namespace Plugin\Saas\Models;

use Plugin\Saas\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Plugin\Saas\Models\CouponOfPackages;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Coupon extends Model
{
    protected $table = "tl_saas_coupons";

    public function packages(): HasManyThrough
    {
        return $this->hasManyThrough(Package::class, CouponOfPackages::class, 'coupon_id', 'id', 'id', 'package_id');
    }
}
