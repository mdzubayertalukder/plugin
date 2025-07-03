<?php

namespace Plugin\Saas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(Request $request)
    {
        $rules = [
            'name' => 'required|unique:tl_saas_packages,name,' . $request->id . '|max:50',
            'is_featured' => 'required',
        ];

        if ($this->input('type') == 'paid') {
            $rules['plans'] = 'required';
        }

        return $rules;
    }

    /**
     * Customizing the error messages (optional).
     */
    public function messages()
    {
        return [
            'name.required' => 'The package name is required.',
            'name.unique' => 'The package name must be unique.',
            'name.max' => 'The package name must not exceed 50 characters.',
            'is_featured.required' => 'The is_featured field is required.',
            'plans.required' => 'The plans field is required for paid packages.',
        ];
    }
}
