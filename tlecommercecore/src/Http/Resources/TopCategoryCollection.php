<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Session;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TopCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'name' => $data->translation('name', Session::get('api_locale')),
                    'slug' => $data->permalink,
                    'icon' => getFilePath($data->icon),
                ];
            })
        ];
    }
}
