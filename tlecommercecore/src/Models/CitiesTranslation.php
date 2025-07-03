<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class CitiesTranslation extends Model
{

    protected $table = "tl_com_city_translations";

    protected $fillable = ['city_id', 'lang'];
}
