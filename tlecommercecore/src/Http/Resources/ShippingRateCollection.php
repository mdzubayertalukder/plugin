<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Plugin\TlcommerceCore\Models\ShippingTimes;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Plugin\TlcommerceCore\Models\ShippingProfile;

class ShippingRateCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'title' => $data->rate_type == 'own_rate' && $data->name != null ? $data->name : $this->carrierName($data->carrier_id),
                    'shipping_cost' => $data->shipping_cost,
                    'shipping_time' => $this->getShippingTime($data->delivery_time),
                    'shipping_from' => $this->shipping_from($data->shipping_zone->profile_id),
                    'by' => $data->shipping_medium != null ? $data->shippied_by() : null
                ];
            })
        ];
    }

    public function carrierName($carrier_id)
    {
        if (isActivePluging('carrier')) {
            $carrier_info = \Plugin\Carrier\Models\ShippingCarrier::where('id', $carrier_id)->first();
            if ($carrier_info != null) {
                return $carrier_info->name;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    public function getShippingTime($time_id)
    {

        $shipping_time = ShippingTimes::where('id', $time_id)->first();
        if ($shipping_time != null) {
            return $shipping_time->min_value . ' ' . $shipping_time->min_unit . '-' . $shipping_time->max_value . ' ' . $shipping_time->max_unit;
        } else {
            return null;
        }
    }

    public function shipping_from($profile_id)
    {
        $shippig_profile = ShippingProfile::where('id', $profile_id)->first();
        return $shippig_profile != null ? $shippig_profile->location : null;
    }
}
