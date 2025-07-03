<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrandTranslations extends Model
{

    protected $table = "tl_com_brand_translations";

    protected $fillable = ['brand_id', 'lang'];
}
