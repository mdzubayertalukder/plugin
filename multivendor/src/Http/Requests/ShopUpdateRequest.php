<?php

namespace Plugin\Multivendor\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ShopUpdateRequest extends FormRequest
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
            'shop_name'    => 'required|max:150',
            'shop_phone'   => 'required|max:100',
            'shop_slug'    => 'required|max:100|unique:tl_com_seller_shop,shop_slug,' . $request->id
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
            'shop_name.required' => translate('Shop name is required'),
            'shop_name.max' => translate('Maximum length 150'),
            'shop_phone.required' => translate('Shop phone is required'),
            'shop_phone.max' => translate('Maximum length 100'),
            'shop_slug.required' => translate('Shop url is required'),
            'shop_slug.unique'   => translate('Shop url is already taken'),
            'shop_slug.max' => translate('Maximum length 100'),
        ];
    }
}
