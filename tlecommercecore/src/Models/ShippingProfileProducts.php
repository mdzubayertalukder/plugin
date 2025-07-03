<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Product;

class ShippingProfileProducts extends Model
{
    protected $table = "tl_com_shipping_profiles_has_products";

    protected $fillable = ['product_id', 'profile_id'];

    public function product_details()
    {
        return $this->belongsTo(Product::class, 'product_id')->select('name', 'id', 'thumbnail_image');
    }
}
