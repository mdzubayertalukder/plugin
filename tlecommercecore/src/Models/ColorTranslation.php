<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class ColorTranslation extends Model
{

    protected $table = "tl_com_color_translations";

    protected $fillable = ['color_id', 'lang'];
}
