<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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

        $rules['name'] = 'required';
        $rules['permalink'] = 'required|unique:tl_com_products,permalink,' . $request->id;
        $rules['discount'] = 'nullable|numeric';
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
            'name.required' => translate('Name is required'),
            'permalink.required' => translate('Permalink is required'),
            'permalink.unique' => translate('Permalink must be unique'),
            'discount.numeric' => translate('Discount amount  must be a number'),
        ];
    }
}
