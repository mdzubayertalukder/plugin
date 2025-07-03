<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCodCities extends Model
{
    protected $table = "tl_com_product_cod_cities";

    protected $fillable = ['product_id', 'city_id'];
}
