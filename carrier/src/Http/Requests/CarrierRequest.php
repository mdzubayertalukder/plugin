<?php

namespace Plugin\Carrier\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class CarrierRequest extends FormRequest
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
        if ($request->has('id')) {
            return [
                'name' => 'required',
                'edit_logo' => 'required',
            ];
        } else {
            return [
                'name' => 'required',
                'logo' => 'required',
            ];
        }
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
            'logo.required' => translate('Logo is required'),
            'edit_logo.required' => translate('Logo is required')
        ];
    }
}
