<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\ColorTranslation;

class Colors extends Model
{
    protected $table = "tl_com_colors";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $color_translations = $this->color_translations->where('lang', $lang)->first();
        return $color_translations != null ? $color_translations->$field : $this->$field;
    }

    public function color_translations()
    {
        return $this->hasMany(ColorTranslation::class, 'color_id');
    }
}
