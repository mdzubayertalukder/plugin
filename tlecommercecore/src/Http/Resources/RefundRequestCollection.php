<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RefundRequestCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'refund_code' => $data->refund_code,
                    'order_id' => $data->order_id,
                    'payment_status_label' => $data->paymentStatusLabel(),
                    'return_status_label' => $data->returnStatusLabel(),
                    'total_refund_amount' => $data->total_refund_amount,
                    'return_date' => $data->created_at->format('d M Y h:i:s A'),
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
