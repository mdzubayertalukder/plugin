<?php

namespace Plugin\Coupon\Models;


use Illuminate\Database\Eloquent\Model;
use Plugin\Coupon\Models\CouponBrands;
use Plugin\Coupon\Models\CouponCategory;
use Plugin\Coupon\Models\CouponProducts;
use Plugin\Coupon\Models\CouponExcludeBrand;
use Plugin\Coupon\Models\CouponExcludeCategory;
use Plugin\Coupon\Models\CouponExcludeProducts;

class Coupons extends Model
{

    protected $table = "tl_com_coupons";

    protected $fillable = ['code'];

    public function products()
    {
        return $this->hasMany(CouponProducts::class, 'coupon_id');
    }

    public function exclude_products()
    {
        return $this->hasMany(CouponExcludeProducts::class, 'coupon_id');
    }
    public function categories()
    {
        return $this->hasMany(CouponCategory::class, 'coupon_id');
    }

    public function exclude_categories()
    {
        return $this->hasMany(CouponExcludeCategory::class, 'coupon_id');
    }

    public function brands()
    {
        return $this->hasMany(CouponBrands::class, 'coupon_id');
    }

    public function exclude_brands()
    {
        return $this->hasMany(CouponExcludeBrand::class, 'coupon_id');
    }
}
