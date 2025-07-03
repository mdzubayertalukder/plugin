<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeTranslation extends Model
{

    protected $table = "tl_com_product_attribute_translations";

    protected $fillable = ['attribute_id', 'lang'];
}
