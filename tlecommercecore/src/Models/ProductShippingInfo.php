<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductShippingInfo extends Model
{
    protected $table = "tl_com_product_shipping_info";

    protected $fillable = ['product_id'];
}
