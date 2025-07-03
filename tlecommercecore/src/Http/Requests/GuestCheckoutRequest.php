<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Session;

class GuestCheckoutRequest extends FormRequest
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
        $rules = [];

        if (getEcommerceSetting('enable_personal_info_guest_checkout') == 1) {
            $rules['name'] = 'required|max:250';
            $rules['email'] = 'required|email|unique:Plugin\TlcommerceCore\Models\Customers,email,' . $request->id;
        }

        if (getEcommerceSetting('create_account_in_guest_checkout') == 1 && getEcommerceSetting('enable_personal_info_guest_checkout') == 1) {
            $rules['password'] = 'required|max:250|confirmed|min:6';
        }

        return $rules;
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
            'password.confirmed' => translate('Password does not match', Session::get('api_locale')),
            'email.required' => translate('Email is required', Session::get('api_locale')),
            'email.email' => translate('Incorrect email', Session::get('api_locale')),
            'email.unique' => translate('Email is already used', Session::get('api_locale')),
        ];
    }
}
