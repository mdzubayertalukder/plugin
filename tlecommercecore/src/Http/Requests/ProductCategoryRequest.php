<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryRequest extends FormRequest
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
            'name' => 'required',
            'parent' => 'nullable|exists:tl_com_categories,id',
            'permalink' => 'required|unique:tl_com_categories,permalink,' . request()->id,
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
            'permalink.required' => translate('Permalink is required'),
            'permalink.unique' => translate('Permalink is already exists'),
            'parent.exists' => translate('Selected parent does not exists'),
        ];
    }
}
