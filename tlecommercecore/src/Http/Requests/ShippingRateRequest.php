<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ShippingRateRequest extends FormRequest
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

        if ($request->has('rate_type')) {
            if ($request['rate_type'] == config('tlecommercecore.shipping_rate_type.carrier_rate')) {
                return [
                    'courier' => 'required|integer',
                    'shipped_by' => 'required|integer'
                ];
            }
        }
        if ($request->has('edit_rate_type')) {
            if ($request['edit_rate_type'] == config('tlecommercecore.shipping_rate_type.carrier_rate')) {
                return [
                    'courier' => 'required|integer',
                    'shipped_by' => 'required|integer'
                ];
            }
        }

        return [
            'rate_name' => 'required',
            'shipping_cost' => 'required'
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
            'rate_name.required' => translate('Name is required'),
            'shipping_cost' => translate('Shipping cost is required'),
            'courier.required' => translate('Please select a carrier'),
            'shipped_by.required' => translate('Please select a shipping medium'),
            'courier.integer' => translate('Please select a carrier'),
            'shipped_by.integer' => translate('Please select a shipping medium')
        ];
    }
}
