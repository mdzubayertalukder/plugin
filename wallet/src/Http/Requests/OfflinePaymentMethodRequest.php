<?php

namespace Plugin\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfflinePaymentMethodRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => translate('Name is required'),
        ];
    }
}
