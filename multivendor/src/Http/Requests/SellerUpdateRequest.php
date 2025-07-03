<?php

namespace Plugin\Multivendor\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class SellerUpdateRequest extends FormRequest
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
            'name' => 'required|unique:tl_users,name,' . $request->id,
            'email' => 'required|unique:tl_users,name,' . $request->id,
            'phone' => 'required',
            'pro_pic' => 'nullable',
            'password' => 'nullable|confirmed|min:6',
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
            'name.required' => translate('Seller name is required'),
            'name.max' => translate('Maximum length 150'),
            'email.required' => translate('Email is required'),
            'email.unique' => translate('Email is already taken'),
            'password.confirmed' => translate('Password does not match'),
        ];
    }
}
