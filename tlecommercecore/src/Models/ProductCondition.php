<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\ProductConditionTranslation;

class ProductCondition  extends Model
{
    protected $table = "tl_com_product_conditions";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $condition_translations = $this->condition_translations->where('lang', $lang)->first();
        return $condition_translations != null ? $condition_translations->$field : $this->$field;
    }

    public function condition_translations()
    {
        return $this->hasMany(ProductConditionTranslation::class, 'condition_id');
    }
}
