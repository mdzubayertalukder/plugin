<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\CollectionHasProducts;
use Plugin\TlcommerceCore\Models\CollectionTranslation;

class ProductCollection extends Model
{
    protected $table = "tl_com_product_collections";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $collection_translations = $this->collection_translations->where('lang', $lang)->first();
        return $collection_translations != null ? $collection_translations->$field : $this->$field;
    }

    public function collection_translations()
    {
        return $this->hasMany(CollectionTranslation::class, 'collection_id');
    }

    public function products()
    {
        return $this->hasMany(CollectionHasProducts::class, 'collection_id')->orderBy('id', 'DESC');
    }
}
