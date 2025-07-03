<?php

namespace Plugin\Flashdeal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlashDealRequest extends FormRequest
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
            'title' => 'required',
            'permalink' => 'required|unique:tl_com_flash_deal,permalink,' . request()->id,
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
            'title.required' => translate('Deal title is required'),
            'permalink.required' => translate('Permalink is required'),
            'permalink.unique' => translate('Permalink is already taken')
        ];
    }
}
