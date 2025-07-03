<?php

namespace Plugin\Multivendor\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->shop_name,
            'slug' => $this->shop_slug,
            'logo' => getFilePath($this->logo, false),
            'shop_banner' => $this->shop_banner != null ? getFilePath($this->shop_banner, false) : getFilePath(getGeneralSetting('shop_default_bg_image')),
            'shop_address' => $this->shop_address,
            'shop_phone' => $this->shop_phone,
            'total_followers' => $this->totalFollowers($this->seller_id),
            'total_product' => $this->products->count(),
            'positive_rating' => $this->positiveRating(),
            'avg_rating' => $this->reviews->avg('rating') != null ? $this->reviews->avg('rating') : 0,
        ];
    }

    public function positiveRating()
    {
        if ($this->reviews->count() > 0) {
            $positive_reviews = $this->reviews->where('rating', '>', 2)->count();
            $positive_rating = $positive_reviews * 100 / $this->reviews->count();
            return number_format($positive_rating, 0);
        }

        return 0;
    }

    /**
     * Will return total follower
     */
    public function totalFollowers($seller_id)
    {
        return DB::table('tl_com_seller_followers')->where('seller_id', $seller_id)->count();
    }
}
