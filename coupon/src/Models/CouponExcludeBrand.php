<?php

namespace Plugin\Coupon\Models;

use Illuminate\Database\Eloquent\Model;

class CouponExcludeBrand extends Model
{
    protected $table = "tl_com_coupon_exclude_brands";

    protected $fillable = ['coupon_id', 'brand_id'];
}
