<?php

namespace Plugin\Flashdeal\Models;

use Carbon\Carbon;
use Plugin\Flashdeal\Models\Product;
use Plugin\Flashdeal\Models\FlashDeal;
use Illuminate\Database\Eloquent\Model;

class FlashDealProducts extends Model
{

    protected $table = "tl_com_deals_products";

    protected $fillable = ['deal_id', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function deal()
    {
        return $this->belongsTo(FlashDeal::class, 'deal_id', 'id');
    }

    public function isExpired()
    {
        $end_date = Carbon::parse($this->deal->end_date);
        if ($end_date->isPast()) {
            return config('settings.general_status.active');
        } else {
            return config('settings.general_status.in_active');
        }
    }
}
