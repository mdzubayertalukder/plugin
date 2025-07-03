<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ShippingZoneTaxRequest extends FormRequest
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
        if ($request->has('tax_type')) {
            if ($request['tax_type'] === 'product_tax') {
                return [
                    'zone_id' => 'required|integer',
                    'collection' => 'required|integer',
                    'tax' => 'required'
                ];
            } else {
                return [
                    'zone_id' => 'required|integer',
                    'state' => 'required|integer',
                    'tax' => 'required'
                ];
            }
        }
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'zone_id.required' => translate('Invalid zone'),
            'tax.required' => translate('Tax is required'),
            'collection.required' => translate('Please select a collection'),
            'collection.integer' => translate('Invalid collection'),
            'state.required' => translate('Please select a state'),
            'state.integer' => translate('Invaid state')
        ];
    }
}
