<?php

namespace Plugin\Refund\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Plugin\Refund\Models\ReasonTranslation;

class ProductRefundReason extends Model
{
    protected $table = "tl_com_product_refund_reasons";

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $reasons_translations = $this->reasons_translations->where('lang', $lang)->first();
        return $reasons_translations != null ? $reasons_translations->$field : $this->$field;
    }

    public function reasons_translations()
    {
        return $this->hasMany(ReasonTranslation::class, 'reason_id');
    }
}
