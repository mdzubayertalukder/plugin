<?php

namespace Plugin\Coupon\Models;

use Illuminate\Database\Eloquent\Model;

class CouponBrands extends Model
{
    protected $table = "tl_com_coupon_brands";
    protected $fillable = ['coupon_id', 'brand_id'];
}