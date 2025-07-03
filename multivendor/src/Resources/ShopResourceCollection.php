<?php

namespace Plugin\Multivendor\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'name' => $data->shop_name,
                    'slug' => $data->shop_slug,
                    'total_product' => $data->products_count,
                    'positive_rating' => $this->positiveRating($data),
                    'avg_rating' => $data->reviews->avg('rating') != null ? $data->reviews->avg('rating') : 0,
                    'logo' => getFilePath($data->logo),
                    'shop_banner' => $data->shop_banner != null ? getFilePath($data->shop_banner) : getFilePath(getGeneralSetting('shop_default_bg_image')),
                ];
            })
        ];
    }

    public function positiveRating($data)
    {
        if ($data->reviews->count() > 0) {
            $positive_reviews = $data->reviews->where('rating', '>', 2)->count();
            $positive_rating = $positive_reviews * 100 / $data->reviews->count();

            return number_format($positive_rating, 0);
        }

        return 0;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
