<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Session;

class SingleDealsCollection extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'title' => $this->translation('title', Session::get('api_locale')),
            'banner' => getFilePath($this->background_image, false),
            'deadline' => $this->end_date,
            'start_date' => $this->start_date,
            'background_color' => $this->background_color,
            'text_color' => $this->text_color
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
