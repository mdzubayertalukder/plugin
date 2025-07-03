<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Session;

class CustomerBasicUpdateRequest extends FormRequest
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
            'image' => 'nullable|max:2000|mimes:jpeg,png,jpg'
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
            'phone.required' => translate('Phone is required', Session::get('api_locale')),
            'phone.unique' => translate('Phone is already used', Session::get('api_locale')),
            'image.max' => translate('Image size is too large', Session::get('api_locale')),
            'image.mimes' => translate('Invalid image format', Session::get('api_locale')),
        ];
    }
}
