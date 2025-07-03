<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartItemsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'uid'          => $data->uid,
                    'id'           => (int) $data->product_id,
                    'name'         => $data->product->translation('name', session()->get('api_locale')),
                    'permalink'    => $data->product->permalink,
                    'image'        => $data->image,
                    'variant'      => $data->variant,
                    'variant_code' => $data->variant_code,
                    'quantity'     => (int) $data->quantity,
                    'unitPrice'    => $data->unitPrice,
                    'oldPrice'     => $data->oldPrice,
                    'min_item'     => (int) $data->min_item,
                    'max_item'     => (int) $data->max_item,
                    'attachment'   => $data->attachment,
                    'seller'       => $data->product->supplier,
                    'shop_name'    => isActivePluging('multivendor')  && $data->product->supplier != null ? $data->product->seller->shop->shop_name : null,
                    'shop_slug'    => isActivePluging('multivendor') && $data->product->supplier != null ? $data->product->seller->shop->shop_slug : null,
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
