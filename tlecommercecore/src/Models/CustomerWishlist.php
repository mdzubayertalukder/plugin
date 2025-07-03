<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerWishlist extends Model
{

    protected $table = "tl_com_customer_wishlists";

    protected $fillable = ['customer_id', 'product_id'];
}
