<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'order_code' => $data->order_code,
                    'total_payable_amount' => $data->total_payable_amount,
                    'total_products' => $data->products->sum('quantity'),
                    'order_date' => $data->created_at->format('d M Y h:i:s A'),
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
