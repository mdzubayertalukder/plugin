<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class StateTranslation extends Model
{

    protected $table = "tl_com_state_translations";

    protected $fillable = ['state_id', 'lang'];
}
