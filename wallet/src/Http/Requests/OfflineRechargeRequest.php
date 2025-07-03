<?php

namespace Plugin\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfflineRechargeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'recharge_amount' => 'required',
            'transaction_id' => 'required',
            'transaction_image' => 'nullable|mimes:jpeg,png,jpg'
        ];
    }

    public function messages()
    {
        return [
            'recharge_amount.required' => translate('Amount is required'),
            'transaction_id.required' => translate('Transaction id is required'),
            'transaction_image.mimes' => translate('Invalid image format')
        ];
    }
}
