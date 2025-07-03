<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name'               => 'required|unique:tl_com_currencies,name,' . request('id'),
            'symbol'             => 'required',
            'code'               => 'required|unique:tl_com_currencies,code,' . request('id'),
            'exchange_rate'      => 'required|numeric',
            'position'           => 'required',
            'thousand_separator' => 'required',
            'decimal_separator'  => 'required',
            'number_of_decimal'  => 'required|numeric'
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
            'name.required' => translate('Name is required'),
            'name.unique'   => translate('This name  is already taken'),
            'exchange_rate.required' => translate('Exchange rate is required'),
            'code.required' => translate('Code is required'),
            'code.unique' => translate('This code is already used'),
            'position.required' => translate('Position is required'),
            'thousand_separator.required' => translate('Thousand separator is required'),
            'decimal_separator.required' => translate('Decimal separator is required'),
            'number_of_decimal.required' => translate('Number of decimal is required')
        ];
    }
}
