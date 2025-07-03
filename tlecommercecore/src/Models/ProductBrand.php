<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\ProductBrandTranslations;

class ProductBrand extends Model
{
    protected $table = "tl_com_brands";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $brand_translations = $this->brand_translations->where('lang', $lang)->first();
        return $brand_translations != null ? $brand_translations->$field : $this->$field;
    }

    public function brand_translations()
    {
        return $this->hasMany(ProductBrandTranslations::class, 'brand_id');
    }
}
