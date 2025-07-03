<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\AttributeValues;
use Plugin\TlcommerceCore\Models\ProductAttributeTranslation;

class ProductAttribute extends Model
{
    protected $table = "tl_com_attributes";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $attribute_translations = $this->attribute_translations->where('lang', $lang)->first();
        return $attribute_translations != null ? $attribute_translations->$field : $this->$field;
    }

    public function attribute_translations()
    {
        return $this->hasMany(ProductAttributeTranslation::class, 'attribute_id');
    }

    public function attribute_values()
    {
        return $this->hasMany(AttributeValues::class, 'attribute_id');
    }
}
