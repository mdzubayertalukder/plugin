<?php

namespace Plugin\Multivendor\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class PayoutSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(Request $request)
    {
        return [
            'bank_name'    => 'required|max:150',
            'bank_code'   => 'max:150',
            'account_name'    => 'required|max:150',
            'account_holder_name'    => 'max:150',
            'account_number'  => 'required|max:150',
            'bank_routing_number'    => 'max:150',
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'bank_name.required' => translate('Bank name is required'),
            'bank_code.max' => translate('Maximum length 150'),
            'account_name.required' => translate('Account name phone is required'),
            'account_name.max' => translate('Maximum length 100'),
            'account_holder_name.max' => translate('Maximum length 150'),
            'account_number.required' => translate('Account number is required'),
            'account_number.max' => translate('Maximum length 150'),
            'bank_routing_number.max' => translate('Maximum length 150'),
        ];
    }
}
