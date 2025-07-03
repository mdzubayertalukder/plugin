<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Session;

class SingleCategoryCollection extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->translation('name', Session::get('api_locale')),
            'slug' => $this->permalink,
            'icon' => getFilePath($this->icon)
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
