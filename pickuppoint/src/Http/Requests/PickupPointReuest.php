<?php

namespace Plugin\PickupPoint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupPointReuest extends FormRequest
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
            'name'     => 'required|unique:tl_pick_up_points,name,' . request('id'),
            'phone'    => 'required',
            'location' => 'required',
            'country'  => 'required|exists:tl_countries,id',
            'state'    => 'required|exists:tl_com_state,id',
            'city'     => 'required|exists:tl_com_cities,id'
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
            'name.required'     => translate('Name is required'),
            'phone.required'    => translate('Phone is required'),
            'location.required' => translate('Location is required'),
            'country.required'  => translate('Country is required'),
            'country.exists'    => translate('Invalid country'),
            'state.required'    => translate('State is required'),
            'state.exists'      => translate('Invalid state'),
            'city.required'     => translate('City is required'),
            'city.exists'       => translate('Invalid city'),
        ];
    }
}
