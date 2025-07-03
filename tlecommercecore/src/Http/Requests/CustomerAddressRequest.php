<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Session;

class CustomerAddressRequest extends FormRequest
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

        if (getEcommerceSetting('hide_country_state_city_in_checkout') == config('settings.general_status.active')) {
            return [
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'postal_code' => getEcommerceSetting('post_code_required_in_checkout') == config('settings.general_status.active') ? 'required' : 'nullable'
            ];
        }

        if (getEcommerceSetting('hide_country_state_city_in_checkout') != config('settings.general_status.active')) {
            return [
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'country' => 'required|exists:Plugin\TlcommerceCore\Models\Country,id',
                'state' => 'required|exists:Plugin\TlcommerceCore\Models\States,id',
                'city' => 'required|exists:Plugin\TlcommerceCore\Models\Cities,id',
                'postal_code' => getEcommerceSetting('post_code_required_in_checkout') == config('settings.general_status.active') ? 'required' : 'nullable'
            ];
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
            'name.required' => translate('Name is required', Session::get('api_locale')),
            'phone.required' => translate('Phone is required', Session::get('api_locale')),
            'address.required' => translate('Address is required', Session::get('api_locale')),
            'country.required' => translate('Country is required', Session::get('api_locale')),
            'state.required' => translate('State is required', Session::get('api_locale')),
            'city.required' => translate('City is required', Session::get('api_locale')),
            'country.exists' => translate('Country is invalid', Session::get('api_locale')),
            'state.exists' => translate('State is invalid', Session::get('api_locale')),
            'city.exists' => translate('City is invalid', Session::get('api_locale')),
            'postal.required' => translate('Postal code is required', Session::get('api_locale')),
        ];
    }
}
