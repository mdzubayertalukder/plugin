<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSeo extends Model
{

    protected $table = "tl_com_product_seo";

    protected $fillable = ['product_id'];
}
