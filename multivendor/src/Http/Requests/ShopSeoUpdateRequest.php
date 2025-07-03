<?php

namespace Plugin\Multivendor\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ShopSeoUpdateRequest extends FormRequest
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
            'meta_title'    => 'nullable|max:220',
            'meta_description'   => 'nullable|max:250',
            'meta_image'   => 'max:11',
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
            'meta_title.max' => translate('Maximum length 220'),
            'meta_description.max' => translate('Maximum length 250'),
            'meta_image.unique'   => translate('Invalid Meta Image'),
            'meta_image.max' => translate('Maximum length 11'),
        ];
    }
}
