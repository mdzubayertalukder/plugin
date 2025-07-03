<?php

namespace Plugin\PickupPoint\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PickupPointCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'name' => $data->translation('name', session()->get('api_locale')),
                    'location' => $data->location,
                    'phone' => $data->phone,
                    'zone_id' => $data->city != null ? $data->city->id : null,
                    'zone_name' => $this->addressInfo($data)
                ];
            })
        ];
    }

    public function addressInfo($data)
    {
        $country = $data->country != null ? $data->country->translation('name', session()->get('api_locale')) : null;
        $state = $data->state != null ? $data->state->translation('name', session()->get('api_locale')) : null;
        $city = $data->city != null ? $data->city->translation('name', session()->get('api_locale')) : null;

        return $city . ', ' . $state . ', ' . $country;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
