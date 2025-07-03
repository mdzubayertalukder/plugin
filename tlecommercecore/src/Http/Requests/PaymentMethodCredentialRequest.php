<?php

namespace Plugin\TlcommerceCore\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodCredentialRequest extends FormRequest
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
        if ($request->has('payment_id')) {
            switch ($request['payment_id']) {
                case config('tlecommercecore.payment_methods.stripe'):
                    return [
                        'stripe_public_key' => 'required',
                        'stripe_secret_key' => 'required',
                    ];
                    break;
                default:
                    return [];
            }
        } else {
            return [];
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
            'stripe_public_key.required' => translate('Public key is required'),
            'stripe_private_key.required' => translate('Private key is required'),
        ];
    }
}
