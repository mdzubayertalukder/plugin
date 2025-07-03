<?php

namespace Plugin\Coupon\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $table = "tl_com_coupon_usages";
    protected $fillable = ['coupon_id', 'order_id'];
}
