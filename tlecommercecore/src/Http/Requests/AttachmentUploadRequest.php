<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Http\FormRequest;

class AttachmentUploadRequest extends FormRequest
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
            'attachment' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2000'
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
            'attachment.mimes' => translate('Invalid document format', Session::get('api_locale')),
            'attachment.max' => translate('Document size is too large', Session::get('api_locale')),
        ];
    }
}
