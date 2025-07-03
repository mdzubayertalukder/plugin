<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHasChoiceOption extends Model
{

    protected $table = "tl_com_product_has_choice_options";

    protected $fillable = ['product_id', 'choice_id', 'option_id'];
}
