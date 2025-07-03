<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerAddressCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id'          => (int) $data->id,
                    'name'        => $data->name,
                    'address'     => $data->address,
                    'phone_code'  => '',
                    'phone'       => $data->phone,
                    'status'      => $data->status == config('settings.general_status.active') ? 'Active' : 'Inactive',
                    'country'     => $data->country != null ? $this->countryInfo($data->country) : null,
                    'state'       => $data->state != null ? $this->stateInfo($data->state) : null,
                    'city'        => $data->city != null ? $this->cityInfo($data->city) : null,
                    'postal_code' => $data->postal_code,
                    'default_shipping' => $data->default_shipping,
                    'default_billing' => $data->default_billing
                ];
            })
        ];
    }
    public function countryInfo($country)
    {
        return [
            'id' => $country->id,
            'name' => $country->translation('name', session()->get('api_locale'))
        ];
    }
    public function stateInfo($state)
    {
        return [
            'id' => $state->id,
            'name' => $state->translation('name', session()->get('api_locale'))
        ];
    }
    public function cityInfo($city)
    {
        return [
            'id' => $city->id,
            'name' => $city->translation('name', session()->get('api_locale'))
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
