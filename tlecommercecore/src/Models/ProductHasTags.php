<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHasTags extends Model
{

    protected $table = "tl_com_product_has_tags";

    protected $fillable = ['product_id', 'tag_id'];
}
