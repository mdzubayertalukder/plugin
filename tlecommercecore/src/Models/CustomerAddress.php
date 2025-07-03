<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\Country;

class CustomerAddress extends Model
{

    protected $table = "tl_com_customer_address";

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function state()
    {
        return $this->belongsTo(States::class, 'state_id');
    }
    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }
}
