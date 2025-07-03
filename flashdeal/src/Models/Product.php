<?php

namespace Plugin\Flashdeal\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\Flashdeal\Models\ProductTranslation;

class Product extends Model
{
    protected $table = "tl_com_products";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->product_translations->where('lang', $lang)->first();
        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function product_translations()
    {
        return $this->hasMany(ProductTranslation::class, 'product_id');
    }
}
