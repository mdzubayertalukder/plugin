<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryTranslations extends Model
{

    protected $table = "tl_com_category_translations";

    protected $fillable = ['category_id', 'lang'];
}
