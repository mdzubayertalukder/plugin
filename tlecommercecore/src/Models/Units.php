<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\UnitTranslation;

class Units extends Model
{
    protected $table = "tl_com_units";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $unit_translations = $this->unit_translations->where('lang', $lang)->first();
        return $unit_translations != null ? $unit_translations->$field : $this->$field;
    }

    public function unit_translations()
    {
        return $this->hasMany(UnitTranslation::class, 'unit_id');
    }
}
