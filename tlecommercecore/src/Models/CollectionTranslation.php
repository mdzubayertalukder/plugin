<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionTranslation extends Model
{
    protected $table = "tl_com_collection_translations";

    protected $fillable = ['collection_id', 'lang'];
}
