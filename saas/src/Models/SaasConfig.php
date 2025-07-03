<?php

namespace Plugin\Saas\Models;

use Illuminate\Database\Eloquent\Model;

class SaasConfig extends Model
{
    protected $table = "tl_saas_settings";

    protected $fillable = ['key_name'];
}
