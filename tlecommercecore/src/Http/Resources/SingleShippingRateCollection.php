<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Plugin\TlcommerceCore\Models\ShippingTimes;
use Illuminate\Http\Resources\Json\JsonResource;
use Plugin\TlcommerceCore\Models\ShippingProfile;

class SingleShippingRateCollection extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => (int) $this->id,
            'title' => $this->rate_type == 'own_rate' && $this->name != null ? $this->name : $this->carrierName($this->carrier_id),
            'shipping_cost' => $this->shipping_cost,
            'shipping_time' => $this->getShippingTime($this->delivery_time),
            'shipping_from' => $this->shipping_from($this->shipping_zone->profile_id),
            'by' => $this->shipping_medium != null ? $this->shippied_by() : null
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
