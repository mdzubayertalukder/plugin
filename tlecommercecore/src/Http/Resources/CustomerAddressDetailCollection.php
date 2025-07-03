<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressDetailCollection extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'address'    => $this->address,
            'phone'      => $this->phone,
            'phone_code' => '',
            'postal_code' => $this->postal_code,
            'country' => $this->country != null ? $this->countryInfo() : null,
            'state' => $this->state != null ? $this->stateInfo() : null,
            'city' => $this->city != null ? $this->cityInfo() : null,
            'status' => $this->status,
            'default_shipping' => $this->default_shipping,
            'default_billing' => $this->default_billing,
        ];
    }
    public function countryInfo()
    {
        return [
            'id' => $this->country->id,
            'name' => $this->country->translation('name', session()->get('api_locale'))
        ];
    }
    public function stateInfo()
    {
        return [
            'id' => $this->state->id,
            'name' => $this->state->translation('name', session()->get('api_locale'))
        ];
    }
    public function cityInfo()
    {
        return [
            'id' => $this->city->id,
            'name' => $this->city->translation('name', session()->get('api_locale'))
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
