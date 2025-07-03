<?php

namespace Plugin\Coupon\Models;

use Illuminate\Database\Eloquent\Model;

class CouponExcludeProducts extends Model
{

    protected $table = "tl_com_coupon_exclude_products";

    protected $fillable = ['coupon_id', 'product_id'];
}
