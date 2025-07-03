<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;;

use Session;

class CustomerForgotPasswordRequest extends FormRequest
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
            'email' => 'required|email|exists:Plugin\TlcommerceCore\Models\Customers,email'
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
            'email.required' => translate('Email is required', Session::get('api_locale')),
            'email.email' => translate('Incorrect email', Session::get('api_locale')),
            'email.exists' => translate('No account exists with this email', Session::get('api_locale')),
        ];
    }
}
