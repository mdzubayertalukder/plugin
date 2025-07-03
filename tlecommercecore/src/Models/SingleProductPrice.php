<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class SingleProductPrice extends Model
{
    protected $table = "tl_com_single_product_price";

    protected $fillable = ['product_id'];
}
