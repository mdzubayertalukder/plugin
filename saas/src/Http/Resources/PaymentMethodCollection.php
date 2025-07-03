<?php

namespace Plugin\Saas\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Plugin\Saas\Repositories\PaymentMethodRepository;

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
                ];
            })
        ];
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
        if ($id == config('saas.payment_methods.cod')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'cod_logo');
        }

        if ($id == config('saas.payment_methods.stripe')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'stripe_logo');
        }

        if ($id == config('saas.payment_methods.paypal')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'paypal_logo');
        }

        if ($id == config('saas.payment_methods.paddle')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'paddle_logo');
        }

        if ($id == config('saas.payment_methods.sslcommerz')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'sslcommerz_logo');
        }

        if ($id == config('saas.payment_methods.paystack')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'paystack_logo');
        }

        if ($id == config('saas.payment_methods.razorpay')) {
            $logo = PaymentMethodRepository::configKeyValue($id, 'razorpay_logo');
        }

        return getFilePath($logo, false);
    }
    public function getInstruction($id)
    {
        $instruction = NULL;
        if ($id == config('saas.payment_methods.cod')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'cod_instruction');
        }

        if ($id == config('saas.payment_methods.stripe')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'stripe_instruction');
        }

        if ($id == config('saas.payment_methods.paypal')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'paypal_instruction');
        }

        if ($id == config('saas.payment_methods.paddle')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'paddle_instruction');
        }

        if ($id == config('saas.payment_methods.sslcommerz')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'sslcommerz_instruction');
        }

        if ($id == config('saas.payment_methods.paystack')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'paystack_instruction');
        }

        if ($id == config('saas.payment_methods.razorpay')) {
            $instruction = PaymentMethodRepository::configKeyValue($id, 'razorpay_instruction');
        }
        return $instruction;
    }
}
