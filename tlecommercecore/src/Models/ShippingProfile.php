<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\ShippingZone;
use Plugin\TlcommerceCore\Models\ShippingProfileProducts;

class ShippingProfile extends Model
{
    protected $table = "tl_com_shipping_profiles";

    public function products()
    {
        return $this->hasMany(ShippingProfileProducts::class, 'profile_id');
    }

    public function zones()
    {
        return $this->hasMany(ShippingZone::class, 'profile_id');
    }
}
