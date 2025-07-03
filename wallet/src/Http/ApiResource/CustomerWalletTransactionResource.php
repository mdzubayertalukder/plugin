<?php

namespace Plugin\Wallet\Http\ApiResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerWalletTransactionResource extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'recharge_amount' => $data->recharge_amount,
                    'date' => $data->created_at->format('D M y'),
                    'status' => $data->status_label,
                    'type' => $data->entry_type == config('tlecommercecore.wallet_entry_type.debit') ? 'debited' : 'credited',
                    'payment_method' => $data->payment_method,
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200,
        ];
    }
}
