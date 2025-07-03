<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductConditionTranslation extends Model
{

    protected $table = "tl_com_product_condition_translations";

    protected $fillable = ['condition_id', 'lang'];
}
