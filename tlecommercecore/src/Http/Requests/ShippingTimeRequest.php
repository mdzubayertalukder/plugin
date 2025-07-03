<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingTimeRequest extends FormRequest
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
            'minimmum_shipping_time' => 'required',
            'minimmum_shipping_time_unit' => 'required',
            'maximum_shipping_time' => 'required',
            'maximum_shipping_time_unit' => 'required',
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
            'minimmum_shipping_time.required' => translate('Minimum time is required'),
            'minimmum_shipping_time_unit.required' => translate('Minimum time unit is required'),
            'maximum_shipping_time.required' => translate('Maximum time is required'),
            'maximum_shipping_time_unit.required' => translate('Maximum time unit is required'),
        ];
    }
}
