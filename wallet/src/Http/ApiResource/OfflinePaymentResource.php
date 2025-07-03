<?php

namespace Plugin\Wallet\Http\ApiResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OfflinePaymentResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'name' => $data->name,
                    'id' => $data->id,
                    'logo' => getFilePath($data->logo, false),
                    'instruction' => $data->instruction,
                    'type' => $data->type,
                    'bank_name' => $data->bank_info != null ? $data->bank_info->bank_name : null,
                    'account_name' => $data->bank_info != null ? $data->bank_info->account_name : null,
                    'account_number' => $data->bank_info != null ? $data->bank_info->account_number : null,
                    'routing_number' => $data->bank_info != null ? $data->bank_info->routing_number : null,
                ];
            })
        ];
    }
}
