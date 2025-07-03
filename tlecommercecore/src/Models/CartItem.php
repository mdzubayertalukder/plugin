<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = "tl_com_cart_items";

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
