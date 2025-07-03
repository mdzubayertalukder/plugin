<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\ProductCollection;

class ShippingZoneHasTaxes extends Model
{

    protected $table = "tl_com_shipping_zone_has_taxes";

    protected $fillable = ['tax', 'state_id', 'product_collection_id', 'zone_id'];

    public function state()
    {
        return $this->belongsTo(States::class, 'state_id');
    }

    public function product_collection()
    {
        return $this->belongsTo(ProductCollection::class, 'product_collection_id');
    }
}
