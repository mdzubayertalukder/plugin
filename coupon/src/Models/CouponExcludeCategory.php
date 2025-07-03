<?php

namespace Plugin\Coupon\Models;

use Illuminate\Database\Eloquent\Model;

class CouponExcludeCategory extends Model
{

    protected $table = "tl_com_coupon_exclude_category";

    protected $fillable = ['coupon_id', 'category_id'];
}
