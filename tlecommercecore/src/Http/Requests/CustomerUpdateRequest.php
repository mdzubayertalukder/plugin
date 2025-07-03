<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CustomerUpdateRequest extends FormRequest
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
            'phone' => 'required|unique:Plugin\TlcommerceCore\Models\Customers,phone,' . $request->id,
            'email' => 'required|unique:Plugin\TlcommerceCore\Models\Customers,email,' . $request->id,
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
            'phone.required' => translate('Phone is required'),
            'phone.unique' => translate('Phone is already used'),
            'email.required' => translate('Email is required'),
            'email.unique' => translate('Email is already used'),
        ];
    }
}
