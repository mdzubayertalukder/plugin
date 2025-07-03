<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductReviewCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'review' => $data->review,
                    'rating' => $data->rating,
                    'customer' => $data->customer != null ? $this->customerDetails($data) : null,
                    'images' => $data->images != null ? $this->getImages($data) : null,
                    'variant' => null,
                    'time' => $data->created_at->format('d M Y h:i:s A')
                ];
            })
        ];
    }

    public function customerDetails($data)
    {
        $customer = [];
        $customer['name'] = $data->customer->name;
        $customer['image'] = getFilePath($data->customer->image, false);
        $customer['valified'] = $data->customer->verified_at != null ? 1 : 2;
        return $customer;
    }
    public function getImages($data)
    {
        $images = substr($data->images, 1, -1);
        $images = explode(',', $images);

        $images_link = [];
        foreach ($images as $image) {
            array_push($images_link, getFilePath($image));
        }
        return $images_link;
    }
    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
