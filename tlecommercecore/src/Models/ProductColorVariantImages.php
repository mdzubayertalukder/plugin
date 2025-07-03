<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColorVariantImages extends Model
{
    protected $table = "tl_com_product_color_variant_image";

    protected $fillable = ['product_id', 'color_id'];
}
