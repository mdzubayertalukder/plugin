<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Session;

class CustomerRequest extends FormRequest
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
            'name' => 'required',
            'password' => 'required|confirmed|min:6',
            'phone' => 'required|unique:Plugin\TlcommerceCore\Models\Customers,phone,' . $request->id,
            'email' => 'required|email|unique:Plugin\TlcommerceCore\Models\Customers,email,' . $request->id
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
            'name.required' => translate('Name is required', Session::get('api_locale')),
            'password.required' => translate('Password is required', Session::get('api_locale')),
            'password.confirmed' => translate('Password does not match', Session::get('api_locale')),
            'password.min' => translate('Password is too short', Session::get('api_locale')),
            'phone.required' => translate('Phone is required', Session::get('api_locale')),
            'phone.unique' => translate('Phone is already used', Session::get('api_locale')),
            'email.required' => translate('Email is required', Session::get('api_locale')),
            'email.email' => translate('Incorrect email', Session::get('api_locale')),
            'email.unique' => translate('Email is already used', Session::get('api_locale')),
        ];
    }
}
