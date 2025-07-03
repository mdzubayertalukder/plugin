<?php

namespace Plugin\Multivendor\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\ProductCategory;

class CategoryHasCommission extends Model
{

    protected $table = "tl_com_category_has_commission";

    protected $fillable = ['category_id'];

    public function category()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }
}
