<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'name' => $data->translation('name', session()->get('api_locale')),
                    'slug' => $data->permalink,
                    'icon' => Cache::remember('category-icon' . $data->icon, 60 * 60 * 24, function () use ($data) {
                        return;
                    }),
                    'childs' => new CategoryCollection($data->childs),
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
