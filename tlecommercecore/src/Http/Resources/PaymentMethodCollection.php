<?php

namespace Plugin\TlcommerceCore\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Plugin\TlcommerceCore\Repositories\PaymentMethodRepository;

class PaymentMethodCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id'          => (int) $data->id,
                    'name'        => $data->name,
                    'logo'     => $this->getLogo($data->id),
                    'instruction'     => $this->getInstruction($data->id),
                    'additional' => $this->extra_setting($data->id)
                ];
            })
        ];
    }

    public function extra_setting($id)
    {
        if ($id == config('tlecommercecore.payment_methods.bank')) {
            $setting = PaymentMethodRepository::configKeyValue($id, 'enable_bank_transaction_details_form');

            return $setting == config('settings.general_status.active') ? config('settings.general_status.active') : config('settings.general_status.in_active');
        }

        return null;
    }
    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }

    public function getLogo($id)
    {
        $logo = NULL;
        if ($id == config('tlecommercecore.payment_methods.cod')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'cod_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.stripe')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'stripe_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.paypal')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'paypal_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.paddle')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'paddle_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.sslcommerz')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'sslcommerz_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.paystack')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'paystack_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.razorpay')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'razorpay_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.bank')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'bank_logo');
        }

        if ($id == config('tlecommercecore.payment_methods.paymob')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'paymob_logo');
        }
        if ($id == config('tlecommercecore.payment_methods.mercado-pago')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'mc_pago_logo');
        }

        return getFilePath($logo, false);
    }
    public function getInstruction($id)
    {
        $instruction = NULL;
        if ($id == config('tlecommercecore.payment_methods.cod')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'cod_instruction');
        }

        if ($id == config('tlecommercecore.payment_methods.stripe')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'stripe_instruction');
        }

        if ($id == config('tlecommercecore.payment_methods.paypal')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'paypal_instruction');
        }

        if ($id == config('tlecommercecore.payment_methods.paddle')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'paddle_instruction');
        }

        if ($id == config('tlecommercecore.payment_methods.sslcommerz')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'sslcommerz_instruction');
        }

        if ($id == config('tlecommercecore.payment_methods.paystack')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'paystack_instruction');
        }

        if ($id == config('tlecommercecore.payment_methods.razorpay')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'razorpay_instruction');
        }
        if ($id == config('tlecommercecore.payment_methods.mollie')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'mollie_instruction');
        }
        if ($id == config('tlecommercecore.payment_methods.bank')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'bank_instruction');
        }
        if ($id == config('tlecommercecore.payment_methods.paymob')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'paymob_instruction');
        }
        if ($id == config('tlecommercecore.payment_methods.mercado-pago')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'mc_pago_instruction');
        }
        return $instruction;
    }
}
