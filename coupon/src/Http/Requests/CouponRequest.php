<?php

namespace Plugin\Coupon\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'coupon_code'        => 'required|unique:tl_com_coupons,code,' . request()->id,
            'discount_amount'    => 'required|integer',
            'coupon_expire_date' => 'required'
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
            'coupon_code.required' => translate('Coupon code is required'),
            'coupon_code.unique' => translate('Code is already taken'),
            'discount_amount.required' => translate('Discount amount is required'),
            'discount_amount.integer' => translate('Discount amount must be a number'),
            'coupon_expire_date.required' => translate('Please select a expire date'),
        ];
    }
}
