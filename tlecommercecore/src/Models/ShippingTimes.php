<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\ShippingRate;

class ShippingTimes extends Model
{
    protected $table = "tl_com_shipping_times";


    public function shippingRate()
    {
        return $this->belongsTo(ShippingRate::class, 'id', 'delivery_time');
    }
}
