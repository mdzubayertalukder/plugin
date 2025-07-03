<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Plugin\TlcommerceCore\Http\Resources\OrderProductCollection;
use Plugin\TlcommerceCore\Http\Resources\CustomerAddressDetailCollection;

class SingleOrderCollection extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->payment_status_label(),
            'delivery_status_label' => $this->delivery_status_label(),
            'payment_method' => $this->wallet_payment == config('settings.general_status.active') ? 'Wallet' : $this->payment_method_info?->name,
            'delivery_status' => $this->delivery_status,
            'sub_total' => $this->sub_total,
            'total_tax' => $this->total_tax,
            'total_delivery_cost' => $this->total_delivery_cost,
            'total_discount' => $this->total_discount,
            'total_payable_amount' => $this->total_payable_amount,
            'order_date' => $this->created_at->format('d M Y h:i:s A'),
            'pickup_point' => isActivePluging('pickuppoint') ? $this->pickup_point : null,
            'billing_details' => $this->billing_details != null ? $this->addressDetails(new CustomerAddressDetailCollection($this->billing_details)) : null,
            'shipping_details' => $this->shipping_details != null ? $this->addressDetails(new CustomerAddressDetailCollection($this->shipping_details)) : null,
            'products' => new OrderProductCollection($this->products),
            'can_cancel' => $this->canCancel(),
            'note' => $this->note,
            'payment_required' => $this->OrderPaymentRequiredStatus(),
        ];
    }

    public function OrderPaymentRequiredStatus()
    {
        if ($this->wallet_payment == config('settings.general_status.active') ||  $this->payment_method == 1 ||  $this->payment_method == 9) {
            return config('settings.general_status.in_active');
        }

        $cancelled_items = $this->products->where('delivery_status', config('tlecommercecore.order_delivery_status.cancelled'))->count();
        if ($cancelled_items > 0) {
            return config('settings.general_status.in_active');
        }

        $paid_items = $this->products->where('payment_status', config('tlecommercecore.order_payment_status.paid'))->count();
        if ($paid_items > 0) {
            return config('settings.general_status.in_active');
        }

        return config('settings.general_status.active');
    }

    public function addressDetails($address)
    {
        $addressData = [
            'name' => $address->name,
            'address' => $address->address,
            'phone' => $address->phone,
            'postal_code' => $address->postal_code,
            'country' => $address->country != null ? $address->country->translation('name') : null,
            'state' => $address->state != null ? $address->state->translation('name') : null,
            'city' => $address->city != null ? $address->city->translation('name') : null,
        ];
        return $addressData;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
