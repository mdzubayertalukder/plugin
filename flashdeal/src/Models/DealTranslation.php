<?php

namespace Plugin\Flashdeal\Models;

use Illuminate\Database\Eloquent\Model;

class DealTranslation extends Model
{

    protected $table = "tl_com_flash_deal_translations";

    protected $fillable = ['lang', 'title', 'deal_id'];
}
