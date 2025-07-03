<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class EcommerceConfig extends Model
{
    protected $table = "tl_com_ecommerce_settings";

    protected $fillable = ['key_name'];
}
