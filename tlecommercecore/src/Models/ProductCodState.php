<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCodState extends Model
{
    protected $table = "tl_com_product_cod_states";

    protected $fillable = ['product_id', 'state_id'];
}
