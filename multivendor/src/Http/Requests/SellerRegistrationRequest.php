<?php

namespace Plugin\Multivendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class SellerRegistrationRequest extends FormRequest
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
            'email'      => 'required|email|unique:tl_users,email,' . request()->id,
            'name'       => 'required',
            'password'   => 'required|confirmed|min:6',
            'phone'      => 'required',
            'shop_name'  => 'required||unique:tl_com_seller_shop,shop_name',
            'shop_phone' => 'required||unique:tl_com_seller_shop,shop_phone',
            'shop_url'   => 'required'
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
            'email.unique' => translate('Email is already used', Session::get('api_locale')),
            'name.required' => translate('Name i required', Session::get('api_locale')),
            'password.required' => translate('Password is required', Session::get('api_locale')),
            'password.confirmed' => translate('Password does not match', Session::get('api_locale')),
            'password.min' => translate('Minimum length is 6', Session::get('api_locale')),
            'phone.required' => translate('Phone is required', Session::get('api_locale')),
            'shop_name.required' => translate('Shop name is required', Session::get('api_locale')),
            'shop_name.unique' => translate('This name is already taken', Session::get('api_locale')),
            'shop_phone.required' => translate('Shop phone is required', Session::get('api_locale')),
            'shop_phone.unique' => translate('Shop phone is already used', Session::get('api_locale')),
            'shop_url.required' => translate('Shop url is required', Session::get('api_locale')),
        ];
    }
}
