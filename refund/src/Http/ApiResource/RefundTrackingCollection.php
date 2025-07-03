<?php

namespace Plugin\Refund\Http\ApiResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RefundTrackingCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'message' => $data->message,
                    'created_at' => $data->created_at->format('d M Y h:i:s A')
                ];
            })
        ];
    }
}
