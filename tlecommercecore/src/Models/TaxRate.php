<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Country;

class TaxRate extends Model
{
    protected $table = "tl_com_tax_rates";

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
