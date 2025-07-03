<?php

namespace Plugin\Flashdeal\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{

    protected $table = "tl_com_product_translation";

    protected $fillable = ['product_id', 'lang'];
}
