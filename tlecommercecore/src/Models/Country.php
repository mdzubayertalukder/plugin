<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\States;
use Plugin\TlcommerceCore\Models\CountryTranslation;

class Country extends Model
{

    protected $table = "tl_countries";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $country_translations = $this->country_translations->where('lang', $lang)->first();
        return $country_translations != null ? $country_translations->$field : $this->$field;
    }

    public function country_translations()
    {
        return $this->hasMany(CountryTranslation::class, 'country_id');
    }

    public function states()
    {
        return $this->hasMany(States::class, 'country_id', 'id');
    }
}
