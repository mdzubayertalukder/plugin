<?php

namespace Plugin\Multivendor\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\OrderHasProducts;
use Plugin\TlcommerceCore\Models\Orders;
use Plugin\TlcommerceCore\Models\Product;

class SellerEarnings extends Model
{

    protected $table = "tl_com_seller_earning";

    protected $fillable = ['seller_id', 'order_package_id', 'order_id', 'product_id'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function package()
    {
        return $this->belongsTo(OrderHasProducts::class, 'order_package_id');
    }
}
