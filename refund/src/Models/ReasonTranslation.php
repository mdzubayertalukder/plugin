<?php

namespace Plugin\Refund\Models;

use Illuminate\Database\Eloquent\Model;

class ReasonTranslation extends Model
{

    protected $table = "tl_com_refund_reason_translations";

    protected $fillable = ['reason_id', 'lang'];
}
