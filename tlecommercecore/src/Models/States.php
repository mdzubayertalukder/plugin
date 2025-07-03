<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Country;
use Plugin\TlcommerceCore\Models\StateTranslation;

class States extends Model
{

    protected $table = "tl_com_state";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $state_translations = $this->state_translations->where('lang', $lang)->first();
        return $state_translations != null ? $state_translations->$field : $this->$field;
    }

    public function state_translations()
    {
        return $this->hasMany(StateTranslation::class, 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities()
    {
        return $this->hasMany(Cities::class, 'state_id');
    }
}
