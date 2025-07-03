<?php

namespace Plugin\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminWalletManualActionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => translate('Amount is required'),
            'amount.integer' => translate('Invalid amount'),
        ];
    }
}
