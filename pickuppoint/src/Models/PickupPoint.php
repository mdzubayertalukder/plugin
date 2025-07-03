<?php

namespace Plugin\PickupPoint\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Cities;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\ShippingZone;
use Plugin\PickupPoint\Models\PickupPointTranslation;

class PickupPoint extends Model
{
    protected $table = "tl_pick_up_points";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $pickup_translations = $this->pickup_translations->where('lang', $lang)->first();
        return $pickup_translations != null ? $pickup_translations->$field : $this->$field;
    }

    public function pickup_translations()
    {
        return $this->hasMany(PickupPointTranslation::class, 'pic_up_point_id');
    }

    public function zoneInfo()
    {
        return $this->belongsTo(ShippingZone::class, 'zone');
    }

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
