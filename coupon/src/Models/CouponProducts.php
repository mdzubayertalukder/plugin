<?php

namespace Plugin\Coupon\Models;

use Illuminate\Database\Eloquent\Model;

class CouponProducts extends Model
{

    protected $table = "tl_com_coupon_products";

    protected $fillable = ['coupon_id', 'product_id'];
}
